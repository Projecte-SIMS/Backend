<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the central application's database.
     * Creates a default tenant if none exists.
     */
    public function run(): void
    {
        $this->call([
            CentralSeeder::class,
        ]);
    }
}