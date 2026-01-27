<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\VehicleController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\TicketMessageController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::apiResource('vehicles', VehicleController::class);
Route::apiResource('tickets', TicketController::class);

Route::post('ticket-messages', [TicketMessageController::class, 'store']);
Route::delete('ticket-messages/{message}', [TicketMessageController::class, 'destroy']);
