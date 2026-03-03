<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     * Order: Permissions -> Roles -> Users -> Test Data -> MongoDB
     */
    public function run(): void
    {
        // 1. Cargar Permisos y Roles
        $this->call([
            PermissionsSeeder::class,
            RolesSeeder::class,
        ]);

        $password = Hash::make('password');

        // 2. Crear ADMIN
        $admin = User::firstOrCreate(
            ['email' => 'admin@sims.com'],
            [
                'name' => 'Administrador',
                'username' => 'admin',
                'password' => $password,
                'active' => true,
            ]
        );
        $admin->assignRole('Admin');

        // 3. Crear CLIENTE
        $client = User::firstOrCreate(
            ['email' => 'client@sims.com'],
            [
                'name' => 'Cliente Demo',
                'username' => 'client',
                'password' => $password,
                'active' => true,
            ]
        );
        $client->assignRole('Client');

        // 4. Crear MANTENIMIENTO
        $maintenance = User::firstOrCreate(
            ['email' => 'maint@sims.com'],
            [
                'name' => 'Técnico Mantenimiento',
                'username' => 'maintenance',
                'password' => $password,
                'active' => true,
            ]
        );
        $maintenance->assignRole('Maintenance');

        // 5. Crear datos de prueba (MySQL)
        $this->call([
            TestDataSeeder::class,
        ]);

        // 6. Crear ubicaciones de vehículos (MongoDB)
        $this->call([
            MongoVehicleLocationsSeeder::class,
        ]);

        echo "\n🚀 SIMS Database seeded successfully!\n";
        echo "📧 Users: admin@sims.com, client@sims.com, maint@sims.com\n";
        echo "🔑 Password: password\n";
    }
}