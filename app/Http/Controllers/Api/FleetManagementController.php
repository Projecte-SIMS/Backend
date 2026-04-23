<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CentralDevice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Process;

class FleetManagementController extends Controller
{
    public function discover()
    {
        try {
            $iotUrl = config('services.iot.url', env('IOT_MICROSERVICE_URL', 'http://127.0.0.1:8001'));
            $apiKey = config('services.iot.api_key', env('IOT_API_KEY', 'MACMECMIC'));
            
            \Log::info("SuperAdmin discovering IoT devices at: {$iotUrl}");

            $response = \Illuminate\Support\Facades\Http::withHeaders([
                'x-api-key' => $apiKey,
                'Accept' => 'application/json'
            ])->timeout(5)->get("{$iotUrl}/api/central/devices", [
                'only_online' => 1
            ]);

            if ($response->successful()) {
                $discovered = $response->json();
                \Log::info("Discovered " . count($discovered) . " devices");
                
                $registeredHardwareIds = CentralDevice::pluck('hardware_id')->toArray();
                
                $data = collect($discovered)->map(function($d) use ($registeredHardwareIds) {
                    $d['is_registered'] = in_array($d['hardware_id'], $registeredHardwareIds);
                    return $d;
                });

                return response()->json([
                    'success' => true,
                    'data' => $data
                ]);
            }

            \Log::error("IoT Server error: " . $response->status() . " - " . $response->body());
            return response()->json([
                'success' => false, 
                'message' => 'Error contactando con servidor IoT (Status: ' . $response->status() . ')',
                'url_tried' => $iotUrl
            ], 502);
        } catch (\Exception $e) {
            \Log::error("Fleet discovery Exception: " . $e->getMessage());
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function index()
    {
        return response()->json([
            'success' => true,
            'data' => CentralDevice::all()
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'hardware_id' => 'required|string|unique:central_devices',
            'display_name' => 'required|string',
            'ip_address' => 'required|string',
            'ssh_user' => 'string',
            'tenant_id' => 'nullable|string',
            'api_key' => 'nullable|string',
            'use_docker' => 'boolean'
        ]);

        $device = CentralDevice::create($data);

        // Intentar sincronizar status inmediatamente de forma silenciosa
        try {
            $this->executeAction(new Request(['action' => 'status']), $device->id);
            $device->refresh();
        } catch (\Exception $e) {
            \Log::warning("Initial status sync failed for device {$device->id}: " . $e->getMessage());
        }

        return response()->json([
            'success' => true,
            'data' => $device
        ], 201);
    }

    public function executeAction(Request $request, $id)
    {
        \Log::info("Executing fleet action", ['id' => $id, 'action' => $request->input('action')]);
        $device = CentralDevice::findOrFail($id);
        $action = $request->input('action'); // deploy, status, reboot, update-keys

        // Validar acción
        if (!in_array($action, ['deploy', 'status', 'reboot', 'update-keys'])) {
            return response()->json(['success' => false, 'message' => 'Acción no válida'], 400);
        }

        // Si es status, intentar obtenerlo del servidor IoT primero (fuente de verdad de WebSockets)
        if ($action === 'status') {
            try {
                $iotUrl = config('services.iot.url', env('IOT_MICROSERVICE_URL', 'http://127.0.0.1:8001'));
                $apiKey = config('services.iot.api_key', env('IOT_API_KEY', 'MACMECMIC'));
                
                $response = \Illuminate\Support\Facades\Http::withHeaders(['x-api-key' => $apiKey])
                    ->get("{$iotUrl}/api/central/devices");

                if ($response->successful()) {
                    $allDevices = $response->json();
                    
                    // Log para depuración
                    \Log::info("Fleet sync debug", [
                        'searching_for' => $device->hardware_id,
                        'available_in_iot' => collect($allDevices)->map(fn($d) => [
                            'hw' => $d['hardware_id'] ?? 'null',
                            'online' => $d['online'] ?? false
                        ])->toArray()
                    ]);

                    $thisDevice = collect($allDevices)->firstWhere('hardware_id', $device->hardware_id);
                    
                    if ($thisDevice) {
                        // ... lógica de online/active ...
                        $isOnline = $thisDevice['online'] ?? false;
                        $isActive = $thisDevice['active'] ?? false;

                        $statusText = $isOnline ? 'Online' : 'Offline';
                        if ($isOnline && $isActive) {
                            $statusText .= ' | Active';
                        } elseif ($isOnline) {
                            $statusText .= ' | Idle';
                        }

                        $device->last_status = $statusText;
                        $device->last_sync_at = now();
                        $device->save();

                        return response()->json([
                            'success' => true,
                            'output' => "Status sync: " . $statusText,
                            'device' => $device
                        ]);
                    } else {
                        $available = collect($allDevices)->pluck('hardware_id')->implode(', ');
                        return response()->json([
                            'success' => true,
                            'output' => "Device not found in IoT network. Available IDs: [" . ($available ?: 'None') . "]. Searching for: [" . $device->hardware_id . "]"
                        ]);
                    }
                }
            } catch (\Exception $e) {
                \Log::warning("IoT status fallback failed: " . $e->getMessage());
            }
        }

        // Ruta al script de Python (ajustada al entorno)
        // Intentar detectar si estamos en Docker o Host
        $scriptPath = file_exists(base_path('../Raspberry_py/fleet_manager.py')) 
            ? base_path('../Raspberry_py/fleet_manager.py')
            : '/var/www/html/Raspberry_py/fleet_manager.py'; // Posible mount point

        $pythonPath = 'python3'; // Usar el del sistema/contenedor
        
        // Crear un inventario temporal para este dispositivo único
        $inventory = [
            [
                'id' => $device->hardware_id,
                'ip' => $device->ip_address,
                'user' => $device->ssh_user,
                'tenant_id' => $device->tenant_id ?? 'default',
                'api_key' => $device->api_key ?? 'MACMECMIC',
                'use_docker' => (bool)$device->use_docker,
                'server_ws' => 'ws://192.168.65.1:8001' // IP del host para el agente
            ]
        ];
        
        $inventoryPath = storage_path("app/inventory_{$device->id}.json");
        file_put_contents($inventoryPath, json_encode($inventory));

        // Ejecutar comando
        $command = "{$pythonPath} {$scriptPath} {$action} --inventory {$inventoryPath}";
        
        $result = Process::run($command);

        // Limpiar
        @unlink($inventoryPath);

        if ($result->successful()) {
            // Si es status, actualizar en DB
            if ($action === 'status') {
                $device->last_status = $result->output();
                $device->last_sync_at = now();
                $device->save();
            }

            return response()->json([
                'success' => true,
                'output' => $result->output(),
                'error' => $result->errorOutput()
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Error al ejecutar acción',
            'output' => $result->output(),
            'error' => $result->errorOutput()
        ], 500);
    }

    public function destroy($id)
    {
        $device = CentralDevice::findOrFail($id);
        $device->delete();
        return response()->json(['success' => true]);
    }
}
