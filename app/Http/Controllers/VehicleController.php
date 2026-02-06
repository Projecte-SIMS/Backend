<?php

namespace App\Http\Controllers;

use App\Models\Vehicle;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class VehicleController extends Controller
{

    public function index(Request $request): JsonResponse
    {
        $user = Auth::user();
        $query = Vehicle::query();

        if (!$user->can('can.view.all.vehicles')) {
            $query->where('active', true);
        } else {
            if ($request->has('active')) {
                $query->where('active', filter_var($request->input('active'), FILTER_VALIDATE_BOOLEAN));
            }
        }


        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('license_plate', 'LIKE', "%{$search}%")
                  ->orWhere('brand', 'LIKE', "%{$search}%")
                  ->orWhere('model', 'LIKE', "%{$search}%");
            });
        }


        if ($request->filled('license_plate')) {
            $query->where('license_plate', 'LIKE', "%{$request->input('license_plate')}%");
        }
        if ($request->filled('brand')) {
            $query->where('brand', 'LIKE', "%{$request->input('brand')}%");
        }
        if ($request->filled('model')) {
            $query->where('model', 'LIKE', "%{$request->input('model')}%");
        }

        $vehicles = $query->orderBy('created_at', 'desc')
                          ->paginate($request->input('per_page', 15));

        return response()->json($vehicles);
    }


    public function store(Request $request): JsonResponse
    {
        $this->authorize('create', Vehicle::class);

        $data = $request->validate([
            'license_plate' => 'required|string|unique:vehicles,license_plate',
            'brand' => 'required|string',
            'model' => 'required|string',
            'active' => 'boolean',
            'image_url' => 'nullable|url'
        ]);

        $vehicle = Vehicle::create($data);

        return response()->json([
            'message' => 'Vehicle created successfully',
            'data' => $vehicle,
        ], 201);
    }


    public function show(Vehicle $vehicle): JsonResponse
    {
        $user = Auth::user();

        if (!$vehicle->active && !$user->can('can.view.all.vehicles')) {
            return response()->json(['message' => 'Vehicle not found or unavailable'], 404);
        }

        return response()->json(['data' => $vehicle]);
    }


    public function update(Request $request, Vehicle $vehicle): JsonResponse
    {
        $this->authorize('update', $vehicle);


        $data = $request->validate([
            'license_plate' => 'sometimes|string|unique:vehicles,license_plate,' . $vehicle->id,
            'brand' => 'sometimes|string',
            'model' => 'sometimes|string',
            'active' => 'sometimes|boolean',
            'image_url' => 'nullable|url'
        ]);

        $vehicle->update($data);

        return response()->json([
            'message' => 'Vehicle updated successfully',
            'data' => $vehicle,
        ]);
    }


    public function destroy(Vehicle $vehicle): JsonResponse
    {
        $this->authorize('delete', $vehicle);


        if ($vehicle->reservations()->whereIn('status', ['pending', 'active'])->exists()) {
             return response()->json([
                 'message' => 'Cannot delete vehicle with active reservations.'
             ], 409);
        }

        $vehicle->delete();

        return response()->json(['message' => 'Vehicle deleted successfully']);
    }
}