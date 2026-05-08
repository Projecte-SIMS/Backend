<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Laravel\Sanctum\PersonalAccessToken;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class TenantSanctumAuth
{
    public function handle(Request $request, Closure $next)
    {
        // Get token from header
        $token = $request->bearerToken();
        
        if (!$token) {
            return response()->json(['message' => 'No autenticado.'], 401);
        }
        
        if (app()->environment('testing') && !tenancy()->initialized) {
            $accessToken = PersonalAccessToken::findToken($token);
            if (!$accessToken || !$accessToken->tokenable) {
                return response()->json(['message' => 'No autenticado.'], 401);
            }

            $user = $accessToken->tokenable;
            if (method_exists($user, 'withAccessToken')) {
                $user->withAccessToken($accessToken);
            }

            auth()->setUser($user);
            return $next($request);
        }

        // Ensure tenant is initialized (should be from tenant.init middleware)
        if (!tenancy()->initialized) {
            return response()->json(['message' => 'Tenant no inicializado.'], 400);
        }
        
        // Sanctum tokens are in format: ID|HASH
        // We need to extract the HASH part and hash it to compare with DB
        if (strpos($token, '|') === false) {
            return response()->json(['message' => 'Formato de token no válido.'], 401);
        }
        
        [$id, $hash] = explode('|', $token, 2);
        $hashedToken = hash('sha256', $hash);
        
        // Find token in tenant database using raw query
        try {
            $accessToken = DB::connection('tenant')
                ->table('personal_access_tokens')
                ->where('id', $id)
                ->where('token', $hashedToken)
                ->first();
            
            if (!$accessToken) {
                return response()->json(['message' => 'No autenticado.'], 401);
            }
            
            // Load the user from the token
            $user = DB::connection('tenant')
                ->table('users')
                ->where('id', $accessToken->tokenable_id)
                ->first();
            
            if (!$user) {
                return response()->json(['message' => 'No autenticado.'], 401);
            }
            
            // Convert to User model
            $userModel = \App\Models\User::hydrate([$user])->first();
            if (!$userModel) {
                return response()->json(['message' => 'No autenticado.'], 401);
            }
            
            // Set the authenticated user
            auth()->setUser($userModel);
            
            return $next($request);
        } catch (\PDOException $e) {
            // Database connection error - likely schema doesn't exist
            Log::error('TenantSanctumAuth - Database error', [
                'error' => $e->getMessage(),
                'tenant' => tenancy()->tenant->id ?? 'unknown',
            ]);
            return response()->json([
                'message' => 'Error en la base de datos del tenant. Es posible que el esquema no exista.',
                'detail' => $e->getMessage()
            ], 500);
        } catch (\Exception $e) {
            Log::error('TenantSanctumAuth error', [
                'error' => $e->getMessage(),
                'tenant' => tenancy()->tenant->id ?? 'unknown',
            ]);
            return response()->json([
                'message' => 'Error de autenticación',
                'detail' => $e->getMessage()
            ], 500);
        }
    }
}
