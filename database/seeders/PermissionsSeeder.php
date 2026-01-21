<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = now();

        $permissions = [
            ['code' => 'users.can.view', 'description' => 'View users'],
            ['code' => 'users.can.delete', 'description' => 'Delete users'],
            ['code' => 'users.can.manage', 'description' => 'Manage users'],
            ['code' => 'roles.can.view', 'description' => 'View roles'],
            ['code' => 'roles.can.delete', 'description' => 'Delete roles'],
            ['code' => 'roles.can.manage', 'description' => 'Manage roles'],
        ];

        foreach ($permissions as $perm) {
            DB::table('permissions')->updateOrInsert(
                ['code' => $perm['code']],
                ['description' => $perm['description'], 'created_at' => $now, 'updated_at' => $now]
            );
        }
    }
}
