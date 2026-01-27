<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            'users.view',
            'users.manage',
            'users.delete',
            'roles.view',
            'roles.delete',
            'roles.manage',
            'vehicles.view',
            'vehicles.delete',
            'vehicles.manage',
            'trips.view',
            'trips.manage',
            'trips.delete',
            'maintenance.view',
            'maintenance.manage',
            'maintenance.delete',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }
    }
}
