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
        $allowedHeaders = config('cors.allowed_headers', ['*']);

        // Handle preflight requests
        if ($request->method() === 'OPTIONS') {
            $response = new Response('', 200);
            
            if (in_array($origin, $allowedOrigins) || in_array('*', $allowedOrigins)) {
                $response->header('Access-Control-Allow-Origin', $origin ?: '*');
                $response->header('Access-Control-Allow-Credentials', 'true');
                $response->header('Access-Control-Allow-Methods', implode(', ', config('cors.allowed_methods', ['*'])));
                
                // Handle allowed headers - if '*', echo back the requested headers
                if (in_array('*', $allowedHeaders)) {
                    $requestHeaders = $request->header('Access-Control-Request-Headers');
                    if ($requestHeaders) {
                        $response->header('Access-Control-Allow-Headers', $requestHeaders);
                    } else {
                        // Fallback to common headers
                        $response->header('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Tenant, X-Requested-With');
                    }
                } else {
                    $response->header('Access-Control-Allow-Headers', implode(', ', $allowedHeaders));
                }
                
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
