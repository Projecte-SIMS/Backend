<?php

namespace App\Http\Controllers;

use App\Models\Vehicle;
use App\Models\Reservation;
use App\Http\Requests\Vehicle\StoreVehicleRequest;
use App\Http\Requests\Vehicle\UpdateVehicleRequest;
use App\Services\VehicleLocationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class VehicleController extends Controller
{
    private VehicleLocationService $locationService;

    public function __construct(VehicleLocationService $locationService) {
        $this->locationService = $locationService;
    }

    /**
     * Lista paginada de vehículos con filtros
     * 
     * Filtros disponibles:
     * - search: busca por license_plate, brand o model
     * - license_plate: filtro exacto por matrícula
     * - brand: filtro por marca
     * - model: filtro por modelo
     * - active: filtro por estado activo (true/false)
     */
    public function index(Request $request): JsonResponse
    {
        // Limpiar reservas caducadas globalmente antes de procesar disponibilidad
        Reservation::where('status', 'pending')
            ->where('activation_deadline', '<', now())
            ->update(['status' => 'expired']);

        $query = Vehicle::query();
        $user = auth()->user();
        $isAdminRequest = $request->segment(2) === 'admin';
        $isAdminUser = $user && ($user->hasRole('Admin') || $user->hasRole('admin'));
        $shouldShowAll = $isAdminUser && $isAdminRequest;

        // Búsqueda y filtros...
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('license_plate', 'ILIKE', "%{$search}%")
                  ->orWhere('brand', 'ILIKE', "%{$search}%")
                  ->orWhere('model', 'ILIKE', "%{$search}%");
            });
        }

        if (!$shouldShowAll) {
            $query->where('active', false);
        }

        $vehicles = $query->orderBy('created_at', 'desc')
                          ->paginate($request->input('per_page', 15));

        $locations = $this->locationService->getLocations();
        
        // Reservas para filtrado
        $currentReservations = Reservation::whereIn('status', ['pending', 'active'])
            ->get()
            ->groupBy('vehicle_id');

        $vehicles->getCollection()->transform(function ($vehicle) use ($locations, $currentReservations, $user, $shouldShowAll) {
            $location = $locations[$vehicle->license_plate] ?? null;
            $reservation = $currentReservations->get($vehicle->id)?->first();
            
            $isMine = $reservation && $reservation->user_id === $user?->id;
            $isOccupied = ($location['active'] ?? false) === true;
            $status = $isOccupied ? 'running' : ($reservation ? 'reserved' : 'available');

            $vehicle->setAttribute('latitude', isset($location['latitude']) ? (float)$location['latitude'] : null);
            $vehicle->setAttribute('longitude', isset($location['longitude']) ? (float)$location['longitude'] : null);
            $vehicle->setAttribute('mongo_active', $isOccupied);
            $vehicle->setAttribute('status', $status);
            $vehicle->setAttribute('is_mine', $isMine);
            
            return $vehicle;
        });

        if (!$shouldShowAll) {
            // Para clientes: Solo disponibles O el mío reservado, Y QUE TENGAN RELACIÓN EN MONGO
            $filtered = $vehicles->getCollection()->filter(function ($v) {
                $hasMongoRelation = $v->latitude !== null && $v->longitude !== null;
                $isAvailable = $v->status === 'available' || ($v->status === 'reserved' && $v->is_mine);
                
                return $hasMongoRelation && $isAvailable;
            })->values();
            
            $vehicles->setCollection($filtered);
        }

        return response()->json($vehicles);
    }

    /**
     * Crear un nuevo vehículo
     */
    public function store(StoreVehicleRequest $request): JsonResponse
    {
        $this->authorize('create', Vehicle::class);

        $vehicle = Vehicle::create($request->validated());

        return response()->json([
            'message' => 'Vehículo creado correctamente',
            'data' => $vehicle,
        ], 201);
    }

    /**
     * Mostrar un vehículo específico
     */
    public function show(Vehicle $vehicle): JsonResponse
    {
        $this->authorize('view', $vehicle);

        return response()->json([
            'data' => $vehicle,
        ]);
    }

    /**
     * Actualizar un vehículo
     */
    public function update(UpdateVehicleRequest $request, Vehicle $vehicle): JsonResponse
    {
        $this->authorize('update', $vehicle);

        $vehicle->update($request->validated());

        return response()->json([
            'message' => 'Vehículo actualizado correctamente',
            'data' => $vehicle,
        ]);
    }

    /**
     * Eliminar un vehículo (soft delete)
     */
    public function destroy(Vehicle $vehicle): JsonResponse
    {
        $this->authorize('delete', $vehicle);

        $vehicle->delete();

        return response()->json([
            'message' => 'Vehículo eliminado correctamente',
        ]);
    }

    /**
     * Obtener vehículos con ubicaciones para el mapa (VISTA CLIENTE)
     * Reglas:
     * 1. Solo vehículos habilitados (active=false en tu convención actual).
     * 2. Solo vehículos SIN reserva activa/pendiente por otros.
     * 3. INCLUIR el vehículo reservado por el usuario actual (si tiene).
     */
    public function map(): JsonResponse
    {
        $this->authorize('viewAny', Vehicle::class);
        $user = auth()->user();

        // 1. Limpiar reservas caducadas globalmente (para asegurar datos frescos)
        Reservation::where('status', 'pending')
            ->where('activation_deadline', '<', now())
            ->update(['status' => 'expired']);

        // 2. Obtener vehículos habilitados
        $vehicles = Vehicle::where('active', false)->get();
        $locations = $this->locationService->getLocations();

        // 3. Obtener reservas actuales (pendientes o activas)
        $currentReservations = Reservation::whereIn('status', ['pending', 'active'])
            ->get()
            ->groupBy('vehicle_id');

        $result = $vehicles->map(function ($vehicle) use ($locations, $currentReservations, $user) {
            $location = $locations[$vehicle->license_plate] ?? null;
            $reservation = $currentReservations->get($vehicle->id)?->first();
            
            $isReservedByMe = $reservation && $reservation->user_id === $user->id;
            $isReservedByOthers = $reservation && $reservation->user_id !== $user->id;
            
            // Determinar estado real
            $isOccupied = ($location['active'] ?? false) === true; // En marcha (Mongo)
            $isPending = $reservation && $reservation->status === 'pending'; // Reservado (Postgres)

            return [
                'id' => $vehicle->id,
                'plate' => $vehicle->license_plate,
                'brand' => $vehicle->brand,
                'model' => $vehicle->model,
                'latitude' => isset($location['latitude']) ? (float)$location['latitude'] : null,
                'longitude' => isset($location['longitude']) ? (float)$location['longitude'] : null,
                'postgres_active' => $reservation ? true : false, // Reservado en Postgres
                'mongo_active' => $isOccupied, // En marcha en Mongo
                'is_mine' => $isReservedByMe,
                'status' => $isOccupied ? 'running' : ($isPending ? 'reserved' : 'available'),
                'available' => !$isReservedByOthers && !$isOccupied
            ];
        })
        // Filtro para el mapa de cliente:
        // 1. Mostrar vehículos LIBRES para todos.
        // 2. Mostrar vehículos RESERVADOS solo si son del usuario actual.
        // 3. OCULTAR vehículos en marcha (running) para todos.
        ->filter(function($v) {
            return $v['latitude'] !== null && 
                   $v['longitude'] !== null && 
                   ($v['status'] === 'available' || ($v['status'] === 'reserved' && $v['is_mine']));
        })->values();

        return response()->json($result);
    }


    /**
     * Obtener vehículos para admin (Vista Gestión Total)
     */
    public function adminMap(): JsonResponse
    {
        $this->authorize('viewAny', Vehicle::class);

        // Limpiar reservas caducadas
        Reservation::where('status', 'pending')
            ->where('activation_deadline', '<', now())
            ->update(['status' => 'expired']);

        $vehicles = Vehicle::all();
        $locations = $this->locationService->getLocations();
        $currentReservations = Reservation::whereIn('status', ['pending', 'active'])->get()->groupBy('vehicle_id');

        $result = $vehicles->map(function ($vehicle) use ($locations, $currentReservations) {
            $location = $locations[$vehicle->license_plate] ?? null;
            $reservation = $currentReservations->get($vehicle->id)?->first();

            return [
                'id' => (int) $vehicle->id,
                'plate' => (string) $vehicle->license_plate,
                'brand' => (string) $vehicle->brand,
                'model' => (string) $vehicle->model,
                'latitude' => isset($location['latitude']) ? (float) $location['latitude'] : null,
                'longitude' => isset($location['longitude']) ? (float) $location['longitude'] : null,
                'postgres_active' => $reservation ? true : false,
                'mongo_active' => isset($location['active']) ? (bool) $location['active'] : false,
                'online' => isset($location['online']) ? (bool) $location['online'] : false,
                'device_id' => $location['device_id'] ?? null,
                'speed' => isset($location['speed']) ? (float) $location['speed'] : 0,
                'rpm' => isset($location['rpm']) ? (int) $location['rpm'] : 0,
                'engine_temp' => isset($location['engine_temp']) ? (float) $location['engine_temp'] : 0,
                'status' => (isset($location['active']) && $location['active']) ? 'running' : ($reservation ? 'reserved' : 'available'),
            ];
        })
        ->filter(fn($v) => $v['latitude'] !== null && $v['longitude'] !== null)
        ->values();

        return response()->json($result);
    }

    /**
     * Endpoint PUBLICO para ver vehiculos disponibles en el mapa (sin autenticacion).
     * Solo muestra vehiculos con active=false en PostgreSQL (disponibles para reservar).
     */
    public function publicMap(): JsonResponse
    {
        $vehicles = Vehicle::where('active', false)->get();
        $locations = $this->locationService->getLocations();

        $result = $vehicles->map(function ($vehicle) use ($locations) {
            $location = $locations[$vehicle->license_plate] ?? null;

            return [
                'id' => $vehicle->id,
                'plate' => $vehicle->license_plate,
                'brand' => $vehicle->brand,
                'model' => $vehicle->model,
                'latitude' => isset($location['latitude']) ? (float)$location['latitude'] : null,
                'longitude' => isset($location['longitude']) ? (float)$location['longitude'] : null,
                'available' => true,
            ];
        })
        ->filter(fn($v) => $v['latitude'] !== null && $v['longitude'] !== null)
        ->values();

        return response()->json($result);
    }
}
