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
    public function __construct(
        private VehicleLocationService $locationService
    ) {}

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

        if ($request->has('active')) {
            $query->where('active', filter_var($request->input('active'), FILTER_VALIDATE_BOOLEAN));
        }

        $vehicles = $query->orderBy('created_at', 'desc')
                          ->paginate($request->input('per_page', 15));

        return response()->json($vehicles);
    }

    /**
     * Crear un nuevo vehículo
     */
    public function store(StoreVehicleRequest $request): JsonResponse
    {
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
        return response()->json([
            'data' => $vehicle,
        ]);
    }

    /**
     * Actualizar un vehículo
     */
    public function update(UpdateVehicleRequest $request, Vehicle $vehicle): JsonResponse
    {
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
        $vehicle->delete();

        return response()->json([
            'message' => 'Vehículo eliminado correctamente',
        ]);
    }

    /**
     * Obtener vehículos con ubicaciones para el mapa
     */
    public function map(): JsonResponse
    {
        $vehicles = Vehicle::all();
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
                'status' => $vehicle->active ? 'active' : 'inactive',
            ];
        })->filter(fn($v) => $v['latitude'] !== null && $v['longitude'] !== null)->values();

        return response()->json($result);
    }
}
