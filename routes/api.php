<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\RoleController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\VehicleController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\TicketMessageController;

// Rutas públicas de autenticación
Route::post('/login', [AuthController::class, 'login']);

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
    Route::apiResource('tickets', TicketController::class);
    Route::post('tickets/{ticket}/messages', [TicketMessageController::class, 'store']);
    Route::delete('tickets/{ticket}/messages/{message}', [TicketMessageController::class, 'destroy']);
});