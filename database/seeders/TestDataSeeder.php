<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\Ticket;
use App\Models\Reservation;
use Carbon\Carbon;

class TestDataSeeder extends Seeder
{
    /**
     * Seed test data for vehicles, tickets and reservations.
     * Vehicles match MongoVehicleLocationsSeeder (Terres de l'Ebre).
     * All vehicles start as available (active = false).
     */
    public function run(): void
    {
        $admin = User::find(1);      // Admin
        $client = User::find(2);     // Client
        $maint = User::find(3);      // Maintenance

        // === VEHICLES (Terres de l'Ebre) ===
        // All vehicles start as available (active = false)
        // Must match license_plates in MongoVehicleLocationsSeeder
        
        // Ulldecona - Centro
        Vehicle::updateOrCreate(
            ['license_plate' => '1234ABC'],
            ['brand' => 'Toyota', 'model' => 'Yaris', 'active' => false]
        );

        // Ulldecona - Zona Norte
        Vehicle::updateOrCreate(
            ['license_plate' => '5678DEF'],
            ['brand' => 'Seat', 'model' => 'Ibiza', 'active' => false]
        );

        // Amposta - Centro
        Vehicle::updateOrCreate(
            ['license_plate' => '9012GHI'],
            ['brand' => 'Renault', 'model' => 'Clio', 'active' => false]
        );

        // Amposta - Zona Puerto
        Vehicle::updateOrCreate(
            ['license_plate' => '3456JKL'],
            ['brand' => 'Ford', 'model' => 'Fiesta', 'active' => false]
        );

        // Sant Carles de la Ràpita
        Vehicle::updateOrCreate(
            ['license_plate' => '7890MNO'],
            ['brand' => 'Volkswagen', 'model' => 'Polo', 'active' => false]
        );

        // Tortosa - Centro
        Vehicle::updateOrCreate(
            ['license_plate' => '2345PQR'],
            ['brand' => 'Peugeot', 'model' => '208', 'active' => false]
        );

        // Alcanar
        Vehicle::updateOrCreate(
            ['license_plate' => '6789STU'],
            ['brand' => 'Citroën', 'model' => 'C3', 'active' => false]
        );

        // La Sénia
        Vehicle::updateOrCreate(
            ['license_plate' => '0123VWX'],
            ['brand' => 'Opel', 'model' => 'Corsa', 'active' => false]
        );

        // === TICKETS (examples) ===
        Ticket::updateOrCreate(
            ['title' => 'Consulta sobre reservas'],
            [
                'user_id' => $client->id,
                'description' => '¿Cómo puedo modificar una reserva existente?',
                'active' => true,
            ]
        );

        Ticket::updateOrCreate(
            ['title' => 'Problema con la aplicación'],
            [
                'user_id' => $client->id,
                'description' => 'La aplicación no carga correctamente en mi dispositivo',
                'active' => true,
            ]
        );

        Ticket::updateOrCreate(
            ['title' => 'Sugerencia de mejora'],
            [
                'user_id' => $admin->id,
                'description' => 'Sería útil poder ver el historial de reservas por vehículo',
                'active' => false,
            ]
        );

        // === NO ACTIVE RESERVATIONS ===
        // All vehicles start available, only historical completed reservations
        
        // Historical reservation 1 (completed)
        Reservation::updateOrCreate(
            ['user_id' => $client->id, 'vehicle_id' => 1, 'status' => 'completed'],
            [
                'scheduled_start' => Carbon::now()->subDays(5),
                'activation_deadline' => Carbon::now()->subDays(5)->addMinutes(20),
            ]
        );

        // Historical reservation 2 (completed)
        Reservation::updateOrCreate(
            ['user_id' => $admin->id, 'vehicle_id' => 3, 'status' => 'completed'],
            [
                'scheduled_start' => Carbon::now()->subDays(3),
                'activation_deadline' => Carbon::now()->subDays(3)->addMinutes(20),
            ]
        );

        // Historical reservation 3 (cancelled)
        Reservation::updateOrCreate(
            ['user_id' => $maint->id, 'vehicle_id' => 5, 'status' => 'cancelled'],
            [
                'scheduled_start' => Carbon::now()->subDays(2),
                'activation_deadline' => Carbon::now()->subDays(2)->addMinutes(20),
            ]
        );

        echo "✅ Test data created (Terres de l'Ebre)!\n";
        echo "   8 vehicles (all available)\n";
        echo "   3 tickets\n";
        echo "   3 historical reservations (no active)\n";
    }
}
