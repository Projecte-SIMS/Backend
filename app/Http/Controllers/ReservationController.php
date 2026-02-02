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
    // 1. CREATE RESERVATION
    public function store(Request $request)
    {
        $validated = $request->validate([
            'vehicle_id' => 'required|exists:vehicles,id',
            'scheduled_start' => 'required|date|after_or_equal:now',
        ]);

        $vehicle = Vehicle::find($validated['vehicle_id']);
        $requestedStart = Carbon::parse($validated['scheduled_start']);

        // Check availability
        $isBusy = Reservation::where('vehicle_id', $vehicle->id)
            ->whereIn('status', ['pending', 'active'])
            ->exists();

        if ($isBusy) {
            return response()->json(['message' => 'Vehicle not available.'], 409);
        }

        // 20 minutes courtesy time to reach the car
        $activationDeadline = $requestedStart->copy()->addMinutes(20);

        $reservation = Reservation::create([
            'user_id' => $request->user()->id,
            'vehicle_id' => $vehicle->id,
            'scheduled_start' => $requestedStart,
            'activation_deadline' => $activationDeadline,
            'status' => 'pending',
        ]);

        return response()->json([
            'message' => 'Reservation created. You have 20 min to activate the vehicle.',
            'data' => $reservation
        ], 201);
    }

    // 2. ACTIVATE (START ENGINE)
    public function activate(Request $request, $id)
    {
        $reservation = Reservation::where('id', $id)
            ->where('user_id', $request->user()->id)
            ->firstOrFail();

        if ($reservation->status !== 'pending') {
            return response()->json(['message' => 'Only pending reservations.'], 400);
        }

        // If late (>20 min)
        if (now()->greaterThan($reservation->activation_deadline)) {
            $reservation->update(['status' => 'expired']);
            return response()->json(['message' => 'Courtesy time expired.'], 403);
        }

        // Start trip
        $trip = DB::transaction(function () use ($reservation) {
            $reservation->update(['status' => 'active']);
            return Trip::create([
                'reservation_id' => $reservation->id,
                'engine_started_at' => now(),
            ]);
        });

        return response()->json([
            'message' => 'Engine started! Have a good trip.',
            'trip_id' => $trip->id
        ]);
    }

    // 3. FINISH TRIP (PAY)
    public function finish(Request $request, $id)
    {
        $reservation = Reservation::where('id', $id)
            ->where('user_id', $request->user()->id)
            ->firstOrFail();

        if ($reservation->status !== 'active') {
            return response()->json(['message' => 'Reservation is not active.'], 400);
        }

        $trip = Trip::where('reservation_id', $reservation->id)->firstOrFail();
        
        // Calculations
        $end = now();
        $start = Carbon::parse($trip->engine_started_at);
        $minutes = (int) ceil($start->floatDiffInMinutes($end));
        if ($minutes < 1) $minutes = 1;

        $amount = round($minutes * 0.15, 2); // 0.15€ per minute

        DB::transaction(function () use ($reservation, $trip, $end, $amount, $minutes) {
            $trip->update([
                'engine_stopped_at' => $end,
                'total_amount' => $amount,
                'minutes_driven' => $minutes
            ]);
            $reservation->update(['status' => 'completed']);
        });

        return response()->json([
            'message' => 'Trip finished.',
            'cost' => $amount . '€',
            'minutes' => $minutes
        ]);
    }

    // 4. CANCEL (FEE LOGIC + GRACE PERIOD)
    public function cancel(Request $request, $id)
    {
        $reservation = Reservation::where('id', $id)
            ->where('user_id', $request->user()->id)
            ->firstOrFail();

        if ($reservation->status !== 'pending') {
            return response()->json(['message' => 'Cannot cancel.'], 400);
        }

        $now = now();
        $scheduledStart = Carbon::parse($reservation->scheduled_start);
        $createdAt = Carbon::parse($reservation->created_at);

        $hoursUntilStart = $now->floatDiffInHours($scheduledStart, false);
        $minutesSinceBooking = $now->diffInMinutes($createdAt);

        $fee = 0;

        // FEE IF: Less than 24h left AND >30 min since booking
        if ($hoursUntilStart < 24 && $minutesSinceBooking > 30) {
            $fee = 5.00;
        }

        $reservation->update([
            'status' => 'cancelled',
            'cancelled_at' => $now,
            'cancellation_fee' => $fee
        ]);

        return response()->json([
            'message' => 'Reservation cancelled.',
            'cancellation_fee' => $fee . '€',
            'note' => $fee > 0 ? 'Late cancellation fee.' : 'Free cancellation.'
        ]);
    }
}