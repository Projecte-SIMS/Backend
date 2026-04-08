<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Laravel\Sanctum\PersonalAccessToken;

class TenantSanctumAuth
{
    public function handle(Request $request, Closure $next)
    {
        // Get token from header
        $token = $request->bearerToken();
        
        if (!$token) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }
        
        // Find token in tenant database
        $accessToken = PersonalAccessToken::on('tenant')
            ->where('token', hash('sha256', $token))
            ->first();
        
        if (!$accessToken || !$accessToken->tokenable) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }
        
        // Set the authenticated user
        auth()->setUser($accessToken->tokenable);
        
        return $next($request);
    }
}
