<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\RoleController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\VehicleController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\AdminReservationController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\TicketMessageController;

Route::post('/login', [AuthController::class, 'login']);

Route::get('/login', function () {
    return response()->json(['message' => 'Unauthenticated.'], 401);
})->name('login');

// Registro público: cualquiera puede crear un usuario (rol por defecto)
Route::post('/users', [UserController::class, 'store']);

// Rutas protegidas por autenticación
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);
    
    // Las demás operaciones sobre users requieren autenticación
    Route::apiResource('users', UserController::class)->except(['store']);
    Route::apiResource('roles', RoleController::class);
    Route::apiResource('vehicles', VehicleController::class);
    
    // Reservations
    Route::post('/reservations', [ReservationController::class, 'store']);
    Route::post('/reservations/{id}/activate', [ReservationController::class, 'activate']);
    Route::post('/reservations/{id}/finish', [ReservationController::class, 'finish']);
    Route::post('/reservations/{id}/cancel', [ReservationController::class, 'cancel']);

    Route::prefix('admin')->group(function () {
        Route::apiResource('reservations', AdminReservationController::class);
        Route::post('/reservations/{id}/force-finish', [AdminReservationController::class, 'forceFinish']);
    });

    // Tickets
    Route::apiResource('tickets', TicketController::class);
    Route::post('tickets/{ticket}/messages', [TicketMessageController::class, 'store']);
    Route::delete('tickets/{ticket}/messages/{message}', [TicketMessageController::class, 'destroy']);
});