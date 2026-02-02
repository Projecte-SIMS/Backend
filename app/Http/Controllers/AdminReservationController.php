<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use App\Models\Trip;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AdminReservationController extends Controller
{
    // 1. LISTAR (Con filtros)
    public function index(Request $request)
    {
        // Traemos relaciones para mostrar nombres en la tabla del dashboard
        $query = Reservation::with(['user', 'vehicle', 'trip']);

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        return response()->json($query->orderBy('created_at', 'desc')->paginate(20));
    }

    // 2. VER DETALLE
    public function show($id)
    {
        return response()->json(Reservation::with(['user', 'vehicle', 'trip'])->findOrFail($id));
    }

    // 3. ACTUALIZAR (Manual)
    public function update(Request $request, $id)
    {
        $reservation = Reservation::findOrFail($id);
        $reservation->update($request->all());
        return response()->json(['message' => 'Actualizado por Admin', 'data' => $reservation]);
    }

    // 4. BORRAR (Solo limpieza)
    public function destroy($id)
    {
        $reservation = Reservation::findOrFail($id);
        if ($reservation->status === 'active') {
            return response()->json(['message' => 'No borres reservas activas.'], 409);
        }
        $reservation->delete();
        return response()->json(['message' => 'Eliminado']);
    }

    // 5. ACCIÓN DE EMERGENCIA: FINALIZAR VIAJE FORZOSAMENTE
    // Útil si el usuario abandona el coche y no finaliza en la app
    // Añadimos Request $request para recibir parámetros
    public function forceFinish(Request $request, $id)
    {
        $reservation = Reservation::findOrFail($id);

        if ($reservation->status !== 'active') {
            return response()->json(['message' => 'Solo se pueden finalizar reservas activas.'], 400);
        }

        $trip = Trip::where('reservation_id', $reservation->id)->firstOrFail();
        
        $end = now();
        $start = Carbon::parse($trip->engine_started_at);
        $minutes = (int) ceil($start->floatDiffInMinutes($end));

        // --- LÓGICA NUEVA ---
        // Si el admin envía un "custom_amount" (ej: 0), usamos eso.
        // Si no, calculamos el precio normal por minutos.
        if ($request->has('custom_amount')) {
            $amount = $request->custom_amount;
            $noteText = 'Finalizado por Admin (Precio manual)';
        } else {
            $amount = round($minutes * 0.15, 2);
            $noteText = 'Finalizado por Admin (Calculado por tiempo)';
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
            'message' => 'Viaje finalizado.',
            'cost' => $amount . '€',
            'minutes_computados' => $minutes
        ]);
    }
}