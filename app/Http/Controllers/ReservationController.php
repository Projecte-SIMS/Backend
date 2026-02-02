<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use App\Models\Vehicle;
use App\Models\Trip;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ReservationController extends Controller
{
    // 1. CREAR RESERVA
    public function store(Request $request)
    {
        $validated = $request->validate([
            'vehicle_id' => 'required|exists:vehicles,id',
            'scheduled_start' => 'required|date|after_or_equal:now',
        ]);

        $vehicle = Vehicle::find($validated['vehicle_id']);
        $requestedStart = Carbon::parse($validated['scheduled_start']);

        // Verificar disponibilidad
        $isBusy = Reservation::where('vehicle_id', $vehicle->id)
            ->whereIn('status', ['pending', 'active'])
            ->exists();

        if ($isBusy) {
            return response()->json(['message' => 'Vehículo no disponible.'], 409);
        }

        // 20 minutos de cortesía para llegar al coche
        $activationDeadline = $requestedStart->copy()->addMinutes(20);

        $reservation = Reservation::create([
            'user_id' => $request->user()->id,
            'vehicle_id' => $vehicle->id,
            'scheduled_start' => $requestedStart,
            'activation_deadline' => $activationDeadline,
            'status' => 'pending',
        ]);

        return response()->json([
            'message' => 'Reserva creada. Tienes 20 min para activar el vehículo.',
            'data' => $reservation
        ], 201);
    }

    // 2. ACTIVAR (ENCENDER MOTOR)
    public function activate(Request $request, $id)
    {
        $reservation = Reservation::where('id', $id)
            ->where('user_id', $request->user()->id)
            ->firstOrFail();

        if ($reservation->status !== 'pending') {
            return response()->json(['message' => 'Solo reservas pendientes.'], 400);
        }

        // Si llega tarde (>20 min)
        if (now()->greaterThan($reservation->activation_deadline)) {
            $reservation->update(['status' => 'expired']);
            return response()->json(['message' => 'Tiempo de cortesía expirado.'], 403);
        }

        // Iniciar viaje
        $trip = DB::transaction(function () use ($reservation) {
            $reservation->update(['status' => 'active']);
            return Trip::create([
                'reservation_id' => $reservation->id,
                'engine_started_at' => now(),
            ]);
        });

        return response()->json([
            'message' => '¡Motor encendido! Buen viaje.',
            'trip_id' => $trip->id
        ]);
    }

    // 3. FINALIZAR VIAJE (PAGAR)
    public function finish(Request $request, $id)
    {
        $reservation = Reservation::where('id', $id)
            ->where('user_id', $request->user()->id)
            ->firstOrFail();

        if ($reservation->status !== 'active') {
            return response()->json(['message' => 'La reserva no está activa.'], 400);
        }

        $trip = Trip::where('reservation_id', $reservation->id)->firstOrFail();
        
        // Cálculos
        $end = now();
        $start = Carbon::parse($trip->engine_started_at);
        $minutes = (int) ceil($start->floatDiffInMinutes($end));
        if ($minutes < 1) $minutes = 1;

        $amount = round($minutes * 0.15, 2); // 0.15€ por minuto

        DB::transaction(function () use ($reservation, $trip, $end, $amount, $minutes) {
            $trip->update([
                'engine_stopped_at' => $end,
                'total_amount' => $amount,
                'minutes_driven' => $minutes
            ]);
            $reservation->update(['status' => 'completed']);
        });

        return response()->json([
            'message' => 'Viaje finalizado.',
            'cost' => $amount . '€',
            'minutes' => $minutes
        ]);
    }

    // 4. CANCELAR (LÓGICA MULTA + PERIODO GRACIA)
    public function cancel(Request $request, $id)
    {
        $reservation = Reservation::where('id', $id)
            ->where('user_id', $request->user()->id)
            ->firstOrFail();

        if ($reservation->status !== 'pending') {
            return response()->json(['message' => 'No se puede cancelar.'], 400);
        }

        $now = now();
        $scheduledStart = Carbon::parse($reservation->scheduled_start);
        $createdAt = Carbon::parse($reservation->created_at);

        $hoursUntilStart = $now->floatDiffInHours($scheduledStart, false);
        $minutesSinceBooking = $now->diffInMinutes($createdAt);

        $fee = 0;

        // MULTA SI: Faltan <24h Y han pasado >30 min desde que reservó
        if ($hoursUntilStart < 24 && $minutesSinceBooking > 30) {
            $fee = 5.00;
        }

        $reservation->update([
            'status' => 'cancelled',
            'cancelled_at' => $now,
            'cancellation_fee' => $fee
        ]);

        return response()->json([
            'message' => 'Reserva cancelada.',
            'cancellation_fee' => $fee . '€',
            'note' => $fee > 0 ? 'Cargo por cancelación tardía.' : 'Cancelación gratuita.'
        ]);
    }
}