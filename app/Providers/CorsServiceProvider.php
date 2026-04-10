<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class CorsServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(\Fruitcake\Cors\CorsService::class, function ($app) {
            return new \Fruitcake\Cors\CorsService(config('cors'));
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
