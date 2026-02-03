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

/*
|--------------------------------------------------------------------------
| Public Routes (No Token Required)
|--------------------------------------------------------------------------
*/

Route::post('/login', [AuthController::class, 'login']);

Route::get('/login', function () {
    return response()->json(['message' => 'Unauthenticated.'], 401);
})->name('login');

// User Registration (Open to everyone)
Route::post('/users', [UserController::class, 'store']);


/*
|--------------------------------------------------------------------------
| Protected Routes (Token Required)
|--------------------------------------------------------------------------
*/
Route::middleware('auth:sanctum')->group(function () {

    // --- AUTHENTICATION & PROFILE ---
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);


    // --- USER ZONE (CLIENT) ---
    
    // Vehicles: Using {vehicle} for automatic Model Binding
    Route::get('/vehicles', [VehicleController::class, 'index']);
    Route::get('/vehicles/{vehicle}', [VehicleController::class, 'show']); 

    // Reservations: Standard flow
    Route::post('/reservations', [ReservationController::class, 'store']);
    Route::post('/reservations/{id}/activate', [ReservationController::class, 'activate']);
    Route::post('/reservations/{id}/finish', [ReservationController::class, 'finish']);
    Route::post('/reservations/{id}/cancel', [ReservationController::class, 'cancel']);

    // Tickets: Create issues and view own tickets
    // Limit to prevent user from deleting/editing main tickets, only view and create
    Route::apiResource('tickets', TicketController::class)->except(['update', 'destroy']);
    Route::post('tickets/{ticket}/messages', [TicketMessageController::class, 'store']);
    Route::delete('tickets/{ticket}/messages/{message}', [TicketMessageController::class, 'destroy']);


    // --- ADMIN ZONE (MANAGEMENT PANEL) ---
    // Everything below requires management permissions
    // ADDED: middleware('role:Admin') to protect the entire group 🔒
    Route::prefix('admin')
        ->middleware('role:Admin') 
        ->group(function () {
        
        // Reservations Dashboard (Full CRUD + Force Finish)
        Route::apiResource('reservations', AdminReservationController::class);
        Route::post('/reservations/{id}/force-finish', [AdminReservationController::class, 'forceFinish']);

        // User Management (List, Edit, Delete users)
        Route::apiResource('users', UserController::class)->except(['store']);
        
        // Role Management (Admin only)
        Route::apiResource('roles', RoleController::class);

        // Vehicle Management (Create, Edit, Delete vehicles)
        Route::post('/vehicles', [VehicleController::class, 'store']);
        Route::put('/vehicles/{vehicle}', [VehicleController::class, 'update']);
        Route::delete('/vehicles/{vehicle}', [VehicleController::class, 'destroy']);
    });

});