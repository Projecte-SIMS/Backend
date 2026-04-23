<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Models\User;
use App\Services\Billing\TenantBillingAccessService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Str;

class TenantController extends Controller
{
    public function __construct(private readonly TenantBillingAccessService $billingAccessService)
    {
    }

    /**
     * List all tenants with admin info
     */
    public function index()
    {
        $tenants = Tenant::with(['domains', 'ownerProfile'])->get();
        
        return response()->json([
            'success' => true,
            'data' => $tenants->map(function ($tenant) {
                // Get admin user from tenant's database
                $adminInfo = $this->getTenantAdmin($tenant);
                $stats = $this->getTenantStats($tenant);
                
                return [
                    'id' => $tenant->id,
                    'domains' => $tenant->domains->pluck('domain'),
                    'admin_email' => $adminInfo['email'] ?? 'admin@sims.com',
                    'admin_username' => $adminInfo['username'] ?? 'admin',
                    'owner_profile' => $tenant->ownerProfile,
                    'company_theme' => $tenant->company_theme,
                    'company_plan' => $tenant->company_plan,
                    'created_at' => $tenant->created_at,
                    'updated_at' => $tenant->updated_at,
                    'billing' => $this->buildBillingSummary($tenant),
                    'stats' => $stats,
                ];
            }),
        ]);
    }

    private function getTenantStats(Tenant $tenant): array
    {
        $stats = [
            'vehicles_count' => 0,
            'reservations_count' => 0,
            'tickets_count' => 0,
            'users_count' => 0,
        ];

        try {
            $tenant->run(function () use (&$stats) {
                // Consulta directa sin caché para tiempo real
                $stats['vehicles_count'] = \DB::connection('tenant')->table('vehicles')->whereNull('deleted_at')->count();
                $stats['reservations_count'] = \DB::connection('tenant')->table('reservations')->whereNull('deleted_at')->count();
                $stats['tickets_count'] = \DB::connection('tenant')->table('tickets')->whereNull('deleted_at')->count();
                $stats['users_count'] = \DB::connection('tenant')->table('users')->whereNull('deleted_at')->count();
            });
        } catch (\Exception $e) {
            // Probablemente el esquema aún no existe o no tiene las tablas
            \Log::warning("Could not get stats for tenant {$tenant->id}: " . $e->getMessage());
        }

        return $stats;
    }

    /**
     * Get global statistics across all tenants
     */
    public function getGlobalStats()
    {
        $tenants = Tenant::all();
        $globalStats = [
            'total_tenants' => $tenants->count(),
            'total_users' => 0,
            'total_vehicles' => 0,
            'total_reservations' => 0,
            'total_tickets' => 0,
            'total_mrr_cents' => 0,
            'top_tenant_by_vehicles' => null,
            'top_tenant_by_users' => null,
        ];

        $maxVehicles = -1;
        $maxUsers = -1;

        foreach ($tenants as $tenant) {
            $stats = $this->getTenantStats($tenant);
            
            $globalStats['total_users'] += $stats['users_count'];
            $globalStats['total_vehicles'] += $stats['vehicles_count'];
            $globalStats['total_reservations'] += $stats['reservations_count'];
            $globalStats['total_tickets'] += $stats['tickets_count'];
            $globalStats['total_mrr_cents'] += (int) ($tenant->billing_monthly_amount_cents ?? 0);

            if ($stats['vehicles_count'] > $maxVehicles) {
                $maxVehicles = $stats['vehicles_count'];
                $globalStats['top_tenant_by_vehicles'] = [
                    'id' => $tenant->id,
                    'count' => $maxVehicles
                ];
            }

            if ($stats['users_count'] > $maxUsers) {
                $maxUsers = $stats['users_count'];
                $globalStats['top_tenant_by_users'] = [
                    'id' => $tenant->id,
                    'count' => $maxUsers
                ];
            }
        }

        return response()->json([
            'success' => true,
            'data' => $globalStats
        ]);
    }

    /**
     * Get admin user info from tenant database
     */
    private function getTenantAdmin(Tenant $tenant): array
    {
        try {
            $admin = null;
            $tenant->run(function () use (&$admin) {
                $admin = User::role('Admin')->first();
            });
            
            if ($admin) {
                return [
                    'email' => $admin->email,
                    'username' => $admin->username,
                    'name' => $admin->name,
                ];
            }
        } catch (\Exception $e) {
            // Schema might not exist yet
        }
        
        return [
            'email' => 'admin@sims.com',
            'username' => 'admin',
            'name' => 'Administrador',
        ];
    }

    /**
     * Create a new tenant with its domain
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|string|max:50|unique:tenants,id|regex:/^[a-z0-9_-]+$/',
            'domain' => 'required|string|max:255|unique:domains,domain',
        ], [
            'id.regex' => 'El ID solo puede contener letras minúsculas, números, guiones y guiones bajos.',
            'id.unique' => 'Ya existe un tenant con este ID.',
            'domain.unique' => 'Ya existe un dominio registrado con este nombre.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            set_time_limit(0); // Allow longer execution time for migrations/seeding
            \Log::info('Creating tenant', ['id' => $request->id, 'domain' => $request->domain]);
            
            // Clean up: Drop schema if it exists from a previous failed attempt
            $schemaName = 'tenant_' . $request->id;
            try {
                \DB::statement("DROP SCHEMA IF EXISTS \"$schemaName\" CASCADE");
                \Log::info('Dropped existing schema', ['schema' => $schemaName]);
            } catch (\Exception $e) {
                \Log::warning('Could not drop schema', ['schema' => $schemaName, 'error' => $e->getMessage()]);
            }
            
            // Create tenant - creates the schema fresh
            $tenant = Tenant::create(['id' => $request->id]);
            
            \Log::info('Tenant created successfully', ['id' => $tenant->id]);
            
            // Create domain for the tenant
            $tenant->domains()->create(['domain' => $request->domain]);
            
            \Log::info('Domain created', ['domain' => $request->domain, 'tenant_id' => $tenant->id]);
            
            // Run migrations and seeding inside tenant context
            $tenant->run(function () {
                \Log::info('Running migrations for tenant', ['tenant_id' => tenant('id')]);
                // Run migrations using Artisan within tenant context, specifying tenant migrations path
                \Artisan::call('migrate', [
                    '--path' => 'database/migrations/tenant',
                    '--force' => true,
                    '--database' => 'tenant',
                    '--quiet' => true,
                ]);
                \Log::info('Migrations completed');
                
                \Log::info('Running manual seeding for tenant', ['tenant_id' => tenant('id')]);
                try {
                    $seederPath = database_path('seeders');
                    $password = \Hash::make('password');
                    
                    // 1. Permissions
                    require_once $seederPath . '/PermissionsSeeder.php';
                    (new \Database\Seeders\PermissionsSeeder())->run();
                    \Log::info('Permissions seeded');
                    
                    // 2. Roles
                    require_once $seederPath . '/RolesSeeder.php';
                    (new \Database\Seeders\RolesSeeder())->run();
                    \Log::info('Roles seeded');
                    
                    // 3. Users
                    $admin = \App\Models\User::firstOrCreate(
                        ['email' => 'admin@sims.com'],
                        [
                            'name' => 'Administrador',
                            'username' => 'admin',
                            'password' => $password,
                            'active' => true,
                        ]
                    );
                    $admin->assignRole('Admin');
                    
                    $client = \App\Models\User::firstOrCreate(
                        ['email' => 'client@sims.com'],
                        [
                            'name' => 'Cliente Demo',
                            'username' => 'client',
                            'password' => $password,
                            'active' => true,
                        ]
                    );
                    $client->assignRole('Client');
                    
                    $maintenance = \App\Models\User::firstOrCreate(
                        ['email' => 'maint@sims.com'],
                        [
                            'name' => 'Técnico Mantenimiento',
                            'username' => 'maintenance',
                            'password' => $password,
                            'active' => true,
                        ]
                    );
                    $maintenance->assignRole('Maintenance');
                    \Log::info('Default users seeded');
                    
                    // 4. Test Data
                    if (file_exists($seederPath . '/TestDataSeeder.php')) {
                        require_once $seederPath . '/TestDataSeeder.php';
                        (new \Database\Seeders\TestDataSeeder())->run();
                        \Log::info('Test data seeded');
                    }

                    // 5. MongoDB Locations
                    if (file_exists($seederPath . '/MongoVehicleLocationsSeeder.php')) {
                        require_once $seederPath . '/MongoVehicleLocationsSeeder.php';
                        (new \Database\Seeders\MongoVehicleLocationsSeeder())->run();
                        \Log::info('MongoDB locations seeded');
                    }
                    
                } catch (\Exception $e) {
                    \Log::warning('Seeding warning (non-critical): ' . $e->getMessage());
                }
                \Log::info('Seeding completed');
            });
            
            // Verify tables were created by checking if users table exists
            $tablesCreated = false;
            $tableList = [];
            $usersCount = 0;
            
            $tenant->run(function () use (&$tablesCreated, &$tableList, &$usersCount) {
                try {
                    $schemaName = 'tenant_' . tenant('id');
                    $tables = \DB::select(
                        "SELECT table_name FROM information_schema.tables WHERE table_schema = ?", 
                        [$schemaName]
                    );
                    $tableList = array_map(fn($t) => $t->table_name, $tables);
                    $tablesCreated = in_array('users', $tableList);
                    
                    if ($tablesCreated) {
                        $usersCount = \App\Models\User::count();
                    }
                    
                    \Log::info('Tenant tables verified', [
                        'tables' => $tableList,
                        'users_count' => $usersCount,
                    ]);
                } catch (\Exception $e) {
                    \Log::error('Error verifying tenant tables', [
                        'error' => $e->getMessage(),
                    ]);
                }
            });

            return response()->json([
                'success' => true,
                'message' => 'Tenant creado exitosamente',
                'data' => [
                    'id' => $tenant->id,
                    'domain' => $request->domain,
                    'admin_email' => 'admin@sims.com',
                    'admin_password' => 'password',
                    'tables_created' => $tablesCreated,
                    'tables' => $tableList,
                    'users_count' => $usersCount,
                ],
            ], 201);
        } catch (\Exception $e) {
            \Log::error('Tenant creation failed', [
                'id' => $request->id,
                'domain' => $request->domain,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error al crear el tenant',
                'error' => $e->getMessage(),
                'trace' => config('app.debug') ? $e->getTraceAsString() : null,
            ], 500);
        }
    }

    /**
     * Get a specific tenant
     */
    public function show(string $id)
    {
        $tenant = Tenant::with(['domains', 'ownerProfile'])->find($id);

        if (!$tenant) {
            return response()->json([
                'success' => false,
                'message' => 'Tenant no encontrado',
            ], 404);
        }

        $adminInfo = $this->getTenantAdmin($tenant);
        $stats = $this->getTenantStats($tenant);

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $tenant->id,
                'domains' => $tenant->domains->pluck('domain'),
                'admin_email' => $adminInfo['email'],
                'admin_username' => $adminInfo['username'],
                'admin_name' => $adminInfo['name'],
                'owner_profile' => $tenant->ownerProfile,
                'company_plan' => $tenant->company_plan,
                'company_theme' => $tenant->company_theme,
                'created_at' => $tenant->created_at,
                'updated_at' => $tenant->updated_at,
                'billing' => $this->buildBillingSummary($tenant),
                'stats' => $stats,
            ],
        ]);
    }

    /**
     * Update tenant theme
     */
    public function updateTheme(Request $request, string $id)
    {
        $tenant = Tenant::find($id);

        if (!$tenant) {
            return response()->json([
                'success' => false,
                'message' => 'Tenant no encontrado',
            ], 404);
        }

        $request->validate([
            'theme' => 'required|string|max:30',
        ]);

        $tenant->company_theme = $request->theme;
        $tenant->save();

        return response()->json([
            'success' => true,
            'message' => 'Tema del inquilino actualizado',
            'data' => [
                'id' => $tenant->id,
                'theme' => $tenant->company_theme,
            ],
        ]);
    }

    private function buildBillingSummary(Tenant $tenant): array
    {
        $monthlyCents = (int) ($tenant->billing_monthly_amount_cents ?? 0);
        $profileAttribute = $tenant->getAttribute('billing_demo_profile');
        $demoProfile = is_array($profileAttribute) ? $profileAttribute : null;

        return [
            'provider' => $tenant->billing_provider,
            'status' => $tenant->billing_status ?? 'inactive',
            'customer_id' => $tenant->billing_customer_id,
            'subscription_id' => $tenant->billing_subscription_id,
            'price_id' => $tenant->billing_price_id,
            'currency' => strtoupper((string) ($tenant->billing_currency ?? 'EUR')),
            'monthly_amount_cents' => $monthlyCents,
            'mrr_amount_cents' => $monthlyCents,
            'arr_amount_cents' => $monthlyCents * 12,
            'current_period_end' => $tenant->billing_current_period_end,
            'last_invoice_at' => $tenant->billing_last_invoice_at,
            'last_invoice_status' => $tenant->billing_last_invoice_status,
            'demo_profile' => $demoProfile,
            'access' => $this->billingAccessService->suspensionSnapshot($tenant),
        ];
    }

    /**
     * Reset admin password for a tenant
     */
    public function resetAdminPassword(Request $request, string $id)
    {
        $tenant = Tenant::find($id);

        if (!$tenant) {
            return response()->json([
                'success' => false,
                'message' => 'Tenant no encontrado',
            ], 404);
        }

        // Generate new password or use provided one
        $newPassword = $request->input('password', Str::random(12));

        try {
            $tenant->run(function () use ($newPassword) {
                $admin = User::role('Admin')->first();
                if ($admin) {
                    $admin->password = Hash::make($newPassword);
                    $admin->save();
                }
            });

            return response()->json([
                'success' => true,
                'message' => 'Contraseña del admin actualizada',
                'data' => [
                    'tenant_id' => $tenant->id,
                    'new_password' => $newPassword,
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al resetear contraseña',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Add a new domain to an existing tenant
     */
    public function addDomain(Request $request, string $id)
    {
        $tenant = Tenant::find($id);

        if (!$tenant) {
            return response()->json([
                'success' => false,
                'message' => 'Tenant no encontrado',
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'domain' => 'required|string|max:255|unique:domains,domain',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $validator->errors(),
            ], 422);
        }

        $tenant->domains()->create(['domain' => $request->domain]);

        return response()->json([
            'success' => true,
            'message' => 'Dominio añadido exitosamente',
            'data' => [
                'id' => $tenant->id,
                'domains' => $tenant->domains()->pluck('domain'),
            ],
        ]);
    }

    /**
     * Verify tenant status - check if schema exists and is accessible
     */
    public function verify(string $id)
    {
        $tenant = Tenant::find($id);

        if (!$tenant) {
            return response()->json([
                'success' => false,
                'message' => 'Tenant no encontrado',
            ], 404);
        }

        try {
            $tenancy = app(\Stancl\Tenancy\Tenancy::class);
            $tenancy->initialize($tenant);

            // Check if schema exists
            $schemaName = 'tenant_' . $tenant->id;
            $schemaExists = \DB::selectOne(
                "SELECT 1 FROM information_schema.schemata WHERE schema_name = ?",
                [$schemaName]
            );

            // Try to query tables in the schema
            $tables = [];
            $tableCount = 0;
            if ($schemaExists) {
                $tables = \DB::select(
                    "SELECT table_name FROM information_schema.tables WHERE table_schema = ?",
                    [$schemaName]
                );
                $tableCount = count($tables);
            }

            $usersCount = 0;
            if ($tableCount > 0) {
                try {
                    $usersCount = \DB::connection('tenant')
                        ->table('users')
                        ->count();
                } catch (\Exception $e) {
                    // Ignore error
                }
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'tenant_id' => $tenant->id,
                    'schema_name' => $schemaName,
                    'schema_exists' => (bool) $schemaExists,
                    'table_count' => $tableCount,
                    'users_count' => $usersCount,
                    'tables' => collect($tables)->pluck('table_name')->toArray(),
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error verificando tenant',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Delete a tenant and its database
     */
    public function destroy(string $id)
    {
        $tenant = Tenant::find($id);

        if (!$tenant) {
            return response()->json([
                'success' => true,
                'message' => 'Tenant already deleted',
            ]);
        }

        try {
            \Log::info('Deleting tenant', ['id' => $id]);
            
            // 1. Explicitly drop schema first with better error handling
            $schemaName = 'tenant_' . $id;
            try {
                // Try to set short timeout to not hang the request
                \DB::statement("SET lock_timeout = '2s'");
                \DB::statement("DROP SCHEMA IF EXISTS \"$schemaName\" CASCADE");
                \Log::info('Dropped schema', ['schema' => $schemaName]);
            } catch (\Exception $e) {
                // Just log, don't fail the whole request
                \Log::warning('Could not drop schema during destroy', ['schema' => $schemaName, 'error' => $e->getMessage()]);
            }
            
            // 2. Delete tenant record from central database
            // Note: tenancy package might also try to do cleanup, we wrap in try
            try {
                $tenant->delete();
            } catch (\Exception $e) {
                \Log::error('Error deleting tenant record', ['id' => $id, 'error' => $e->getMessage()]);
                // If it fails because of database constraints but we already dropped schema, 
                // we might need to force delete or handle specifically
            }
            
            return response()->json([
                'success' => true,
                'message' => 'Tenant y datos eliminados correctamente',
            ]);
        } catch (\Exception $e) {
            \Log::error('Critical error deleting tenant', ['id' => $id, 'error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Error crítico al eliminar el tenant',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
