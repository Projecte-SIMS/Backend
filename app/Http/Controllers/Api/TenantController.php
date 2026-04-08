<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
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
            // Create tenant (this triggers database creation)
            $tenant = Tenant::create(['id' => $request->id]);
            
            // Force run migrations manually
            $tenant->run(function () {
                \Artisan::call('migrate', [
                    '--force' => true,
                    '--path' => 'database/migrations/tenant',
                ]);
                
                // Create admin user directly
                $password = Hash::make('password');
                
                $admin = User::firstOrCreate(
                    ['email' => 'admin@sims.com'],
                    [
                        'name' => 'Administrador',
                        'username' => 'admin',
                        'password' => $password,
                        'active' => true,
                    ]
                );
                
                $client = User::firstOrCreate(
                    ['email' => 'client@sims.com'],
                    [
                        'name' => 'Cliente Demo',
                        'username' => 'client',
                        'password' => $password,
                        'active' => true,
                    ]
                );
                
                $maint = User::firstOrCreate(
                    ['email' => 'maint@sims.com'],
                    [
                        'name' => 'Técnico Mantenimiento',
                        'username' => 'maintenance',
                        'password' => $password,
                        'active' => true,
                    ]
                );
            });
            
            // Create domain for the tenant
            $tenant->domains()->create(['domain' => $request->domain]);

            return response()->json([
                'success' => true,
                'message' => 'Tenant creado exitosamente',
                'data' => [
                    'id' => $tenant->id,
                    'domain' => $request->domain,
                    'admin_email' => 'admin@sims.com',
                    'admin_password' => 'password',
                ],
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al crear el tenant',
                'error' => $e->getMessage(),
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
            $tenant->delete();

            return response()->json([
                'success' => true,
                'message' => 'Tenant eliminado exitosamente',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar el tenant',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
