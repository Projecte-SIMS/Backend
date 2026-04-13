<?php

namespace App\Http\Middleware;

use App\Models\Tenant;
use App\Services\Billing\TenantBillingAccessService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureTenantBillingIsActive
{
    public function __construct(private readonly TenantBillingAccessService $billingAccessService)
    {
    }

    public function handle(Request $request, Closure $next): Response
    {
        if (!tenancy()->initialized) {
            return $next($request);
        }

        $tenant = tenancy()->tenant;
        if (!$tenant instanceof Tenant) {
            return $next($request);
        }

        $snapshot = $this->billingAccessService->suspensionSnapshot($tenant);
        if (!$snapshot['is_suspended']) {
            return $next($request);
        }

        return response()->json([
            'success' => false,
            'message' => $snapshot['reason'],
            'error_code' => 'TENANT_SUSPENDED_FOR_NON_PAYMENT',
            'data' => [
                'tenant_id' => $tenant->id,
                'billing_access' => $snapshot,
            ],
        ], 423);
    }
}

