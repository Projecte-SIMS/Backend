<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'role' => \Spatie\Permission\Middleware\RoleMiddleware::class,
            'admin' => \App\Http\Middleware\EnsureUserIsAdmin::class,
            'central.admin' => \App\Http\Middleware\CentralAdminAuth::class,
            'tenant.init' => \App\Http\Middleware\InitializeTenancyByRequestData::class,
            'tenant.billing.active' => \App\Http\Middleware\EnsureTenantBillingIsActive::class,
            'tenant.auth' => \App\Http\Middleware\TenantSanctumAuth::class,
        ]);
        
        // Trust proxies for Render/Vercel
        $middleware->trustProxies(at: '*');
        
        // CORS middleware must be first to process preflight requests and add headers to all responses
        $middleware->prepend(\App\Http\Middleware\HandleCors::class);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
