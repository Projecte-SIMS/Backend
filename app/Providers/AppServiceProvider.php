<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Laravel\Sanctum\Sanctum;
use App\Models\PersonalAccessToken;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Use custom PersonalAccessToken model for multi-tenancy
        Sanctum::usePersonalAccessTokenModel(PersonalAccessToken::class);
        
        $this->configureRateLimiting();
    }

    /**
     * Configure the rate limiters for the application.
     */
    protected function configureRateLimiting(): void
    {
        // Rate limiter for login: 5 attempts per minute per IP
        RateLimiter::for('login', function (Request $request) {
            return Limit::perMinute(5)
                ->by($request->ip())
                ->response(function (Request $request, array $headers) {
                    return response()->json([
                        'message' => 'Demasiados intentos de inicio de sesión. Por favor, espera un minuto.',
                        'retry_after' => $headers['Retry-After'] ?? 60,
                    ], 429, $headers);
                });
        });

        // Rate limiter for API: 60 requests per minute per user
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });

        // Rate limiter for chatbot: 50 requests per minute per user
        RateLimiter::for('chatbot', function (Request $request) {
            return Limit::perMinute(50)
                ->by($request->user()?->id ?: $request->ip())
                ->response(function (Request $request, array $headers) {
                    return response()->json([
                        'message' => 'Has alcanzado el límite de consultas al asistente. Espera un momento.',
                        'retry_after' => $headers['Retry-After'] ?? 60,
                    ], 429, $headers);
                });
        });

        // Rate limiter for user registration: 3 per hour per IP
        RateLimiter::for('register', function (Request $request) {
            return Limit::perHour(3)
                ->by($request->ip())
                ->response(function (Request $request, array $headers) {
                    return response()->json([
                        'message' => 'Demasiados intentos de registro. Por favor, inténtalo más tarde.',
                        'retry_after' => $headers['Retry-After'] ?? 3600,
                    ], 429, $headers);
                });
        });

        // Rate limiter for tenant onboarding: 2 per hour per IP
        RateLimiter::for('onboarding', function (Request $request) {
            return Limit::perHour(2)
                ->by($request->ip())
                ->response(function (Request $request, array $headers) {
                    return response()->json([
                        'message' => 'Límite de creación de empresas alcanzado. Por favor, espera.',
                        'retry_after' => $headers['Retry-After'] ?? 3600,
                    ], 429, $headers);
                });
        });
    }
}
