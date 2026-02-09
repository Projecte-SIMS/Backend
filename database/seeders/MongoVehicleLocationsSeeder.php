<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MongoVehicleLocationsSeeder extends Seeder
{
    public function run()
    {
        $locations = [
            [
                'license_plate' => 'ABC123',
                'latitude' => -33.4489,
                'longitude' => -70.6693,
                'active' => true,
            ],
            [
                'license_plate' => 'DEF456',
                'latitude' => -33.4590,
                'longitude' => -70.6400,
                'active' => false,
            ],
            [
                'license_plate' => 'GHI789',
                'latitude' => -33.4500,
                'longitude' => -70.6500,
                'active' => true,
            ],
        ];

        // Inserta/actualiza por license_plate
        $connection = DB::connection('mongodb');
        foreach ($locations as $loc) {
            $connection->table('vehicle_locations')->updateOrInsert(
                ['license_plate' => $loc['license_plate']],
                $loc
            );
        }
    }
}
