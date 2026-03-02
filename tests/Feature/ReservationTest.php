<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Vehicle;
use App\Models\Reservation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Carbon\Carbon;

class ReservationTest extends TestCase
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

    public function test_client_can_create_reservation(): void
    {
        $vehicle = Vehicle::factory()->create(['active' => false]);

        $response = $this->withHeader('Authorization', "Bearer {$this->clientToken}")
                         ->postJson('/api/reservations', [
                             'vehicle_id' => $vehicle->id,
                             'scheduled_start' => Carbon::now()->addHour()->toIso8601String(),
                         ]);

        $response->assertStatus(201)
                 ->assertJsonPath('data.vehicle_id', $vehicle->id)
                 ->assertJsonPath('data.status', 'pending');
    }

    public function test_client_can_list_own_reservations(): void
    {
        $vehicle = Vehicle::factory()->create();
        Reservation::factory()->count(3)->create([
            'user_id' => $this->clientUser->id,
            'vehicle_id' => $vehicle->id,
        ]);

        // Create reservations for another user (should not be visible)
        $otherUser = User::factory()->create();
        Reservation::factory()->count(2)->create([
            'user_id' => $otherUser->id,
            'vehicle_id' => $vehicle->id,
        ]);

        $response = $this->withHeader('Authorization', "Bearer {$this->clientToken}")
                         ->getJson('/api/reservations');

        $response->assertStatus(200);
        $this->assertCount(3, $response->json());
    }

    public function test_client_can_view_own_reservation(): void
    {
        $vehicle = Vehicle::factory()->create();
        $reservation = Reservation::factory()->create([
            'user_id' => $this->clientUser->id,
            'vehicle_id' => $vehicle->id,
        ]);

        $response = $this->withHeader('Authorization', "Bearer {$this->clientToken}")
                         ->getJson("/api/reservations/{$reservation->id}");

        $response->assertStatus(200)
                 ->assertJsonPath('id', $reservation->id);
    }

    public function test_client_cannot_view_other_user_reservation(): void
    {
        $otherUser = User::factory()->create();
        $vehicle = Vehicle::factory()->create();
        $reservation = Reservation::factory()->create([
            'user_id' => $otherUser->id,
            'vehicle_id' => $vehicle->id,
        ]);

        $response = $this->withHeader('Authorization', "Bearer {$this->clientToken}")
                         ->getJson("/api/reservations/{$reservation->id}");

        $response->assertStatus(403);
    }

    public function test_client_can_cancel_pending_reservation(): void
    {
        $vehicle = Vehicle::factory()->create();
        $reservation = Reservation::factory()->create([
            'user_id' => $this->clientUser->id,
            'vehicle_id' => $vehicle->id,
            'status' => 'pending',
        ]);

        $response = $this->withHeader('Authorization', "Bearer {$this->clientToken}")
                         ->postJson("/api/reservations/{$reservation->id}/cancel");

        $response->assertStatus(200);
        $this->assertDatabaseHas('reservations', [
            'id' => $reservation->id,
            'status' => 'cancelled',
        ]);
    }

    public function test_client_cannot_cancel_active_reservation(): void
    {
        $vehicle = Vehicle::factory()->create();
        $reservation = Reservation::factory()->active()->create([
            'user_id' => $this->clientUser->id,
            'vehicle_id' => $vehicle->id,
        ]);

        $response = $this->withHeader('Authorization', "Bearer {$this->clientToken}")
                         ->postJson("/api/reservations/{$reservation->id}/cancel");

        $response->assertStatus(400);
    }

    public function test_admin_can_view_all_reservations(): void
    {
        $vehicle = Vehicle::factory()->create();
        Reservation::factory()->count(5)->create(['vehicle_id' => $vehicle->id]);

        $response = $this->withHeader('Authorization', "Bearer {$this->adminToken}")
                         ->getJson('/api/admin/reservations');

        $response->assertStatus(200);
    }

    public function test_cannot_reserve_vehicle_already_reserved(): void
    {
        $vehicle = Vehicle::factory()->create(['active' => false]);
        
        // Create an existing active reservation
        Reservation::factory()->active()->create([
            'vehicle_id' => $vehicle->id,
        ]);

        $response = $this->withHeader('Authorization', "Bearer {$this->clientToken}")
                         ->postJson('/api/reservations', [
                             'vehicle_id' => $vehicle->id,
                             'scheduled_start' => Carbon::now()->addHour()->toIso8601String(),
                         ]);

        $response->assertStatus(409);
    }
}
