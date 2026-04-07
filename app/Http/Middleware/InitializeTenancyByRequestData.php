<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Stancl\Tenancy\Tenancy;
use App\Models\Tenant;

class InitializeTenancyByRequestData
{
    protected Tenancy $tenancy;

    public function __construct(Tenancy $tenancy)
    {
        $this->tenancy = $tenancy;
    }

    /**
     * Initialize tenancy by header (X-Tenant) or query parameter (?tenant=)
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Try to get tenant from header first, then query param
        $tenantId = $request->header('X-Tenant') ?? $request->query('tenant');

        if (!$tenantId) {
            return response()->json([
                'success' => false,
                'message' => 'Tenant no especificado. Usa header X-Tenant o query param ?tenant=',
            ], 400);
        }

        $tenant = Tenant::find($tenantId);

        if (!$tenant) {
            return response()->json([
                'success' => false,
                'message' => "Tenant '{$tenantId}' no encontrado",
            ], 404);
        }

        $this->tenancy->initialize($tenant);

        return $next($request);
    }
}
