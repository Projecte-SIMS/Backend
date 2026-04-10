<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Str;

class TenantController extends Controller
{
    /**
     * List all tenants with admin info
     */
    public function index()
    {
        $tenants = Tenant::with('domains')->get();
        
        return response()->json([
            'success' => true,
            'data' => $tenants->map(function ($tenant) {
                // Get admin user from tenant's database
                $adminInfo = $this->getTenantAdmin($tenant);
                
                return [
                    'id' => $tenant->id,
                    'domains' => $tenant->domains->pluck('domain'),
                    'admin_email' => $adminInfo['email'] ?? 'admin@sims.com',
                    'admin_username' => $adminInfo['username'] ?? 'admin',
                    'created_at' => $tenant->created_at,
                    'updated_at' => $tenant->updated_at,
                ];
            }),
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
                
                \Log::info('Running seeder for tenant', ['tenant_id' => tenant('id')]);
                try {
                    // Load seeder file directly to avoid autoloader issues
                    $seederPath = database_path('seeders/Tenant/TenantDatabaseSeeder.php');
                    if (file_exists($seederPath)) {
                        require_once $seederPath;
                        $seeder = new \Database\Seeders\Tenant\TenantDatabaseSeeder();
                        $seeder->run();
                    } else {
                        \Log::warning('Seeder file not found at: ' . $seederPath);
                    }
                } catch (\Exception $e) {
                    \Log::error('Seeder error: ' . $e->getMessage());
                    throw $e;
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
                        'trace' => $e->getTraceAsString(),
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
        $tenant = Tenant::with('domains')->find($id);

        if (!$tenant) {
            return response()->json([
                'success' => false,
                'message' => 'Tenant no encontrado',
            ], 404);
        }

        $adminInfo = $this->getTenantAdmin($tenant);

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $tenant->id,
                'domains' => $tenant->domains->pluck('domain'),
                'admin_email' => $adminInfo['email'],
                'admin_username' => $adminInfo['username'],
                'admin_name' => $adminInfo['name'],
                'created_at' => $tenant->created_at,
                'updated_at' => $tenant->updated_at,
            ],
        ]);
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
                'success' => false,
                'message' => 'Tenant no encontrado',
            ], 404);
        }

        try {
            \Log::info('Deleting tenant', ['id' => $id]);
            
            // Explicitly drop schema first
            $schemaName = 'tenant_' . $id;
            try {
                \DB::statement("DROP SCHEMA IF EXISTS \"$schemaName\" CASCADE");
                \Log::info('Dropped schema', ['schema' => $schemaName]);
            } catch (\Exception $e) {
                \Log::warning('Could not drop schema', ['schema' => $schemaName, 'error' => $e->getMessage()]);
            }
            
            // Delete tenant record from central database
            $tenant->delete();
            
            \Log::info('Tenant deleted successfully', ['id' => $id]);

            return response()->json([
                'success' => true,
                'message' => 'Tenant eliminado exitosamente',
            ]);
        } catch (\Exception $e) {
            \Log::error('Error deleting tenant', ['id' => $id, 'error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar el tenant',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
