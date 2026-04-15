<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Servicio para comunicación con el microservicio IoT (FastAPI).
 * Centraliza todas las operaciones relacionadas con telemetría y comandos de vehículos.
 * 
 * MODO HÍBRIDO: Si el microservicio no está disponible, devuelve array vacío
 * y el controlador usa solo datos de PostgreSQL.
 */
class VehicleLocationService
{
    private string $baseUrl;
    private string $apiKey;
    private int $timeout;
    private bool $microserviceAvailable = true;

    public function __construct()
    {
        $this->baseUrl = rtrim(env('IOT_MICROSERVICE_URL', 'http://localhost:8001'), '/');
        $this->apiKey = env('IOT_API_KEY', 'MACMECMIC');
        $this->timeout = (int) env('IOT_TIMEOUT', 5); // Reducido a 5s para no bloquear
    }

    /**
     * Obtiene el ID del inquilino actual.
     * Si no hay inquilino (consola/central), usa 'default'.
     */
    private function getTenantId(): string
    {
        try {
            if (function_exists('tenant') && tenant('id')) {
                return (string) tenant('id');
            }
        } catch (\Exception $e) {
            Log::debug('IoT: Could not determine tenant, using default');
        }
        return 'default';
    }

    /**
     * Obtiene las ubicaciones de todos los vehículos desde el microservicio IoT.
     * Devuelve un array indexado por license_plate.
     * Si el microservicio no está disponible, devuelve array vacío.
     */
    public function getLocations(): array
    {
        $tenantId = $this->getTenantId();
        try {
            $url = "{$this->baseUrl}/api/{$tenantId}/devices";
            $response = Http::timeout($this->timeout)
                ->withHeaders(['x-api-key' => $this->apiKey])
                ->get($url);

            if (!$response->successful()) {
                Log::warning('IoT Microservice error', [
                    'url' => $url,
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
                $this->microserviceAvailable = false;
                return [];
            }
                $this->microserviceAvailable = false;
                return [];
            }

            $this->microserviceAvailable = true;
            $devices = $response->json();
            
            if (!is_array($devices)) {
                return [];
            }
            
            $result = [];

            foreach ($devices as $device) {
                $licensePlate = $device['license_plate'] ?? $device['identity']['license_plate'] ?? null;
                $telemetry = $device['telemetry'] ?? [];
                $status = $device['status'] ?? [];

                if ($licensePlate) {
                    $result[$licensePlate] = [
                        'device_id' => $device['id'] ?? $device['_id'] ?? null,
                        'latitude' => (float) ($telemetry['latitude'] ?? 0),
                        'longitude' => (float) ($telemetry['longitude'] ?? 0),
                        'active' => (bool) ($status['active'] ?? false),
                        'online' => (bool) ($device['online'] ?? false),
                        'speed' => (float) ($telemetry['speed'] ?? 0),
                        'rpm' => (int) ($telemetry['rpm'] ?? 0),
                        'engine_temp' => (float) ($telemetry['engine_temp'] ?? 0),
                        'battery_voltage' => (float) ($telemetry['battery_voltage'] ?? 0),
                    ];
                }
            }

            return $result;
        } catch (\Exception $e) {
            Log::warning('IoT Microservice not available', [
                'error' => $e->getMessage(),
            ]);
            $this->microserviceAvailable = false;
            return [];
        }
    }

    /**
     * Verifica si el microservicio está disponible.
     */
    public function isMicroserviceAvailable(): bool
    {
        return $this->microserviceAvailable;
    }

    /**
     * Obtiene todos los dispositivos IoT con información completa.
     */
    public function getAllDevices(): array
    {
        $tenantId = $this->getTenantId();
        try {
            $response = Http::timeout($this->timeout)
                ->withHeaders(['x-api-key' => $this->apiKey])
                ->get("{$this->baseUrl}/api/{$tenantId}/devices");

            if (!$response->successful()) {
                return [];
            }

            return $response->json() ?? [];
        } catch (\Exception $e) {
            Log::warning('IoT: Failed to get devices', ['error' => $e->getMessage()]);
            return [];
        }
    }

    /**
     * Obtiene un dispositivo específico por su ID.
     */
    public function getDevice(string $deviceId): ?array
    {
        $tenantId = $this->getTenantId();
        try {
            $response = Http::timeout($this->timeout)
                ->withHeaders(['x-api-key' => $this->apiKey])
                ->get("{$this->baseUrl}/api/{$tenantId}/devices/{$deviceId}");

            if (!$response->successful()) {
                return null;
            }

            return $response->json();
        } catch (\Exception $e) {
            Log::warning('IoT: Failed to get device', [
                'device_id' => $deviceId,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    /**
     * Obtiene la ubicación de un vehículo específico por matrícula.
     */
    public function getLocationByPlate(string $licensePlate): ?array
    {
        $locations = $this->getLocations();
        return $locations[$licensePlate] ?? null;
    }

    /**
     * Envía un comando al vehículo (encender/apagar).
     */
    public function sendCommand(string $deviceId, string $action, int $relay = 0): array
    {
        $tenantId = $this->getTenantId();
        try {
            $response = Http::timeout($this->timeout)
                ->withHeaders([
                    'x-api-key' => $this->apiKey,
                    'Content-Type' => 'application/json',
                ])
                ->post("{$this->baseUrl}/api/{$tenantId}/command", [
                    'device_id' => $deviceId,
                    'action' => $action,
                    'relay' => $relay,
                ]);

            if (!$response->successful()) {
                Log::error('IoT: Command failed', [
                    'device_id' => $deviceId,
                    'action' => $action,
                    'status' => $response->status(),
                ]);
                return [
                    'success' => false,
                    'error' => 'Command failed: ' . $response->body(),
                    'status' => $response->status()
                ];
            }

            $result = $response->json();
            Log::info('IoT: Command sent', [
                'device_id' => $deviceId,
                'action' => $action,
            ]);

            return [
                'success' => true,
                'result' => $result['result'] ?? 'sent',
            ];
        } catch (\Exception $e) {
            Log::error('IoT: Command exception', [
                'device_id' => $deviceId,
                'action' => $action,
                'error' => $e->getMessage()
            ]);
            return [
                'success' => false,
                'error' => 'Connection failed: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Obtiene el historial de ruta de un dispositivo.
     */
    public function getRoute(string $deviceId): array
    {
        $tenantId = $this->getTenantId();
        try {
            $response = Http::timeout($this->timeout)
                ->withHeaders(['x-api-key' => $this->apiKey])
                ->get("{$this->baseUrl}/api/{$tenantId}/devices/{$deviceId}/route");

            if (!$response->successful()) {
                return [];
            }

            return $response->json() ?? [];
        } catch (\Exception $e) {
            Log::warning('IoT: Failed to get route', ['error' => $e->getMessage()]);
            return [];
        }
    }

    /**
     * Limpia el historial de ruta de un dispositivo.
     */
    public function clearRoute(string $deviceId): bool
    {
        $tenantId = $this->getTenantId();
        try {
            $response = Http::timeout($this->timeout)
                ->withHeaders(['x-api-key' => $this->apiKey])
                ->post("{$this->baseUrl}/api/{$tenantId}/devices/{$deviceId}/route/clear");

            return $response->successful();
        } catch (\Exception $e) {
            Log::warning('IoT: Failed to clear route', ['error' => $e->getMessage()]);
            return false;
        }
    }

    /**
     * Enciende un vehículo.
     */
    public function turnOn(string $deviceId): array
    {
        return $this->sendCommand($deviceId, 'on');
    }

    /**
     * Apaga un vehículo.
     */
    public function turnOff(string $deviceId): array
    {
        return $this->sendCommand($deviceId, 'off');
    }

    /**
     * Verifica si el microservicio está disponible (health check).
     */
    public function healthCheck(): bool
    {
        try {
            $response = Http::timeout(3)
                ->get("{$this->baseUrl}/health");

            return $response->successful();
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Verifica si un dispositivo específico está online.
     */
    public function isDeviceOnline(string $deviceId): bool
    {
        $tenantId = $this->getTenantId();
        try {
            $response = Http::timeout(3)
                ->withHeaders(['x-api-key' => $this->apiKey])
                ->get("{$this->baseUrl}/api/{$tenantId}/ping/{$deviceId}");

            return $response->successful() && ($response->json()['online'] ?? false);
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Actualiza la matrícula de un dispositivo IoT.
     * Usado para vincular un dispositivo a un vehículo de PostgreSQL.
     */
    public function updateDevicePlate(string $deviceId, string $licensePlate): array
    {
        $tenantId = $this->getTenantId();
        try {
            $response = Http::timeout($this->timeout)
                ->withHeaders(['x-api-key' => $this->apiKey])
                ->put("{$this->baseUrl}/api/{$tenantId}/devices/{$deviceId}", [
                    'license_plate' => $licensePlate
                ]);

            if (!$response->successful()) {
                Log::error('IoT: Failed to update device plate', [
                    'device_id' => $deviceId,
                    'license_plate' => $licensePlate,
                    'status' => $response->status()
                ]);
                return [
                    'success' => false,
                    'error' => 'Failed to update: ' . $response->body()
                ];
            }

            Log::info('IoT: Device plate updated', [
                'device_id' => $deviceId,
                'license_plate' => $licensePlate
            ]);

            return [
                'success' => true,
                'result' => $response->json()
            ];
        } catch (\Exception $e) {
            Log::error('IoT: Update device exception', [
                'device_id' => $deviceId,
                'error' => $e->getMessage()
            ]);
            return [
                'success' => false,
                'error' => 'Connection failed: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Elimina un dispositivo del microservicio IoT.
     */
    public function deleteDevice(string $deviceId): bool
    {
        $tenantId = $this->getTenantId();
        try {
            $response = Http::timeout($this->timeout)
                ->withHeaders(['x-api-key' => $this->apiKey])
                ->delete("{$this->baseUrl}/api/{$tenantId}/devices/{$deviceId}");

            return $response->successful();
        } catch (\Exception $e) {
            Log::error('IoT: Failed to delete device', [
                'device_id' => $deviceId,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }
}
