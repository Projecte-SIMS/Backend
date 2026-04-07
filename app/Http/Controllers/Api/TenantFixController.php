<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use Illuminate\Http\Request;

class TenantFixController extends Controller
{
    /**
     * Reset and recreate a tenant (DEBUG ONLY)
     */
    public function reset(Request $request)
    {
        $tenantId = $request->input('tenant_id');
        
        if (!$tenantId) {
            return response()->json([
                'success' => false,
                'message' => 'tenant_id is required',
            ], 400);
        }

        try {
            $tenant = Tenant::find($tenantId);

            if (!$tenant) {
                return response()->json([
                    'success' => false,
                    'message' => "Tenant '$tenantId' not found",
                ], 404);
            }

            // Delete tenant (this will delete its schema)
            $tenant->delete();

            // Recreate it
            $newTenant = Tenant::create(['id' => $tenantId]);

            return response()->json([
                'success' => true,
                'message' => "Tenant '$tenantId' has been reset and recreated",
                'tenant' => [
                    'id' => $newTenant->id,
                    'created_at' => $newTenant->created_at,
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Recreate all tenants
     */
    public function resetAll()
    {
        try {
            $tenants = Tenant::all()->pluck('id')->toArray();

            foreach ($tenants as $tenantId) {
                $tenant = Tenant::find($tenantId);
                if ($tenant) {
                    $tenant->delete();
                }
            }

            // Recreate them
            foreach ($tenants as $tenantId) {
                Tenant::create(['id' => $tenantId]);
            }

            return response()->json([
                'success' => true,
                'message' => 'All tenants have been reset',
                'tenant_ids' => $tenants,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
            ], 500);
        }
    }
}
