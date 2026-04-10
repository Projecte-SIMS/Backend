<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class PreventAccessFromCentralDomainsExceptApi
{
    /**
     * Allow API routes from central domains.
     */
    public function handle(Request $request, Closure $next)
    {
        if (in_array($request->getHost(), config('tenancy.central_domains'))) {
            // Allow API routes from central domains
            if ($request->is('api/*') || $request->is('sanctum/*')) {
                return $next($request);
            }

            // For non-API routes from central domains, prevent access
            abort(404);
        }

        return $next($request);
    }
}
