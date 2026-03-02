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
use App\Http\Controllers\IoTController;

use App\Http\Controllers\ChatbotController;


Route::post('/login', [AuthController::class, 'login'])
    ->middleware('throttle:login')
    ->name('login');

Route::post('/register', [AuthController::class, 'register'])
    ->middleware('throttle:login')
    ->name('register');

// Health check del microservicio IoT (público para monitorización)
Route::get('/iot/health', [IoTController::class, 'health']);

// Endpoint PÚBLICO para ver vehículos disponibles en el mapa (sin autenticación)
Route::get('/public/vehicles/map', [VehicleController::class, 'publicMap']);

Route::middleware('auth:sanctum')->group(function () {
    
    Route::post('/logout', [AuthController::class, 'logout']);
    
    // Chatbot endpoint with rate limiting
    Route::post('/chatbot/chat', [ChatbotController::class, 'chat'])
        ->middleware('throttle:chatbot');

    // Users endpoints
    Route::get('/users/me', [UserController::class, 'me']);
    Route::put('/users/me', [UserController::class, 'updateMe']);
    Route::delete('/users/me', [UserController::class, 'destroyMe']);

    // Vehicles (Client) - usuario autenticado
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

    // IoT endpoints (dispositivos y comandos)
    Route::prefix('iot')->group(function () {
        Route::get('devices', [IoTController::class, 'devices']);
        Route::get('devices/{deviceId}', [IoTController::class, 'device']);
        Route::get('devices/{deviceId}/ping', [IoTController::class, 'ping']);
    });

    
    // Admin endpoints (Admin only operations)
    Route::prefix('admin')->name('admin.')->middleware('admin')->group(function () {
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
        Route::get('vehicles/map', [VehicleController::class, 'adminMap']);
        Route::apiResource('vehicles', VehicleController::class);
        // Tickets
        Route::get('tickets', [TicketController::class, 'index']);
        Route::post('tickets', [TicketController::class, 'store']);
        Route::get('tickets/{ticket}', [TicketController::class, 'show']);
        Route::put('tickets/{ticket}', [TicketController::class, 'update']);
        Route::delete('tickets/{ticket}', [TicketController::class, 'destroy']);
        Route::post('tickets/{ticket}/messages', [TicketMessageController::class, 'store']);
        // Reservations
        Route::get('reservations', [AdminReservationController::class, 'index'])->name('reservations.index');
        Route::post('reservations', [AdminReservationController::class, 'store'])->name('reservations.store');
        Route::get('reservations/{id}', [AdminReservationController::class, 'show'])->name('reservations.show');
        Route::put('reservations/{id}', [AdminReservationController::class, 'update'])->name('reservations.update');
        Route::delete('reservations/{id}', [AdminReservationController::class, 'destroy'])->name('reservations.destroy');
        Route::post('reservations/{id}/force-finish', [AdminReservationController::class, 'forceFinish'])->name('reservations.forceFinish');
        
        // IoT Commands (solo admin puede enviar comandos)
        Route::post('iot/devices/{deviceId}/on', [IoTController::class, 'turnOn']);
        Route::post('iot/devices/{deviceId}/off', [IoTController::class, 'turnOff']);
        Route::post('iot/devices/{deviceId}/command', [IoTController::class, 'sendCommand']);
        
        // IoT Device Linking (vincular dispositivos a vehículos)
        Route::post('iot/devices/{deviceId}/link', [IoTController::class, 'linkToVehicle']);
        Route::get('iot/devices/unlinked', [IoTController::class, 'unlinkedDevices']);
        Route::get('iot/vehicles/available', [IoTController::class, 'availableVehicles']);
    });
});
