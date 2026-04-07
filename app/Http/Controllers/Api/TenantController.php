<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class TenantController extends Controller
{
    /**
     * List all tenants
     */
    public function index()
    {
        $tenants = Tenant::with('domains')->get();
        
        return response()->json([
            'success' => true,
            'data' => $tenants->map(function ($tenant) {
                return [
                    'id' => $tenant->id,
                    'domains' => $tenant->domains->pluck('domain'),
                    'created_at' => $tenant->created_at,
                    'updated_at' => $tenant->updated_at,
                ];
            }),
        ]);
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
            // Create tenant (this triggers database creation, migrations and seeding)
            $tenant = Tenant::create(['id' => $request->id]);
            
            // Create domain for the tenant
            $tenant->domains()->create(['domain' => $request->domain]);

            return response()->json([
                'success' => true,
                'message' => 'Tenant creado exitosamente',
                'data' => [
                    'id' => $tenant->id,
                    'domain' => $request->domain,
                    'database' => 'tenant' . $tenant->id,
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

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $tenant->id,
                'domains' => $tenant->domains->pluck('domain'),
                'created_at' => $tenant->created_at,
                'updated_at' => $tenant->updated_at,
            ],
        ]);
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
