<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Services\VehicleLocationService;
use Mockery\MockInterface;

class VehicleTest extends TestCase
{
    use RefreshDatabase;

    private User $adminUser;
    private User $clientUser;
    private string $adminToken;
    private string $clientToken;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedPermissionsAndRoles();
        $this->createTestUsers();
    }

    private function seedPermissionsAndRoles(): void
    {
        $permissions = [
            'users.view', 'users.manage', 'users.delete',
            'vehicles.view', 'vehicles.manage', 'vehicles.delete',
            'tickets.view', 'tickets.manage', 'tickets.delete',
            'reservations.view', 'reservations.manage', 'reservations.delete',
            'roles.view', 'roles.manage', 'roles.delete',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        $adminRole = Role::firstOrCreate(['name' => 'Admin', 'guard_name' => 'web']);
        $clientRole = Role::firstOrCreate(['name' => 'Client', 'guard_name' => 'web']);

        $adminRole->syncPermissions(Permission::all());
        $clientRole->syncPermissions(['vehicles.view', 'tickets.view', 'tickets.manage', 'reservations.view', 'reservations.manage']);
    }

    private function createTestUsers(): void
    {
        $this->adminUser = User::factory()->create(['email' => 'admin@test.com']);
        $this->adminUser->assignRole('Admin');
        $this->adminToken = $this->adminUser->createToken('test')->plainTextToken;

        $this->clientUser = User::factory()->create(['email' => 'client@test.com']);
        $this->clientUser->assignRole('Client');
        $this->clientToken = $this->clientUser->createToken('test')->plainTextToken;
    }

    public function test_client_can_list_available_vehicles(): void
    {
        // Mock IoT service to return only one vehicle as online
        $this->mock(VehicleLocationService::class, function (MockInterface $mock) {
            $mock->shouldReceive('getLocations')->andReturn([
                'V1' => [
                    'device_id' => 'dev1',
                    'latitude' => 41.5,
                    'longitude' => 0.5,
                    'online' => true,
                    'active' => false
                ],
                'V2' => [
                    'device_id' => 'dev2',
                    'latitude' => 41.6,
                    'longitude' => 0.6,
                    'online' => false, // Offline
                    'active' => false
                ]
            ]);
        });

        Vehicle::factory()->create(['license_plate' => 'V1', 'active' => false]);
        Vehicle::factory()->create(['license_plate' => 'V2', 'active' => false]);

        $response = $this->withHeader('Authorization', "Bearer {$this->clientToken}")
                         ->getJson('/api/vehicles');

        $response->assertStatus(200);
        $this->assertCount(1, $response->json('data'));
        $this->assertEquals('V1', $response->json('data.0.license_plate'));
    }

    public function test_vehicles_map_endpoint_filters_by_online_and_location(): void
    {
        // Mock IoT service to return only one vehicle as online
        $this->mock(VehicleLocationService::class, function (MockInterface $mock) {
            $mock->shouldReceive('getLocations')->andReturn([
                'V1' => [
                    'device_id' => 'dev1',
                    'latitude' => 41.5,
                    'longitude' => 0.5,
                    'online' => true,
                    'active' => false
                ],
                'V3' => [
                    'device_id' => 'dev3',
                    'latitude' => 0, // No location
                    'longitude' => 0,
                    'online' => true,
                    'active' => false
                ]
            ]);
        });

        Vehicle::factory()->create(['license_plate' => 'V1', 'active' => false]);
        Vehicle::factory()->create(['license_plate' => 'V3', 'active' => false]);

        $response = $this->withHeader('Authorization', "Bearer {$this->clientToken}")
                         ->getJson('/api/vehicles/map');

        $response->assertStatus(200);
        $this->assertCount(1, $response->json());
        $this->assertEquals('V1', $response->json('0.plate'));
    }

    public function test_client_can_view_single_vehicle(): void
    {
        $vehicle = Vehicle::factory()->create();

        $response = $this->withHeader('Authorization', "Bearer {$this->clientToken}")
                         ->getJson("/api/vehicles/{$vehicle->id}");

        $response->assertStatus(200)
                 ->assertJsonPath('data.license_plate', $vehicle->license_plate);
    }

    public function test_admin_can_create_vehicle(): void
    {
        $vehicleData = [
            'license_plate' => '1234ABC',
            'brand' => 'Tesla',
            'model' => 'Model 3',
            'active' => false,
            'price_per_minute' => 0.25,
        ];

        $response = $this->withHeader('Authorization', "Bearer {$this->adminToken}")
                         ->postJson('/api/admin/vehicles', $vehicleData);

        $response->assertStatus(201);
        $this->assertDatabaseHas('vehicles', ['license_plate' => '1234ABC']);
    }

    public function test_client_cannot_create_vehicle(): void
    {
        $vehicleData = [
            'license_plate' => '1234ABC',
            'brand' => 'Tesla',
            'model' => 'Model 3',
        ];

        $response = $this->withHeader('Authorization', "Bearer {$this->clientToken}")
                         ->postJson('/api/admin/vehicles', $vehicleData);

        $response->assertStatus(403);
    }

    public function test_admin_can_update_vehicle(): void
    {
        $vehicle = Vehicle::factory()->create(['brand' => 'Toyota']);

        $response = $this->withHeader('Authorization', "Bearer {$this->adminToken}")
                         ->putJson("/api/admin/vehicles/{$vehicle->id}", [
                             'brand' => 'Honda',
                         ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('vehicles', [
            'id' => $vehicle->id,
            'brand' => 'Honda',
        ]);
    }

    public function test_admin_can_delete_vehicle(): void
    {
        $vehicle = Vehicle::factory()->create();

        $response = $this->withHeader('Authorization', "Bearer {$this->adminToken}")
                         ->deleteJson("/api/admin/vehicles/{$vehicle->id}");

        $response->assertStatus(200);
        $this->assertSoftDeleted('vehicles', ['id' => $vehicle->id]);
    }

    public function test_client_cannot_delete_vehicle(): void
    {
        $vehicle = Vehicle::factory()->create();

        $response = $this->withHeader('Authorization', "Bearer {$this->clientToken}")
                         ->deleteJson("/api/admin/vehicles/{$vehicle->id}");

        $response->assertStatus(403);
    }

    public function test_vehicles_map_endpoint_returns_coordinates(): void
    {
        Vehicle::factory()->count(3)->create();

        $response = $this->withHeader('Authorization', "Bearer {$this->clientToken}")
                         ->getJson('/api/vehicles/map');

        $response->assertStatus(200);
    }
}
