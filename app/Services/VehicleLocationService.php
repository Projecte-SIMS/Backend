<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class VehicleLocationService
{
    /**
     * Obtiene las ubicaciones de los vehículos desde MongoDB Atlas
     * Devuelve un array indexado por license_plate
     */
    public function getLocations(): array
    {
        $locations = DB::connection('mongodb')
            ->table('vehicle_locations')
            ->get();

        $result = [];
        foreach ($locations as $location) {
            // Extraemos los bloques anidados (asegurando que sean tratados como arrays o objetos)
            $identity  = $location->identity ?? [];
            $telemetry = $location->telemetry ?? [];
            $status    = $location->status ?? [];

            // La matrícula ahora está dentro de identity
            $licensePlate = $identity['license_plate'] ?? null;

            if ($licensePlate) {
                $result[$licensePlate] = [
                    // Las coordenadas están dentro de telemetry
                    'latitude'  => (float) ($telemetry['latitude'] ?? 0),
                    'longitude' => (float) ($telemetry['longitude'] ?? 0),

                    // El estado de encendido está dentro de status
                    'active'    => isset($status['active']) ? (bool) $status['active'] : null,

                    // Opcional: Puedes añadir los nuevos campos que hemos implementado
                    'speed'     => (float) ($telemetry['speed'] ?? 0),
                    'rpm'       => (int) ($telemetry['rpm'] ?? 0),
                    'temp'      => (float) ($telemetry['engine_temp'] ?? 0),
                ];
            }
        }

        return $result;
    }

    /**
     * Obtiene la ubicación de un vehículo específico por matrícula
     */
    public function getLocationByPlate(string $licensePlate): ?array
    {
        $location = DB::connection('mongodb')
            ->table('vehicle_locations')
            ->where('license_plate', $licensePlate)
            ->orWhere('licensePlate', $licensePlate)
            ->first();

        if (!$location) {
            return null;
        }

        return [
            'latitude' => (float) ($location->latitude ?? $location->lat ?? 0),
            'longitude' => (float) ($location->longitude ?? $location->lng ?? $location->lon ?? 0),
            'active' => isset($location->active) ? (bool) $location->active : null,
        ];
    }
}
