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
        try {
            // 1. Cargar Permisos y Roles
            $this->call([
                PermissionsSeeder::class,
                RolesSeeder::class,
            ]);
        } catch (\Exception $e) {
            echo "⚠️ Permissions/Roles seeder error: " . $e->getMessage() . "\n";
        }

        $password = Hash::make('password');

        // 2. Crear ADMIN
        try {
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
            echo "✅ Admin user created\n";
        } catch (\Exception $e) {
            echo "⚠️ Admin user error: " . $e->getMessage() . "\n";
        }

        // 3. Crear CLIENTE
        try {
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
            echo "✅ Client user created\n";
        } catch (\Exception $e) {
            echo "⚠️ Client user error: " . $e->getMessage() . "\n";
        }

        // 4. Crear MANTENIMIENTO
        try {
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
            echo "✅ Maintenance user created\n";
        } catch (\Exception $e) {
            echo "⚠️ Maintenance user error: " . $e->getMessage() . "\n";
        }

        // 5. Crear datos de prueba
        try {
            $this->call([
                TestDataSeeder::class,
            ]);
        } catch (\Exception $e) {
            echo "⚠️ Test data seeder error: " . $e->getMessage() . "\n";
        }

        // 6. Crear ubicaciones de vehículos (MongoDB) - skip if not available
        try {
            $this->call([
                MongoVehicleLocationsSeeder::class,
            ]);
        } catch (\Exception $e) {
            echo "⚠️ MongoDB seeder skipped: " . $e->getMessage() . "\n";
        }

        echo "\n🚀 Tenant Database seeded!\n";
    }
}
