<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\RoleController;
use App\Http\Controllers\Api\AuthController;

use App\Http\Controllers\VehicleController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\TicketMessageController;
use App\Http\Controllers\ReservationController;


Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::post('/users', [UserController::class, 'store']);



Route::middleware('auth:sanctum')->group(function () {
    
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);
    
    Route::apiResource('users', UserController::class)->except(['store']);
    Route::apiResource('roles', RoleController::class)->middleware(\Spatie\Permission\Middleware\RoleMiddleware::class.':Admin');
    Route::apiResource('vehicles', VehicleController::class);
    Route::get('vehicles-map', [VehicleController::class, 'map']);
    Route::get('vehicles-map-admin', [VehicleController::class, 'adminMap'])->middleware(\Spatie\Permission\Middleware\RoleMiddleware::class.':Admin');

    Route::apiResource('tickets', TicketController::class);
    Route::post('tickets/{ticket}/messages', [TicketMessageController::class, 'store']);
    Route::delete('messages/{message}', [TicketMessageController::class, 'destroy']);


    Route::get('reservations', [ReservationController::class, 'index']);
    Route::post('reservations', [ReservationController::class, 'store']);
    Route::get('reservations/{reservation}', [ReservationController::class, 'show']);
    

    Route::post('reservations/{reservation}/activate', [ReservationController::class, 'activate']);
    Route::post('reservations/{reservation}/finish', [ReservationController::class, 'finish']);
    Route::post('reservations/{reservation}/cancel', [ReservationController::class, 'cancel']);
    
    Route::post('reservations/{reservation}/force-finish', [ReservationController::class, 'forceFinish']);
});