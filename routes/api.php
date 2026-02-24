<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\RoleController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\PermissionController;

use App\Http\Controllers\VehicleController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\TicketMessageController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\AdminReservationController;


Route::post('/login', [AuthController::class, 'login'])->name('login');

Route::middleware('auth:sanctum')->group(function () {
    
    Route::post('/logout', [AuthController::class, 'logout']);
    // Users endpoints
    Route::get('/users/me', [UserController::class, 'me']);
    Route::put('/users/me', [UserController::class, 'updateMe']);
    Route::delete('/users/me', [UserController::class, 'destroyMe']);

    // Vehicles (Client)
    Route::get('vehicles', [VehicleController::class, 'index']);
    Route::get('vehicles/map', [VehicleController::class, 'map']);
    Route::get('vehicles/{vehicle}', [VehicleController::class, 'show']);

    // Tiquets (Client)
    Route::get('tickets', [TicketController::class, 'index']);
    Route::post('tickets', [TicketController::class, 'store']);
    Route::get('tickets/{ticket}', [TicketController::class, 'show']);
    Route::post('tickets/{ticket}/messages', [TicketMessageController::class, 'store']);
    Route::delete('messages/{message}', [TicketMessageController::class, 'destroy']);

    // Reserves (Client)
    Route::get('reservations', [ReservationController::class, 'index']);
    Route::post('reservations', [ReservationController::class, 'store']);
    Route::get('reservations/{reservation}', [ReservationController::class, 'show']);
    Route::post('reservations/{reservation}/activate', [ReservationController::class, 'activate']);
    Route::post('reservations/{reservation}/finish', [ReservationController::class, 'finish']);
    Route::post('reservations/{reservation}/cancel', [ReservationController::class, 'cancel']);
    Route::post('reservations/{reservation}/force-finish', [ReservationController::class, 'forceFinish']);

    // Limitar acceso a solo Client mediante middleware personalizado si es necesario

    
    // Admin endpoints (Admin only operations)
    Route::prefix('admin')->name('admin.')->group(function () {
        // Users (solo admin)
        Route::apiResource('users', UserController::class);
        // Roles (solo admin)
        Route::apiResource('roles', RoleController::class);
        // Permissions (solo admin)
        Route::get('permissions', [PermissionController::class, 'index']);
        Route::post('permissions', [PermissionController::class, 'store']);
        Route::put('permissions/{id}', [PermissionController::class, 'update']);
        Route::delete('permissions/{id}', [PermissionController::class, 'destroy']);
        // Vehicles
        Route::apiResource('vehicles', VehicleController::class);
        Route::get('vehicles/map', [VehicleController::class, 'adminMap']);
        // Tickets
        Route::get('tickets', [TicketController::class, 'index']);
        Route::post('tickets', [TicketController::class, 'store']);
        Route::get('tickets/{id}', [TicketController::class, 'show']);
        Route::put('tickets/{id}', [TicketController::class, 'update']);
        Route::delete('tickets/{id}', [TicketController::class, 'destroy']);
        Route::post('tickets/{id}/messages', [TicketMessageController::class, 'store']);
        // Reservations
        Route::get('reservations', [AdminReservationController::class, 'index'])->name('reservations.index');
        Route::post('reservations', [AdminReservationController::class, 'store'])->name('reservations.store');
        Route::get('reservations/{id}', [AdminReservationController::class, 'show'])->name('reservations.show');
        Route::put('reservations/{id}', [AdminReservationController::class, 'update'])->name('reservations.update');
        Route::delete('reservations/{id}', [AdminReservationController::class, 'destroy'])->name('reservations.destroy');
        Route::post('reservations/{id}/force-finish', [AdminReservationController::class, 'forceFinish'])->name('reservations.forceFinish');
    });
});
