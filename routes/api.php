<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\TenantController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BillingController;
use App\Http\Controllers\Api\PublicTenantOnboardingController;
use App\Http\Controllers\Api\CentralSettingsController;

/*
|--------------------------------------------------------------------------
| Central API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your central application.
| These routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group.
|
| These routes are only accessible from central domains (localhost, 127.0.0.1, sims-backend-api-0b2w.onrender.com)
|
*/

// Health check endpoint (available on all domains)
Route::get('/health', function () {
    return response()->json([
        'status' => 'ok',
        'type' => 'central',
        'message' => 'SIMS Central API - Use tenant domains to access tenant APIs',
    ]);
});

// Public tenant info (for theme and identification)
Route::get('/public/tenants/{id}/settings', [PublicTenantOnboardingController::class, 'getPublicSettings']);
Route::get('/public/settings', [CentralSettingsController::class, 'index']);

// Central authentication for super admin (no domain restriction)
Route::post('/central/login', [AuthController::class, 'centralLogin']);
Route::post('/central/billing/webhook/stripe', [BillingController::class, 'stripeWebhook']);
Route::post('/public/tenant-onboarding/demo-complete', [PublicTenantOnboardingController::class, 'demoComplete'])
    ->middleware('throttle:onboarding');

use App\Http\Controllers\Api\FleetManagementController;

// Tenant management routes (protected with central admin middleware)
Route::middleware('central.admin')->prefix('tenants')->group(function () {
    Route::get('/stats/global', [TenantController::class, 'getGlobalStats']);
    
    // IoT Fleet Management
    Route::get('/fleet/discover', [FleetManagementController::class, 'discover']);
    Route::get('/fleet/devices', [FleetManagementController::class, 'index']);
    Route::post('/fleet/devices', [FleetManagementController::class, 'store']);
    Route::post('/fleet/devices/{id}/action', [FleetManagementController::class, 'executeAction']);
    Route::delete('/fleet/devices/{id}', [FleetManagementController::class, 'destroy']);

    Route::get('/', [TenantController::class, 'index']);
    Route::post('/', [TenantController::class, 'store']);
    Route::get('/{id}', [TenantController::class, 'show']);
    Route::get('/{id}/verify', [TenantController::class, 'verify']);
    Route::post('/{id}/domains', [TenantController::class, 'addDomain']);
    Route::post('/{id}/reset-password', [TenantController::class, 'resetAdminPassword']);
    Route::patch('/{id}/theme', [TenantController::class, 'updateTheme']);
    Route::get('/{id}/billing/status', [BillingController::class, 'status']);
    Route::post('/{id}/billing/checkout-session', [BillingController::class, 'checkoutSession']);
    Route::post('/{id}/billing/portal-session', [BillingController::class, 'portalSession']);
    Route::post('/{id}/billing/demo-profile', [BillingController::class, 'updateDemoProfile']);
    Route::delete('/{id}', [TenantController::class, 'destroy']);
});

Route::middleware('central.admin')->prefix('settings')->group(function () {
    Route::post('/update', [CentralSettingsController::class, 'update']);
});
