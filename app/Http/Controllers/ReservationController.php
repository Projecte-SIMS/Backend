<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use App\Models\Vehicle;
use App\Models\Trip;
use App\Models\CommandLog;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Services\VehicleLocationService;
use Illuminate\Http\JsonResponse;

class ReservationController extends Controller
{
    private VehicleLocationService $iotService;

    public function __construct(VehicleLocationService $iotService)
    {
        $this->iotService = $iotService;
    }

    public function index()
    {
        $this->authorize('viewAny', Reservation::class);

        $user = Auth::user();
        
        // Limpieza automática de reservas caducadas del usuario antes de listar
        $this->cleanupExpiredReservations($user->id);

        $locations = $this->iotService->getLocations();

        return Reservation::where('user_id', $user->id)
            ->with(['vehicle', 'trip'])
            ->orderBy('scheduled_start', 'desc')
            ->get()
            ->map(function($res) use ($locations) {
                $res->remaining_seconds = $res->status === 'pending' 
                    ? max(0, now()->diffInSeconds($res->activation_deadline, false))
                    : 0;
                
                if ($res->vehicle) {
                    $loc = $locations[$res->vehicle->license_plate] ?? null;
                    
                    // Asegurar coordenadas en la raíz para el frontend
                    $res->latitude = isset($loc['latitude']) ? (float)$loc['latitude'] : null;
                    $res->longitude = isset($loc['longitude']) ? (float)$loc['longitude'] : null;

                    if ($res->status === 'active') {
                        $res->telemetry = [
                            'online' => $loc['online'] ?? false,
                            'speed' => $loc['speed'] ?? 0,
                            'rpm' => $loc['rpm'] ?? 0,
                            'engine_temp' => $loc['engine_temp'] ?? 0,
                            'battery_voltage' => $loc['battery_voltage'] ?? 12.6,
                            'device_id' => $loc['device_id'] ?? null,
                        ];
                    }
                }
                
                return $res;
            });
    }

    public function store(Request $request)
    {
        $this->authorize('create', Reservation::class); 

        $validated = $request->validate([
            'vehicle_id' => 'required|exists:vehicles,id',
            'scheduled_start' => 'nullable|date',
        ]);

        $user = $request->user();

        // 1. Limpiar reservas caducadas del usuario
        $this->cleanupExpiredReservations($user->id);

        // 2. Verificar si el usuario ya tiene una reserva activa o pendiente
        $hasActiveBooking = Reservation::where('user_id', $user->id)
            ->whereIn('status', ['active', 'pending'])
            ->exists();

        if ($hasActiveBooking) {
            return response()->json([
                'message' => 'Ya tienes una reserva activa o pendiente. Finalízala antes de realizar otra.'
            ], 422);
        }

        return DB::transaction(function () use ($validated, $user) {
            $vehicle = Vehicle::where('id', $validated['vehicle_id'])->lockForUpdate()->firstOrFail();
            
            // 3. Verificar si el vehículo está libre
            $isBusy = Reservation::where('vehicle_id', $vehicle->id)
                ->whereIn('status', ['active', 'pending'])
                ->exists();

            if ($isBusy) {
                return response()->json([
                    'message' => 'Este vehículo ya no está disponible.'
                ], 409);
            }

            // Si no se envía hora, usamos "ahora"
            $requestedStart = isset($validated['scheduled_start']) 
                ? Carbon::parse($validated['scheduled_start']) 
                : now();

            // 4. Ventana de 10 minutos para activar
            $activationDeadline = $requestedStart->copy()->addMinutes(10);

            $reservation = $user->reservations()->create([
                'vehicle_id' => $vehicle->id,
                'scheduled_start' => $requestedStart,
                'activation_deadline' => $activationDeadline,
                'status' => 'pending',
            ]);

            $reservation->remaining_seconds = 600; // 10 minutes

            return response()->json([
                'message' => 'Reserva creada con éxito. Tienes 10 minutos para activarla.',
                'data' => $reservation
            ], 201);
        });
    }

    /**
     * Helper para cancelar automáticamente reservas que han superado su tiempo límite
     */
    private function cleanupExpiredReservations($userId = null)
    {
        $expiredQuery = Reservation::where('status', 'pending')
            ->where('activation_deadline', '<', now());
            
        if ($userId) {
            $expiredQuery->where('user_id', $userId);
        }

        $expiredReservations = $expiredQuery->get();

        foreach ($expiredReservations as $res) {
            DB::transaction(function () use ($res) {
                $res->update(['status' => 'expired']);
                $res->vehicle()->update(['active' => false]);
            });
        }
    }

    public function show(Reservation $reservation)
    {
        $this->authorize('view', $reservation);
        return $reservation->load(['vehicle', 'trip']);
    }

    public function activate(Request $request, Reservation $reservation)
    {
        $this->authorize('activate', $reservation);

        if ($reservation->status !== 'pending') {
            return response()->json(['message' => 'Reservation is not pending.'], 400);
        }

        if (now()->greaterThan($reservation->activation_deadline)) {
            $this->cleanupExpiredReservations();
            return response()->json(['message' => 'Reservation expired.'], 403);
        }

        $trip = DB::transaction(function () use ($reservation) {
            $reservation->update(['status' => 'active']);
            // Marcar vehículo como OCUPADO en Postgres
            $reservation->vehicle()->update(['active' => true]);
            
            return Trip::create([
                'reservation_id' => $reservation->id,
                'engine_started_at' => now(),
            ]);
        });

        return response()->json([
            'message' => 'Vehicle activated. Engine started.',
            'trip_id' => $trip->id,
            'started_at' => $trip->engine_started_at
        ], 200);
    }

    public function finish(Request $request, Reservation $reservation)
    {
        $this->authorize('finish', $reservation);

        if ($reservation->status !== 'active') {
            return response()->json(['message' => 'Only active reservations can be finished.'], 400);
        }

        // 1. Apagar el motor automáticamente antes de finalizar
        try {
            $loc = $this->iotService->getLocations()[$reservation->vehicle->license_plate] ?? null;
            if ($loc && isset($loc['device_id'])) {
                $this->iotService->turnOff($loc['device_id']);
            }
        } catch (\Exception $e) {
            // Log error but continue finishing trip so user isn't stuck
            \Log::error("Failed to auto-turn-off engine on finish: " . $e->getMessage());
        }

        $trip = Trip::where('reservation_id', $reservation->id)->firstOrFail();

        $start = Carbon::parse($trip->engine_started_at);
        $end = now();
        $minutes = (int) max(1, $start->diffInMinutes($end)); 
        
        $pricePerMinute = (float) ($reservation->vehicle->price_per_minute ?? 0.15);
        $amount = (float) round($minutes * $pricePerMinute, 2);

        // Obtener resumen de ruta antes de limpiar
        $routeData = [];
        $avgSpeed = 0;
        try {
            if ($loc && isset($loc['device_id'])) {
                $routeData = $this->iotService->getRoute($loc['device_id']);
                if (count($routeData) > 0) {
                    $avgSpeed = array_sum(array_column($routeData, 'speed')) / count($routeData);
                }
                // Limpiar ruta para el siguiente usuario
                $this->iotService->clearRoute($loc['device_id']);
            }
        } catch (\Exception $e) {
            \Log::error("Failed to get route summary: " . $e->getMessage());
        }

        DB::transaction(function () use ($reservation, $trip, $end, $amount, $minutes) {
            $trip->update([
                'engine_stopped_at' => $end,
                'total_amount' => $amount,
                'minutes_driven' => $minutes
            ]);
            $reservation->update(['status' => 'completed']);
            // Liberar vehículo en Postgres
            $reservation->vehicle()->update(['active' => false]);
        });

        return response()->json([
            'message' => 'Trip finished.',
            'minutes' => $minutes,
            'cost' => $amount . '€',
            'summary' => [
                'avg_speed' => round($avgSpeed, 1),
                'points' => $routeData
            ]
        ]);
    }

    public function cancel(Reservation $reservation)
    {
        $this->authorize('cancel', $reservation);

        if ($reservation->status !== 'pending') {
            return response()->json(['message' => 'Only pending reservations can be cancelled.'], 400);
        }

        DB::transaction(function () use ($reservation) {
            $reservation->update(['status' => 'cancelled']);
            $reservation->vehicle()->update(['active' => false]);
        });

        return response()->json(['message' => 'Reservation cancelled.']);
    }

    public function forceFinish(Request $request, Reservation $reservation)
    {
        $this->authorize('forceFinish', $reservation);

        if ($reservation->status !== 'active') {
            return response()->json(['message' => 'Only active reservations can be finished.'], 400);
        }

        $trip = Trip::where('reservation_id', $reservation->id)->firstOrFail();
        
        $end = now();
        $start = Carbon::parse($trip->engine_started_at);
        $minutes = (int) ceil($start->floatDiffInMinutes($end));

        if ($request->has('custom_amount')) {
            $amount = $request->custom_amount;
            $noteText = 'Admin Override (Manual price)';
        } else {
             $pricePerMinute = $reservation->vehicle->price_per_minute ?? 0.15;
             $amount = round($minutes * $pricePerMinute, 2);
             $noteText = 'Admin Override (Time calculated)';
        }

        DB::transaction(function () use ($reservation, $trip, $end, $amount, $minutes, $noteText) {
            $trip->update([
                'engine_stopped_at' => $end,
                'total_amount' => $amount,
                'minutes_driven' => $minutes,
                'notes' => $noteText 
            ]);
            $reservation->update(['status' => 'completed']);
            $reservation->vehicle()->update(['active' => false]);
        });

        return response()->json([
            'message' => 'Trip finished by Admin.',
            'cost' => $amount . '€',
            'minutes' => $minutes
        ]);
    }

    public function turnOn(Reservation $reservation): JsonResponse
    {
        $this->authorize('update', $reservation);
        
        if ($reservation->status !== 'active') {
            return response()->json(['message' => 'Vehicle must be active to send commands'], 403);
        }

        $loc = $this->iotService->getLocations()[$reservation->vehicle->license_plate] ?? null;
        if (!$loc || !isset($loc['device_id'])) {
            return response()->json(['message' => 'IoT Device not found'], 404);
        }

        $result = $this->iotService->turnOn($loc['device_id']);

        // Auditoría de comando (Cliente)
        CommandLog::create([
            'user_id' => auth()->id(),
            'device_id' => $loc['device_id'],
            'action' => 'on',
            'status' => $result['success'] ? 'sent' : 'failed',
        ]);

        return response()->json($result);
    }

    public function turnOff(Reservation $reservation): JsonResponse
    {
        $this->authorize('update', $reservation);
        
        if ($reservation->status !== 'active') {
            return response()->json(['message' => 'Vehicle must be active to send commands'], 403);
        }

        $loc = $this->iotService->getLocations()[$reservation->vehicle->license_plate] ?? null;
        if (!$loc || !isset($loc['device_id'])) {
            return response()->json(['message' => 'IoT Device not found'], 404);
        }

        $result = $this->iotService->turnOff($loc['device_id']);

        // Auditoría de comando (Cliente)
        CommandLog::create([
            'user_id' => auth()->id(),
            'device_id' => $loc['device_id'],
            'action' => 'off',
            'status' => $result['success'] ? 'sent' : 'failed',
        ]);

        return response()->json($result);
    }
}