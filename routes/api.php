<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\TenantController;
use App\Http\Controllers\Api\AuthController;

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

// Central authentication for super admin (no domain restriction)
Route::post('/central/login', [AuthController::class, 'centralLogin']);

// Tenant management routes (protected with central admin middleware)
Route::middleware('central.admin')->prefix('tenants')->group(function () {
    Route::get('/', [TenantController::class, 'index']);
    Route::post('/', [TenantController::class, 'store']);
    Route::get('/{id}', [TenantController::class, 'show']);
    Route::get('/{id}/verify', [TenantController::class, 'verify']);
    Route::post('/{id}/domains', [TenantController::class, 'addDomain']);
    Route::post('/{id}/reset-password', [TenantController::class, 'resetAdminPassword']);
    Route::delete('/{id}', [TenantController::class, 'destroy']);
});
