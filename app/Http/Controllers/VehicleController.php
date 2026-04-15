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
        
        // Determinar si es una petición de administración
        $isAdminRequest = $request->is('api/admin/*') || $request->is('admin/*');
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

        // Filtro por estado activo (solo si se proporciona)
        if ($request->has('active') && $request->input('active') !== '') {
            $query->where('active', filter_var($request->input('active'), FILTER_VALIDATE_BOOLEAN));
        }

        // Si no es admin viendo la lista completa, solo mostramos vehículos disponibles (active=false)
        if (!$shouldShowAll) {
            $query->where('active', false);
        }

        // Obtener todos los vehículos que cumplen los filtros básicos de base de datos
        // Usamos get() en lugar de paginate() inicialmente para poder filtrar por el estado dinámico (IoT/Reservas)
        $vehiclesList = $query->orderBy('created_at', 'desc')->get();

        $locations = $this->locationService->getLocations();
        
        // Reservas para filtrado
        $currentReservations = Reservation::whereIn('status', ['pending', 'active'])
            ->get()
            ->groupBy('vehicle_id');

        // Transformar la colección para calcular estados dinámicos
        $transformedVehicles = $vehiclesList->map(function ($vehicle) use ($locations, $currentReservations, $user) {
            $licensePlate = (string) $vehicle->license_plate;
            $location = $locations[$licensePlate] ?? null;
            $reservation = $currentReservations->get($vehicle->id)?->first();
            
            $isOnline = (bool) ($location['online'] ?? false);
            $hasGPS = isset($location['latitude']) && isset($location['longitude']) && 
                      ((float)$location['latitude'] != 0 || (float)$location['longitude'] != 0);
            
            $userId = $user ? $user->id : null;
            $isMine = $reservation && $userId && $reservation->user_id == $userId;
            $isOccupied = ($location['active'] ?? false) === true;

            // Nuevo cálculo de status más preciso
            if ($isOccupied) {
                $status = 'running';
            } elseif ($reservation) {
                $status = 'reserved';
            } elseif (!$isOnline || !$hasGPS) {
                $status = 'offline';
            } else {
                $status = 'available';
            }

            $vehicle->setAttribute('latitude', isset($location['latitude']) ? (float)$location['latitude'] : null);
            $vehicle->setAttribute('longitude', isset($location['longitude']) ? (float)$location['longitude'] : null);
            $vehicle->setAttribute('mongo_active', $isOccupied);
            $vehicle->setAttribute('online', $isOnline);
            $vehicle->setAttribute('status', $status);
            $vehicle->setAttribute('is_mine', (bool) $isMine);
            $vehicle->setAttribute('iot_device_id', $location['device_id'] ?? null);
            
            return $vehicle;
        });

        // Aplicar filtro de estado dinámico si se solicita
        if ($request->filled('status')) {
            $statusFilter = $request->input('status');
            $transformedVehicles = $transformedVehicles->filter(function($v) use ($statusFilter) {
                return $v->getAttribute('status') === $statusFilter;
            });
        }

        if (!$shouldShowAll) {
            // Para clientes: Solo disponibles O el mío reservado, Y QUE TENGAN RELACIÓN EN MONGO (y estén ONLINE)
            $transformedVehicles = $transformedVehicles->filter(function ($v) {
                // Consideramos que tiene relación válida si tiene coordenadas distintas de 0 y está online
                $hasValidLocation = $v->getAttribute('latitude') !== null && $v->getAttribute('longitude') !== null && 
                                   ($v->getAttribute('latitude') != 0 || $v->getAttribute('longitude') != 0);
                $isOnline = $v->getAttribute('online') === true;
                $isAvailable = $v->getAttribute('status') === 'available' || ($v->getAttribute('status') === 'reserved' && $v->getAttribute('is_mine'));
                
                return $hasValidLocation && $isOnline && $isAvailable;
            });
        }

        // Paginar manualmente la colección resultante
        $perPage = (int) $request->input('per_page', 15);
        $page = max(1, (int) $request->input('page', 1));
        $total = $transformedVehicles->count();
        
        $results = $transformedVehicles->slice(($page - 1) * $perPage, $perPage)->values();

        return response()->json([
            'data' => $results,
            'current_page' => $page,
            'last_page' => max(1, (int) ceil($total / $perPage)),
            'per_page' => $perPage,
            'total' => $total
        ]);
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

        // Intentar obtener información de IoT basada en la matrícula
        $locations = $this->locationService->getLocations();
        $location = $locations[$vehicle->license_plate] ?? null;

        if ($location) {
            $vehicle->setAttribute('iot_device_id', $location['device_id'] ?? null);
            $vehicle->setAttribute('online', (bool) ($location['online'] ?? false));
            $vehicle->setAttribute('mongo_active', (bool) ($location['active'] ?? false));
            $vehicle->setAttribute('latitude', (float) ($location['latitude'] ?? null));
            $vehicle->setAttribute('longitude', (float) ($location['longitude'] ?? null));
        }

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
            
            $isReservedByMe = $reservation && $user && $reservation->user_id === $user->id;
            $isReservedByOthers = $reservation && (!$user || $reservation->user_id !== $user->id);
            
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
                'online' => (bool) ($location['online'] ?? false),
                'iot_device_id' => $location['device_id'] ?? null,
                'is_mine' => $isReservedByMe,
                'status' => $isOccupied ? 'running' : ($isPending ? 'reserved' : 'available'),
                'available' => !$isReservedByOthers && !$isOccupied && (bool) ($location['online'] ?? false)
            ];
        })
        // Filtro para el mapa de cliente:
        // 1. Mostrar vehículos LIBRES para todos (solo si están online).
        // 2. Mostrar vehículos RESERVADOS solo si son del usuario actual (solo si están online).
        // 3. OCULTAR vehículos en marcha (running) para todos.
        ->filter(function($v) {
            $hasLocation = $v['latitude'] !== null && $v['longitude'] !== null && 
                          ($v['latitude'] != 0 || $v['longitude'] != 0);
            $isOnline = $v['online'] === true;
            $isCorrectStatus = ($v['status'] === 'available' || ($v['status'] === 'reserved' && $v['is_mine']));
            
            return $hasLocation && $isOnline && $isCorrectStatus;
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
                'battery_voltage' => isset($location['battery_voltage']) ? (float) $location['battery_voltage'] : 12.6,
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
            $online = (bool) ($location['online'] ?? false);

            return [
                'id' => $vehicle->id,
                'plate' => $vehicle->license_plate,
                'brand' => $vehicle->brand,
                'model' => $vehicle->model,
                'latitude' => isset($location['latitude']) ? (float)$location['latitude'] : null,
                'longitude' => isset($location['longitude']) ? (float)$location['longitude'] : null,
                'online' => $online,
                'available' => $online,
            ];
        })
        ->filter(function($v) {
            $hasLocation = $v['latitude'] !== null && $v['longitude'] !== null && 
                          ($v['latitude'] != 0 || $v['longitude'] != 0);
            return $hasLocation && $v['online'] === true;
        })
        ->values();

        return response()->json($result);
    }
}
