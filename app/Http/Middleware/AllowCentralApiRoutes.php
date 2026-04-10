<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AllowCentralApiRoutes
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Allow central API routes from any domain
        if ($request->is('api/health', 'api/central/*')) {
            // Skip tenancy middleware for these routes by not initializing tenancy
            return $next($request);
        }

        return $next($request);
    }
}
