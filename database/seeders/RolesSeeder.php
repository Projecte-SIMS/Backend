<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Crear Roles
        $adminRole = Role::firstOrCreate(['name' => 'Admin', 'guard_name' => 'web']);
        $clientRole = Role::firstOrCreate(['name' => 'Client', 'guard_name' => 'web']);
        $maintenanceRole = Role::firstOrCreate(['name' => 'Maintenance', 'guard_name' => 'web']);

        // 2. Asignar Permisos al ADMIN (Todo)
        $adminRole->syncPermissions(Permission::all());

        // 3. Asignar Permisos al CLIENTE (Según tu Excel)
        $clientRole->syncPermissions([
            'can.view.vehicles',
            'can.view.vehicle.detail',
            'can.create.reservation',
            'can.activate.reservation',
            'can.finish.reservation',
            'can.cancel.reservation',
            'can.create.ticket',
            'can.view.own.tickets',
            'can.reply.own.tickets',
            'can.view.profile',
        ]);

        // 4. Asignar Permisos a MANTENIMIENTO (Opcional, basado en lo que tenías)
        // Le damos permisos de ver vehículos y gestionar su propia tabla
        $maintenanceRole->syncPermissions([
            'can.view.all.vehicles', // Necesita ver todos para repararlos
            'can.edit.vehicle',      // Para cambiar estado a "en reparación"
            'can.view.maintenance',
            'can.manage.maintenance',
            'can.delete.maintenance',
        ]);
    }
}