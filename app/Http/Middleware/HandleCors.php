<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class HandleCors
{
    public function handle(Request $request, Closure $next)
    {
        $origin = $request->header('Origin') ?: '*';

        // Manejo inmediato de peticiones OPTIONS (Preflight)
        if ($request->isMethod('OPTIONS')) {
            return response('', 200)
                ->header('Access-Control-Allow-Origin', $origin)
                ->header('Access-Control-Allow-Methods', 'POST, GET, OPTIONS, PUT, DELETE, PATCH')
                ->header('Access-Control-Allow-Credentials', 'true')
                ->header('Access-Control-Max-Age', '86400')
                ->header('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With, X-Tenant, Accept, Origin, Set-Cookie');
        }

        try {
            $response = $next($request);
        } catch (\Throwable $e) {
            // Si hay un error interno, capturamos la respuesta de error de Laravel
            // y le inyectamos las cabeceras CORS para poder ver el error en la consola
            $response = response()->json([
                'message' => 'Internal Server Error',
                'error' => $e->getMessage()
            ], 500);
        }

        // Asegurar que la respuesta sea un objeto de respuesta de Symfony/Laravel
        if (!$response instanceof Response) {
            $response = response($response);
        }

        // Inyectar cabeceras en todas las respuestas (éxito o error)
        $response->headers->set('Access-Control-Allow-Origin', $origin);
        $response->headers->set('Access-Control-Allow-Methods', 'POST, GET, OPTIONS, PUT, DELETE, PATCH');
        $response->headers->set('Access-Control-Allow-Credentials', 'true');
        $response->headers->set('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With, X-Tenant, Accept, Origin, Set-Cookie');

        return $response;
    }
}
