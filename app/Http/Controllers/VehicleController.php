<?php

namespace App\Http\Controllers;

use App\Models\Vehicle;
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
        $query = Vehicle::query();
        $user = auth()->user();
        
        // Detección mejorada
        $isAdminUser = $user && ($user->hasRole('Admin') || $user->hasRole('admin'));
        $isAdminRoute = $request->is('api/admin/*') || $request->is('admin/*');
        
        // Solo mostramos todo si es un admin en una ruta de administración
        $shouldShowAll = $isAdminUser && $isAdminRoute;

        // Búsqueda general por license_plate, brand o model
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('license_plate', 'ILIKE', "%{$search}%")
                  ->orWhere('brand', 'ILIKE', "%{$search}%")
                  ->orWhere('model', 'ILIKE', "%{$search}%");
            });
        }

        // Filtros específicos
        if ($request->filled('license_plate')) {
            $query->where('license_plate', 'ILIKE', "%{$request->input('license_plate')}%");
        }

        if ($request->filled('brand')) {
            $query->where('brand', 'ILIKE', "%{$request->input('brand')}%");
        }

        if ($request->filled('model')) {
            $query->where('model', 'ILIKE', "%{$request->input('model')}%");
        }

        if (!$shouldShowAll) {
            // Cliente (o admin en vista pública): solo mostrar vehículos operativos (active=false en postgres)
            $query->where('active', false);
        } else {
            // Admin: puede filtrar por cualquier estado si lo desea (pero por defecto ve todos)
            if ($request->has('active')) {
                $query->where('active', filter_var($request->input('active'), FILTER_VALIDATE_BOOLEAN));
            }
        }

        $vehicles = $query->orderBy('created_at', 'desc')
                          ->paginate($request->input('per_page', 15));

        $locations = $this->locationService->getLocations();

        // Adjuntamos datos de telemetría de MongoDB
        $vehicles->getCollection()->transform(function ($vehicle) use ($locations) {
            $location = $locations[$vehicle->license_plate] ?? null;
            $vehicle->setAttribute('latitude', $location['latitude'] ?? null);
            $vehicle->setAttribute('longitude', $location['longitude'] ?? null);
            $vehicle->setAttribute('mongo_active', $location['active'] ?? null);
            return $vehicle;
        });

        // Para clientes: filtrar adicionalmente por disponibilidad real en MongoDB
        if (!$shouldShowAll) {
            $filtered = $vehicles->getCollection()->filter(function ($vehicle) {
                // En vista de cliente, solo mostramos los que no tienen sesión activa
                return $vehicle->mongo_active === false;
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
     * Obtener vehículos con ubicaciones para el mapa (solo disponibles para clientes)
     * Solo muestra vehículos con active=false en PostgreSQL y mongo_active=false (no en uso)
     */
    public function map(): JsonResponse // /vehicles/map para cliente
    {
        $this->authorize('viewAny', Vehicle::class);

        $vehicles = Vehicle::where('active', false)->get();
        $locations = $this->locationService->getLocations();

        $result = $vehicles->map(function ($vehicle) use ($locations) {
            $location = $locations[$vehicle->license_plate] ?? null;

            return [
                'id' => $vehicle->id,
                'plate' => $vehicle->license_plate,
                'brand' => $vehicle->brand,
                'model' => $vehicle->model,
                'postgres_active' => (bool) $vehicle->active,
                'mongo_active' => $location['active'] ?? null,
                'latitude' => $location['latitude'] ?? null,
                'longitude' => $location['longitude'] ?? null,
            ];
        })
        // Solo vehículos con coordenadas, postgres_active=false y mongo_active=false (disponibles)
        ->filter(fn($v) => $v['latitude'] !== null && $v['longitude'] !== null && $v['mongo_active'] === false)->values();

        return response()->json($result);
    }


    /**
     * Obtener vehículos para admin (incluye inactivos y datos extra)
     */
    public function adminMap(): JsonResponse
    {
        $this->authorize('viewAny', Vehicle::class);

        $vehicles = Vehicle::all();
        $locations = $this->locationService->getLocations();

        $result = $vehicles->map(function ($vehicle) use ($locations) {
            $location = $locations[$vehicle->license_plate] ?? null;

            return [
                'id' => (int) $vehicle->id,
                'plate' => (string) $vehicle->license_plate,
                'brand' => (string) $vehicle->brand,
                'model' => (string) $vehicle->model,
                'latitude' => isset($location['latitude']) ? (float) $location['latitude'] : null,
                'longitude' => isset($location['longitude']) ? (float) $location['longitude'] : null,
                'postgres_active' => (bool) $vehicle->active,
                'mongo_active' => isset($location['active']) ? (bool) $location['active'] : false,
                'online' => isset($location['online']) ? (bool) $location['online'] : false,
                'device_id' => $location['device_id'] ?? null,
                'speed' => isset($location['speed']) ? (float) $location['speed'] : 0,
                'rpm' => isset($location['rpm']) ? (int) $location['rpm'] : 0,
                'engine_temp' => isset($location['engine_temp']) ? (float) $location['engine_temp'] : 0,
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
                'latitude' => $location['latitude'] ?? null,
                'longitude' => $location['longitude'] ?? null,
                'available' => true,
            ];
        })
        ->filter(fn($v) => $v['latitude'] !== null && $v['longitude'] !== null)
        ->values();

        return response()->json($result);
    }
}
