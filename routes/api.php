<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\RoleController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\VehicleController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\AdminReservationController;

Route::post('/login', [AuthController::class, 'login']);

Route::get('/login', function () {
    return response()->json(['message' => 'Unauthenticated.'], 401);
})->name('login');

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);
    
    Route::apiResource('users', UserController::class);
    Route::apiResource('roles', RoleController::class);
    Route::apiResource('vehicles', VehicleController::class);
    Route::post('/reservations', [ReservationController::class, 'store']);
    Route::post('/reservations/{id}/activate', [ReservationController::class, 'activate']);
    Route::post('/reservations/{id}/finish', [ReservationController::class, 'finish']);
    Route::post('/reservations/{id}/cancel', [ReservationController::class, 'cancel']);


    Route::prefix('admin')->group(function () {
        Route::apiResource('reservations', AdminReservationController::class);
        Route::post('/reservations/{id}/force-finish', [AdminReservationController::class, 'forceFinish']);
    });
});