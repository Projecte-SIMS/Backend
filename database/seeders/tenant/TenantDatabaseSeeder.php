<?php

namespace Database\Seeders\Tenant;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Database\Seeders\PermissionsSeeder;
use Database\Seeders\RolesSeeder;
use Database\Seeders\TestDataSeeder;
use Database\Seeders\MongoVehicleLocationsSeeder;

class TenantDatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the tenant's database.
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

        // 5. Crear datos de prueba
        $this->call([
            TestDataSeeder::class,
        ]);

        // 6. Crear ubicaciones de vehículos (MongoDB) - skip if not available
        try {
            $this->call([
                MongoVehicleLocationsSeeder::class,
            ]);
        } catch (\Exception $e) {
            echo "⚠️ MongoDB seeder skipped: " . $e->getMessage() . "\n";
        }

        echo "\n🚀 Tenant Database seeded successfully!\n";
        echo "📧 Users: admin@sims.com, client@sims.com, maint@sims.com\n";
        echo "🔑 Password: password\n";
    }
}
