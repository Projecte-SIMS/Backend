<?php

namespace Tests\Feature;

use App\Models\Vehicle;
use App\Services\VehicleLocationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicEndpointTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_vehicles_map_does_not_require_authentication(): void
    {
        $response = $this->getJson('/api/public/vehicles/map');

        $response->assertStatus(200);
    }

    public function test_public_vehicles_map_returns_empty_when_no_iot_data(): void
    {
        // Create vehicles without IoT data - should return empty array
        Vehicle::factory()->count(3)->create(['active' => false]);

        $response = $this->getJson('/api/public/vehicles/map');

        $response->assertStatus(200)
                 ->assertJson([]); // Empty because no IoT location data
    }

    public function test_public_vehicles_map_filters_unavailable_vehicles(): void
    {
        // Create active vehicles (in use) - should not appear
        Vehicle::factory()->count(2)->create(['active' => true]);

        $response = $this->getJson('/api/public/vehicles/map');

        $response->assertStatus(200)
                 ->assertJson([]);
    }
}
