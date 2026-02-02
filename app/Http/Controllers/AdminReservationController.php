<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use App\Models\Trip;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AdminReservationController extends Controller
{
    // 1. LIST (With filters)
    public function index(Request $request)
    {
        // Bring relations to show names in the dashboard table
        $query = Reservation::with(['user', 'vehicle', 'trip']);

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        return response()->json($query->orderBy('created_at', 'desc')->paginate(20));
    }

    // 2. VIEW DETAIL
    public function show($id)
    {
        return response()->json(Reservation::with(['user', 'vehicle', 'trip'])->findOrFail($id));
    }

    // 3. UPDATE (Manual)
    public function update(Request $request, $id)
    {
        $reservation = Reservation::findOrFail($id);
        $reservation->update($request->all());
        return response()->json(['message' => 'Updated by Admin', 'data' => $reservation]);
    }

    // 4. DELETE (Cleanup only)
    public function destroy($id)
    {
        $reservation = Reservation::findOrFail($id);
        if ($reservation->status === 'active') {
            return response()->json(['message' => 'Do not delete active reservations.'], 409);
        }
        $reservation->delete();
        return response()->json(['message' => 'Deleted']);
    }

    // 5. EMERGENCY ACTION: FORCE FINISH TRIP
    // Useful if user abandons car and doesn't finish in app
    // Add Request $request to receive parameters
    public function forceFinish(Request $request, $id)
    {
        $reservation = Reservation::findOrFail($id);

        if ($reservation->status !== 'active') {
            return response()->json(['message' => 'Only active reservations can be finished.'], 400);
        }

        $trip = Trip::where('reservation_id', $reservation->id)->firstOrFail();
        
        $end = now();
        $start = Carbon::parse($trip->engine_started_at);
        $minutes = (int) ceil($start->floatDiffInMinutes($end));

        // --- NEW LOGIC ---
        // If admin sends "custom_amount" (e.g., 0), use that.
        // Otherwise, calculate normal price per minute.
        if ($request->has('custom_amount')) {
            $amount = $request->custom_amount;
            $noteText = 'Finished by Admin (Manual price)';
        } else {
            $amount = round($minutes * 0.15, 2);
            $noteText = 'Finished by Admin (Time calculated)';
        }
        // --------------------

        DB::transaction(function () use ($reservation, $trip, $end, $amount, $minutes, $noteText) {
            $trip->update([
                'engine_stopped_at' => $end,
                'total_amount' => $amount,
                'minutes_driven' => $minutes,
                'notes' => $noteText 
            ]);
            $reservation->update(['status' => 'completed']);
        });

        return response()->json([
            'message' => 'Trip finished.',
            'cost' => $amount . '€',
            'minutes_computados' => $minutes
        ]);
    }
}