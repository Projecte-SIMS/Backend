<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Fruitcake\Cors\CorsService;

class HandleCors
{
    protected $cors;

    public function __construct(CorsService $cors)
    {
        $this->cors = $cors;
    }

    public function handle(Request $request, Closure $next)
    {
        if ($this->cors->isCorsRequest($request)) {
            if ($this->cors->isPreflightRequest($request)) {
                return $this->handlePreflightRequest($request);
            }
        }

        $response = $next($request);

        if ($this->cors->isCorsRequest($request)) {
            return $this->cors->addActualRequestHeaders($response, $request);
        }

        return $response;
    }

    protected function handlePreflightRequest(Request $request)
    {
        return $this->cors->handlePreflightRequest(request());
    }
}
