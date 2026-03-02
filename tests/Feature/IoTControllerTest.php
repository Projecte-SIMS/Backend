<?php

namespace Tests\Feature;

use App\Models\User;
use App\Services\VehicleLocationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class IoTControllerTest extends TestCase
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

    public function test_health_endpoint_is_public(): void
    {
        Http::fake([
            '*/health' => Http::response(['ok' => true], 200),
        ]);

        $response = $this->getJson('/api/iot/health');

        $response->assertStatus(200)
                 ->assertJson(['ok' => true]);
    }

    public function test_authenticated_user_can_list_iot_devices(): void
    {
        Http::fake([
            '*/api/devices' => Http::response([
                [
                    'id' => '123',
                    'license_plate' => 'TEST-001',
                    'online' => true,
                    'telemetry' => ['latitude' => 40.0, 'longitude' => -3.0],
                    'status' => ['active' => true]
                ]
            ], 200),
        ]);

        $response = $this->withHeader('Authorization', "Bearer {$this->clientToken}")
                         ->getJson('/api/iot/devices');

        $response->assertStatus(200);
    }

    public function test_authenticated_user_can_get_single_device(): void
    {
        Http::fake([
            '*/api/devices/123' => Http::response([
                'id' => '123',
                'license_plate' => 'TEST-001',
                'online' => true,
            ], 200),
        ]);

        $response = $this->withHeader('Authorization', "Bearer {$this->clientToken}")
                         ->getJson('/api/iot/devices/123');

        $response->assertStatus(200)
                 ->assertJsonPath('id', '123');
    }

    public function test_admin_can_turn_on_device(): void
    {
        Http::fake([
            '*/api/command' => Http::response(['result' => 'sent'], 200),
        ]);

        $response = $this->withHeader('Authorization', "Bearer {$this->adminToken}")
                         ->postJson('/api/admin/iot/devices/123/on');

        $response->assertStatus(200)
                 ->assertJsonPath('result', 'sent');
    }

    public function test_admin_can_turn_off_device(): void
    {
        Http::fake([
            '*/api/command' => Http::response(['result' => 'sent'], 200),
        ]);

        $response = $this->withHeader('Authorization', "Bearer {$this->adminToken}")
                         ->postJson('/api/admin/iot/devices/123/off');

        $response->assertStatus(200)
                 ->assertJsonPath('result', 'sent');
    }

    public function test_client_cannot_send_commands(): void
    {
        $response = $this->withHeader('Authorization', "Bearer {$this->clientToken}")
                         ->postJson('/api/admin/iot/devices/123/on');

        $response->assertStatus(403);
    }

    public function test_unauthenticated_cannot_access_devices(): void
    {
        $response = $this->getJson('/api/iot/devices');

        $response->assertStatus(401);
    }

    public function test_ping_device_returns_online_status(): void
    {
        Http::fake([
            '*/api/ping/123' => Http::response(['online' => true], 200),
        ]);

        $response = $this->withHeader('Authorization', "Bearer {$this->clientToken}")
                         ->getJson('/api/iot/devices/123/ping');

        $response->assertStatus(200)
                 ->assertJsonPath('online', true);
    }
}
