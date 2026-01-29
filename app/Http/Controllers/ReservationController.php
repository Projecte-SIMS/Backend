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
        // 1. CREAR RESERVA (POST /api/reservations)
        public function store(Request $request)
        {
            // A. Validamos los datos que vienen de Postman
            $validated = $request->validate([
                'vehicle_id' => 'required|exists:vehicles,id',
                'scheduled_start' => 'required|date|after_or_equal:now',
            ]);

            $vehicle = Vehicle::find($validated['vehicle_id']);
            $requestedStart = Carbon::parse($validated['scheduled_start']);

            // B. Comprobamos si el coche está ocupado (pending o active)
            $isBusy = Reservation::where('vehicle_id', $vehicle->id)
                ->whereIn('status', ['pending', 'active'])
                ->exists();

            if ($isBusy) {
                return response()->json([
                    'message' => 'Este vehículo no está disponible en este momento.'
                ], 409); // 409 Conflict
            }

            // C. Calculamos el tiempo límite (20 minutos de cortesía)
            $activationDeadline = $requestedStart->copy()->addMinutes(20);

            // D. Guardamos en la Base de Datos
            // NOTA: Ya he quitado el campo 'price' para que no te de Error 500
            $reservation = Reservation::create([
                'user_id' => $request->user()->id,
                'vehicle_id' => $vehicle->id,
                'scheduled_start' => $requestedStart,
                'activation_deadline' => $activationDeadline,
                'status' => 'pending',
            ]);

            return response()->json([
                'message' => 'Reserva creada con éxito. Tienes 20 minutos para activar el vehículo.',
                'data' => $reservation
            ], 201);
        }

        // 2. ACTIVAR RESERVA / ENCENDER MOTOR (POST /api/reservations/{id}/activate)
        public function activate(Request $request, $id)
        {
            // A. Buscamos la reserva y verificamos que sea del usuario
            $reservation = Reservation::where('id', $id)
                ->where('user_id', $request->user()->id)
                ->firstOrFail();

            // B. Verificamos que esté pendiente
            if ($reservation->status !== 'pending') {
                return response()->json([
                    'message' => 'No se puede activar una reserva que no está pendiente.'
                ], 400);
            }

            // C. LA REGLA DE ORO: ¿Ha llegado tarde? (Más de 20 min)
            if (now()->greaterThan($reservation->activation_deadline)) {
                
                $reservation->update(['status' => 'expired']);

                return response()->json([
                    'message' => 'Has llegado tarde. El tiempo de cortesía ha expirado.'
                ], 403);
            }

            // D. Todo correcto: Encendemos motor (Creamos Trip y actualizamos estado)
            $trip = DB::transaction(function () use ($reservation) {
                
                $reservation->update(['status' => 'active']);

                return Trip::create([
                    'reservation_id' => $reservation->id,
                    'engine_started_at' => now(),
                ]);
            });

            return response()->json([
                'message' => 'Vehículo activado correctamente. ¡Buen viaje!',
                'trip_id' => $trip->id,
                'started_at' => $trip->engine_started_at
            ], 200);
        }
        // FINALIZAR VIAJE
        public function finish(Request $request, $id)
        {
            // 1. Buscar la reserva activa
            $reservation = Reservation::where('id', $id)
                ->where('user_id', $request->user()->id)
                ->firstOrFail();

            if ($reservation->status !== 'active') {
                return response()->json(['message' => 'Solo puedes finalizar reservas activas.'], 400);
            }

            // 2. Buscar el viaje
            $trip = Trip::where('reservation_id', $reservation->id)->firstOrFail();

            // 3. Cálculos finales
            $start = \Carbon\Carbon::parse($trip->engine_started_at);
            $end = now();
            
            // Calculamos minutos (redondeando hacia arriba)
            $minutes = $start->diffInMinutes($end) + 1; 

            // Precio: 0.15€ por minuto (puedes cambiarlo)
            $pricePerMinute = 0.15;
            $amount = $minutes * $pricePerMinute;

            // 4. Guardar en Base de Datos
            \DB::transaction(function () use ($reservation, $trip, $end, $amount, $minutes) {
                
                // Actualizamos el viaje con TUS columnas exactas
                $trip->update([
                    'engine_stopped_at' => $end,
                    'total_amount' => $amount,      // <--- Aquí guardamos el precio
                    'minutes_driven' => $minutes    // <--- Aquí los minutos
                ]);

                // Liberamos el coche (reserva completada)
                $reservation->update(['status' => 'completed']);
            });

            return response()->json([
                'message' => 'Viaje finalizado. ¡Gracias!',
                'minutes' => $minutes,
                'cost' => $amount . '€'
            ]);
        }
    }

