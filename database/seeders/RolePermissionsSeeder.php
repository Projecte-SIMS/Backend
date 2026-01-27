<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RolePermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = now();

        $roleMap = DB::table('roles')->pluck('id', 'name')->toArray();
        $permMap = DB::table('permissions')->pluck('id', 'code')->toArray();

        $mapping = [
            'Admin' => [
                'users.can.view', 'users.can.delete', 'users.can.manage',
                'roles.can.view', 'roles.can.delete', 'roles.can.manage',
            ],
            'Client' => [
                'users.can.view', 'roles.can.view',
            ],
            'Mantenaince' => [
                'users.can.view', 'users.can.manage',
            ],
        ];

        foreach ($mapping as $roleName => $permCodes) {
            if (!isset($roleMap[$roleName])) {
                continue;
            }

            $roleId = $roleMap[$roleName];

            foreach ($permCodes as $code) {
                if (!isset($permMap[$code])) {
                    continue;
                }

                $permissionId = $permMap[$code];

                DB::table('role_permissions')->updateOrInsert(
                    ['role_id' => $roleId, 'permission_id' => $permissionId],
                    ['created_at' => $now, 'updated_at' => $now]
                );
            }
        }
    }
}
