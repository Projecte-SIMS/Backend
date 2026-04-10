<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use MongoDB\Client as MongoClient;

class MongoVehicleLocationsSeeder extends Seeder
{
    /**
     * Seed vehicle locations directly in MongoDB.
     * All vehicles are located in Terres de l'Ebre (Ulldecona, Amposta, etc.)
     * All vehicles start as available (active = false).
     */
    public function run()
    {
        $mongoUri = env('MONGODB_URI', 'mongodb://localhost:27017');
        $dbName = env('MONGODB_DATABASE', 'raspi_db');
        
        $locations = [
            // Ulldecona - Centro
            [
                'license_plate' => '1234ABC',
                'latitude' => 40.6100,
                'longitude' => 0.4530,
                'name' => 'Vehículo Ulldecona Centro',
            ],
            // Ulldecona - Zona Norte
            [
                'license_plate' => '5678DEF',
                'latitude' => 40.6150,
                'longitude' => 0.4580,
                'name' => 'Vehículo Ulldecona Norte',
            ],
            // Amposta - Centro
            [
                'license_plate' => '9012GHI',
                'latitude' => 40.7130,
                'longitude' => 0.5800,
                'name' => 'Vehículo Amposta Centro',
            ],
            // Amposta - Zona Puerto
            [
                'license_plate' => '3456JKL',
                'latitude' => 40.7080,
                'longitude' => 0.5750,
                'name' => 'Vehículo Amposta Puerto',
            ],
            // Sant Carles de la Ràpita
            [
                'license_plate' => '7890MNO',
                'latitude' => 40.6180,
                'longitude' => 0.5920,
                'name' => 'Vehículo Sant Carles',
            ],
            // Tortosa - Centro
            [
                'license_plate' => '2345PQR',
                'latitude' => 40.8125,
                'longitude' => 0.5215,
                'name' => 'Vehículo Tortosa',
            ],
            // Alcanar
            [
                'license_plate' => '6789STU',
                'latitude' => 40.5430,
                'longitude' => 0.4780,
                'name' => 'Vehículo Alcanar',
            ],
            // La Sénia
            [
                'license_plate' => '0123VWX',
                'latitude' => 40.6510,
                'longitude' => 0.2730,
                'name' => 'Vehículo La Sénia',
            ],
        ];

        try {
            $client = new MongoClient($mongoUri);
            $collection = $client->selectDatabase($dbName)->selectCollection('vehicle_locations');
            
            // Clear existing data
            $collection->deleteMany([]);
            
            $success = 0;
            foreach ($locations as $loc) {
                $doc = [
                    'identity' => [
                        'hardware_id' => 'SEED-' . $loc['license_plate'],
                        'name' => $loc['name'],
                        'license_plate' => $loc['license_plate'],
                    ],
                    'status' => [
                        'online' => false,
                        'active' => false,  // All vehicles start as available
                        'last_update' => time(),
                    ],
                    'telemetry' => [
                        'latitude' => $loc['latitude'],
                        'longitude' => $loc['longitude'],
                        'speed' => 0.0,
                        'engine_temp' => 0.0,
                        'rpm' => 0,
                        'battery_voltage' => 12.6,
                    ],
                    'meta' => new \stdClass(),
                ];
                
                $collection->insertOne($doc);
                $success++;
            }
            
            \Log::info('MongoDB vehicle locations seeded', [
                'region' => 'Terres de l\'Ebre',
                'vehicles' => $success,
                'status' => 'all available',
            ]);
            
        } catch (\Exception $e) {
            \Log::warning('MongoDB connection failed', [
                'uri' => $mongoUri,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
