<?php

declare(strict_types=1);

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
use App\Http\Controllers\Api\TenantSettingsController;

use App\Http\Controllers\ChatbotController;
use App\Http\Controllers\Api\UserWalletController;

/*
|--------------------------------------------------------------------------
| Tenant Routes
|--------------------------------------------------------------------------
|
| Tenant is identified by X-Tenant header or ?tenant= query param
| Example: /api/login?tenant=fleetlee
|
*/

Route::middleware([
    'tenant.init',
    'api',
    'tenant.billing.active',
])->prefix('api')->group(function () {
    Route::post('/login', [AuthController::class, 'login'])
        ->middleware('throttle:login')
        ->name('login');

    Route::post('/register', [AuthController::class, 'register'])
        ->middleware('throttle:register')
        ->name('register');

    // Debug endpoint to check tenant context
    Route::get('/debug/tenant', function () {
        $tenantId = tenancy()->tenant?->id ?? null;
        $schemaName = $tenantId ? 'tenant_' . $tenantId : 'public';
        
        return response()->json([
            'tenancy_initialized' => tenancy()->initialized,
            'tenant_id' => $tenantId,
            'schema_name' => $schemaName,
            'connection' => \DB::connection()->getName(),
            'tables' => \DB::select("SELECT table_name FROM information_schema.tables WHERE table_schema = ? LIMIT 30", [$schemaName]),
        ]);
    });

    // Endpoint PÚBLICO para ver vehículos disponibles en el mapa (sin autenticación)
    Route::get('/public/vehicles/map', [VehicleController::class, 'publicMap']);

    Route::middleware('tenant.auth')->group(function () {
        
        Route::post('/logout', [AuthController::class, 'logout']);
        
        // Chatbot endpoint with rate limiting
        Route::post('/chatbot/chat', [ChatbotController::class, 'chat'])
            ->middleware('throttle:chatbot');

        // Users endpoints
        Route::get('/users/me', [UserController::class, 'me']);
        Route::put('/users/me', [UserController::class, 'updateMe']);
        Route::delete('/users/me', [UserController::class, 'destroyMe']);
        
        // Debug auth
        Route::get('/debug/auth', [\App\Http\Controllers\Api\DebugController::class, 'testAuth']);

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
        Route::post('reservations/{reservation}/on', [ReservationController::class, 'turnOn']);
        Route::post('reservations/{reservation}/off', [ReservationController::class, 'turnOff']);
        Route::post('/reservations/{reservation}/force-finish', [ReservationController::class, 'forceFinish']);

        // Cartera (Wallet)
        Route::get('/wallet', [UserWalletController::class, 'index']);
        Route::post('/wallet/topup', [UserWalletController::class, 'topup']);


        // IoT endpoints (lectura para usuarios autenticados)
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

            // Configuración del Tenant
            Route::get('settings', [TenantSettingsController::class, 'index']);
            Route::patch('settings', [TenantSettingsController::class, 'update']);

            // Tickets
            Route::get('tickets', [TicketController::class, 'index']);
            Route::post('tickets', [TicketController::class, 'store']);
            Route::get('tickets/{ticket}', [TicketController::class, 'show']);
            Route::put('tickets/{ticket}', [TicketController::class, 'update']);
            Route::delete('tickets/{ticket}', [TicketController::class, 'destroy']);
            Route::post('tickets/{ticket}/messages', [TicketMessageController::class, 'store']);
            // Bookings (Admin)
            Route::get('bookings', [AdminReservationController::class, 'index'])->name('bookings.index');
            Route::post('bookings', [AdminReservationController::class, 'store'])->name('bookings.store');
            Route::get('bookings/{id}', [AdminReservationController::class, 'show'])->name('bookings.show');
            Route::put('bookings/{id}', [AdminReservationController::class, 'update'])->name('bookings.update');
            Route::delete('bookings/{id}', [AdminReservationController::class, 'destroy'])->name('bookings.destroy');
            Route::post('bookings/{id}/force-finish', [AdminReservationController::class, 'forceFinish'])->name('bookings.forceFinish');

            // Fallback for old reservations route
            Route::any('reservations/{any?}', function($any = null) {
                $newPath = str_replace('reservations', 'bookings', request()->path());
                return redirect(url($newPath), 301);
            })->where('any', '.*');
            
            // IoT Admin endpoints (solo admin)
            Route::get('iot/health', [IoTController::class, 'health']);
            Route::get('iot/logs', [IoTController::class, 'logs']);
            Route::get('iot/devices', [IoTController::class, 'devices']);
            
            // IoT Commands (solo admin puede enviar comandos)
            Route::post('iot/devices/{deviceId}/on', [IoTController::class, 'turnOn']);
            Route::post('iot/devices/{deviceId}/off', [IoTController::class, 'turnOff']);
            Route::post('iot/devices/{deviceId}/command', [IoTController::class, 'sendCommand']);
            
            // IoT Device Linking (vincular dispositivos a vehículos)
            Route::post('iot/devices/{deviceId}/link', [IoTController::class, 'linkToVehicle']);
            Route::post('iot/devices/{deviceId}/unlink', [IoTController::class, 'unlinkDevice']);
            Route::delete('iot/devices/{deviceId}', [IoTController::class, 'destroy']);
            Route::post('iot/devices/{deviceId}/create-vehicle', [IoTController::class, 'createVehicleAndLink']);
            Route::get('iot/devices/unlinked', [IoTController::class, 'unlinkedDevices']);
            Route::get('iot/vehicles/available', [IoTController::class, 'availableVehicles']);
        });
    });
});

// Web routes for tenant (optional, can be removed if not needed)
Route::middleware([
    'tenant.init',
    'web',
    'tenant.billing.active',
])->group(function () {
    Route::get('/tenant-info', function () {
        return 'Tenant: ' . tenant('id');
    });
});
