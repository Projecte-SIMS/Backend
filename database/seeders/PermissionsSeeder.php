<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class PermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Limpiar la caché de permisos de Spatie
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // 2. Lista exacta basada en tu Excel (Columna Acción)
        $permissions = [
            // --- BLOQUE CLIENTE (Común) ---
            'can.view.vehicles',            // Ver lista
            'can.view.vehicle.detail',      // Ver detalle
            'can.create.reservation',       // Reservar
            'can.activate.reservation',     // Recoger
            'can.finish.reservation',       // Devolver
            'can.cancel.reservation',       // Cancelar
            'can.create.ticket',            // Crear ticket
            'can.view.own.tickets',         // Ver mis tickets
            'can.reply.own.tickets',        // Responder mis tickets
            'can.view.profile',             // Ver mi perfil

            // --- BLOQUE ADMIN (Gestión) ---
            'can.create.vehicle',
            'can.edit.vehicle',
            'can.delete.vehicle',
            'can.view.all.vehicles',        // Ver inventario completo (inc. rotos)
            
            'can.view.users',
            'can.manage.users',
            'can.delete.users',
            
            'can.view.all.reservations',
            'can.force.finish.reservation',
            
            'can.manage.roles',             // Crear roles y asignar
            
            'can.view.all.tickets',
            'can.reply.any.ticket',
            'can.delete.any.ticket',
            
            // --- BLOQUE MANTENIMIENTO (Extraído de tu lógica anterior) ---
            'can.view.maintenance',
            'can.manage.maintenance',
            'can.delete.maintenance',
        ];

        // 3. Crear los permisos
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }
    }
}