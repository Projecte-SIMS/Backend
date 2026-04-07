<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CentralAdminAuth
{
    /**
     * Handle an incoming request.
     * Validates central admin token from cache
     */
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->bearerToken();

        if (!$token) {
            return response()->json([
                'success' => false,
                'message' => 'Token de autenticación requerido',
            ], 401);
        }

        // Check if token exists in cache
        if (!cache()->has('central_token:' . $token)) {
            return response()->json([
                'success' => false,
                'message' => 'Token inválido o expirado',
            ], 401);
        }

        return $next($request);
    }
}
