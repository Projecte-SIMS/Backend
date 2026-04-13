<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;

/**
 * Seeder para asegurar que existe un usuario admin específico.
 * Útil para crear admins adicionales sin resetear la base de datos.
 * 
 * Uso: php artisan db:seed --class=EnsureJordiAdminSeeder
 */
class EnsureJordiAdminSeeder extends Seeder
{
    public function run()
    {
        $role = Role::firstOrCreate(['name' => 'Admin', 'guard_name' => 'web']);

        $user = User::firstOrCreate(
            ['email' => 'jordi@sims.com'],
            [
                'name' => 'Jordi',
                'username' => 'jordi',
                'password' => bcrypt('SuperAdmin123!'),
                'active' => true,
            ]
        );

        if (!$user->hasRole('Admin')) {
            $user->assignRole($role);
        }

        $user->active = true;
        $user->save();
    }
}
