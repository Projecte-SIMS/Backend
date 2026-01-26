<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Database\Seeders\PermissionsSeeder;
use Database\Seeders\RolesSeeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            PermissionsSeeder::class,
            RolesSeeder::class,
        ]);

        // Create a default admin user
        $admin = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'uuid' => Str::uuid(),
                'name' => 'Administrator',
                'username' => 'admin',
                'password' => Hash::make('password'),
                'active' => true,
            ]
        );
        $admin->assignRole('Admin');
    }
}
