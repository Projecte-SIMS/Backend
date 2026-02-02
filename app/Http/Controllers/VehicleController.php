<?php

namespace App\Http\Controllers;

use App\Models\Vehicle;
use App\Http\Requests\Vehicle\StoreVehicleRequest;
use App\Http\Requests\Vehicle\UpdateVehicleRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class VehicleController extends Controller
{
    /**
     * Paginated list of vehicles with filters
     * 
     * Available filters:
     * - search: search by license_plate, brand or model
     * - license_plate: exact filter by license plate
     * - brand: filter by brand
     * - model: filter by model
     * - active: filter by active status (true/false)
     */
    public function index(Request $request): JsonResponse
    {
        $query = Vehicle::query();

        // General search by license_plate, brand or model
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('license_plate', 'ILIKE', "%{$search}%")
                  ->orWhere('brand', 'ILIKE', "%{$search}%")
                  ->orWhere('model', 'ILIKE', "%{$search}%");
            });
        }

        // Specific filters
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
     * Create a new vehicle
     */
    public function store(StoreVehicleRequest $request): JsonResponse
    {
        $vehicle = Vehicle::create($request->validated());

        return response()->json([
            'message' => 'Vehicle created successfully',
            'data' => $vehicle,
        ], 201);
    }

    /**
     * Show a specific vehicle
     */
    public function show(Vehicle $vehicle): JsonResponse
    {
        return response()->json([
            'data' => $vehicle,
        ]);
    }

    /**
     * Update a vehicle
     */
    public function update(UpdateVehicleRequest $request, Vehicle $vehicle): JsonResponse
    {
        $vehicle->update($request->validated());

        return response()->json([
            'message' => 'Vehicle updated successfully',
            'data' => $vehicle,
        ]);
    }

    /**
     * Delete a vehicle (soft delete)
     */
    public function destroy(Vehicle $vehicle): JsonResponse
    {
        $vehicle->delete();

        return response()->json([
            'message' => 'Vehicle deleted successfully',
        ]);
    }
}
