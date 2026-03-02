<?php

namespace App\Http\Controllers;

use App\Models\Vehicle;
use App\Services\VehicleLocationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Controlador para operaciones IoT de vehículos.
 * Permite enviar comandos a los dispositivos (encender/apagar).
 */
class IoTController extends Controller
{
    private VehicleLocationService $iotService;

    public function __construct(VehicleLocationService $iotService)
    {
        $this->iotService = $iotService;
    }

    /**
     * Lista todos los dispositivos IoT con su estado.
     */
    public function devices(): JsonResponse
    {
        $devices = $this->iotService->getAllDevices();
        return response()->json($devices);
    }

    /**
     * Obtiene el estado de un dispositivo específico.
     */
    public function device(string $deviceId): JsonResponse
    {
        $device = $this->iotService->getDevice($deviceId);

        if (!$device) {
            return response()->json(['message' => 'Device not found'], 404);
        }

        return response()->json($device);
    }

    /**
     * Enciende un vehículo.
     */
    public function turnOn(Request $request, string $deviceId): JsonResponse
    {
        $result = $this->iotService->turnOn($deviceId);

        if (!$result['success']) {
            return response()->json([
                'message' => 'Failed to turn on vehicle',
                'error' => $result['error'] ?? 'Unknown error'
            ], 500);
        }

        return response()->json([
            'message' => 'Vehicle turn on command sent',
            'result' => $result['result']
        ]);
    }

    /**
     * Apaga un vehículo.
     */
    public function turnOff(Request $request, string $deviceId): JsonResponse
    {
        $result = $this->iotService->turnOff($deviceId);

        if (!$result['success']) {
            return response()->json([
                'message' => 'Failed to turn off vehicle',
                'error' => $result['error'] ?? 'Unknown error'
            ], 500);
        }

        return response()->json([
            'message' => 'Vehicle turn off command sent',
            'result' => $result['result']
        ]);
    }

    /**
     * Envía un comando genérico a un dispositivo.
     */
    public function sendCommand(Request $request, string $deviceId): JsonResponse
    {
        $request->validate([
            'action' => 'required|string|in:on,off,reboot',
            'relay' => 'integer|min:0|max:1'
        ]);

        $result = $this->iotService->sendCommand(
            $deviceId,
            $request->input('action'),
            $request->input('relay', 0)
        );

        if (!$result['success']) {
            return response()->json([
                'message' => 'Command failed',
                'error' => $result['error'] ?? 'Unknown error'
            ], 500);
        }

        return response()->json([
            'message' => 'Command sent successfully',
            'result' => $result['result']
        ]);
    }

    /**
     * Verifica el estado del microservicio IoT.
     */
    public function health(): JsonResponse
    {
        $isHealthy = $this->iotService->healthCheck();

        return response()->json([
            'microservice' => $isHealthy ? 'online' : 'offline',
            'ok' => $isHealthy
        ], $isHealthy ? 200 : 503);
    }

    /**
     * Verifica si un dispositivo específico está online.
     */
    public function ping(string $deviceId): JsonResponse
    {
        $isOnline = $this->iotService->isDeviceOnline($deviceId);

        return response()->json([
            'device_id' => $deviceId,
            'online' => $isOnline
        ]);
    }

    /**
     * Vincula un dispositivo IoT a un vehículo existente.
     * Actualiza la matrícula del dispositivo en el microservicio.
     */
    public function linkToVehicle(Request $request, string $deviceId): JsonResponse
    {
        $request->validate([
            'vehicle_id' => 'required|exists:vehicles,id'
        ]);

        $vehicle = Vehicle::findOrFail($request->input('vehicle_id'));
        
        $result = $this->iotService->updateDevicePlate($deviceId, $vehicle->license_plate);

        if (!$result['success']) {
            return response()->json([
                'message' => 'Failed to link device to vehicle',
                'error' => $result['error'] ?? 'Unknown error'
            ], 500);
        }

        return response()->json([
            'message' => 'Device linked to vehicle successfully',
            'device_id' => $deviceId,
            'vehicle_id' => $vehicle->id,
            'license_plate' => $vehicle->license_plate
        ]);
    }

    /**
     * Lista dispositivos IoT no vinculados (con matrícula AUTO-*).
     * Útil para ver qué dispositivos necesitan vincularse.
     */
    public function unlinkedDevices(): JsonResponse
    {
        $devices = $this->iotService->getAllDevices();
        
        $unlinked = array_filter($devices, function($d) {
            $plate = $d['license_plate'] ?? '';
            return str_starts_with($plate, 'AUTO-');
        });

        return response()->json(array_values($unlinked));
    }

    /**
     * Lista vehículos disponibles para vincular (que no tienen dispositivo IoT asignado).
     */
    public function availableVehicles(): JsonResponse
    {
        $devices = $this->iotService->getAllDevices();
        $linkedPlates = array_map(fn($d) => $d['license_plate'] ?? '', $devices);
        
        $vehicles = Vehicle::whereNotIn('license_plate', $linkedPlates)
            ->get(['id', 'license_plate', 'brand', 'model']);

        return response()->json($vehicles);
    }
}
