<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create roles
        $adminRole = Role::firstOrCreate(['name' => 'Admin', 'guard_name' => 'web']);
        $clientRole = Role::firstOrCreate(['name' => 'Client', 'guard_name' => 'web']);
        $maintenanceRole = Role::firstOrCreate(['name' => 'Maintenance', 'guard_name' => 'web']);

        // Assign permissions to Admin
        $adminRole->givePermissionTo(Permission::all());

        // Assign permissions to Client
        $clientRole->givePermissionTo([
            'users.view',
            'roles.view',
            'vehicles.view',
            'trips.view',
            'trips.create',
        ]);

        // Assign permissions to Maintenance
        $maintenanceRole->givePermissionTo([
            'users.view',
            'vehicles.view',
            'vehicles.edit',
            'maintenance.view',
            'maintenance.create',
            'maintenance.edit',
            'maintenance.delete',
        ]);
    }
}
