<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class HandleCors
{
    public function handle(Request $request, Closure $next)
    {
        $allowedOrigins = config('cors.allowed_origins', []);
        $origin = $request->header('Origin');

        // Handle preflight requests
        if ($request->method() === 'OPTIONS') {
            $response = new Response('', 200);
            
            if (in_array($origin, $allowedOrigins) || in_array('*', $allowedOrigins)) {
                $response->header('Access-Control-Allow-Origin', $origin ?: '*');
                $response->header('Access-Control-Allow-Credentials', 'true');
                $response->header('Access-Control-Allow-Methods', implode(', ', config('cors.allowed_methods', ['*'])));
                $response->header('Access-Control-Allow-Headers', implode(', ', config('cors.allowed_headers', ['*'])));
                $response->header('Access-Control-Max-Age', config('cors.max_age', 0));
            }
            
            return $response;
        }

        // Process the actual request
        $response = $next($request);

        // Add CORS headers to actual responses
        if (in_array($origin, $allowedOrigins) || in_array('*', $allowedOrigins)) {
            $response->header('Access-Control-Allow-Origin', $origin ?: '*');
            $response->header('Access-Control-Allow-Credentials', 'true');
            $response->header('Vary', 'Origin');
        }

        return $response;
    }
}
