<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Vehicle;
use App\Models\Ticket;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class TicketTest extends TestCase
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

    public function test_client_can_create_ticket(): void
    {
        $vehicle = Vehicle::factory()->create();

        $response = $this->withHeader('Authorization', "Bearer {$this->clientToken}")
                         ->postJson('/api/tickets', [
                             'vehicle_id' => $vehicle->id,
                             'title' => 'Test Ticket',
                             'description' => 'This is a test ticket description.',
                         ]);

        $response->assertStatus(201)
                 ->assertJsonPath('title', 'Test Ticket');
    }

    public function test_client_can_list_own_tickets(): void
    {
        $vehicle = Vehicle::factory()->create();
        Ticket::factory()->count(3)->create([
            'user_id' => $this->clientUser->id,
            'vehicle_id' => $vehicle->id,
        ]);

        // Create tickets for another user
        $otherUser = User::factory()->create();
        Ticket::factory()->count(2)->create([
            'user_id' => $otherUser->id,
            'vehicle_id' => $vehicle->id,
        ]);

        $response = $this->withHeader('Authorization', "Bearer {$this->clientToken}")
                         ->getJson('/api/tickets');

        $response->assertStatus(200);
        $this->assertCount(3, $response->json());
    }

    public function test_client_can_view_own_ticket(): void
    {
        $vehicle = Vehicle::factory()->create();
        $ticket = Ticket::factory()->create([
            'user_id' => $this->clientUser->id,
            'vehicle_id' => $vehicle->id,
        ]);

        $response = $this->withHeader('Authorization', "Bearer {$this->clientToken}")
                         ->getJson("/api/tickets/{$ticket->id}");

        $response->assertStatus(200)
                 ->assertJsonPath('id', $ticket->id);
    }

    public function test_client_cannot_view_other_user_ticket(): void
    {
        $otherUser = User::factory()->create();
        $vehicle = Vehicle::factory()->create();
        $ticket = Ticket::factory()->create([
            'user_id' => $otherUser->id,
            'vehicle_id' => $vehicle->id,
        ]);

        $response = $this->withHeader('Authorization', "Bearer {$this->clientToken}")
                         ->getJson("/api/tickets/{$ticket->id}");

        $response->assertStatus(403);
    }

    public function test_admin_can_view_all_tickets(): void
    {
        $vehicle = Vehicle::factory()->create();
        Ticket::factory()->count(5)->create(['vehicle_id' => $vehicle->id]);

        $response = $this->withHeader('Authorization', "Bearer {$this->adminToken}")
                         ->getJson('/api/admin/tickets');

        $response->assertStatus(200);
        $this->assertCount(5, $response->json());
    }

    public function test_admin_can_update_ticket_status(): void
    {
        $vehicle = Vehicle::factory()->create();
        $ticket = Ticket::factory()->create([
            'vehicle_id' => $vehicle->id,
            'active' => true,
        ]);

        $response = $this->withHeader('Authorization', "Bearer {$this->adminToken}")
                         ->putJson("/api/admin/tickets/{$ticket->id}", [
                             'active' => false,
                         ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('tickets', [
            'id' => $ticket->id,
            'active' => false,
        ]);
    }

    public function test_admin_can_delete_ticket(): void
    {
        $vehicle = Vehicle::factory()->create();
        $ticket = Ticket::factory()->create(['vehicle_id' => $vehicle->id]);

        $response = $this->withHeader('Authorization', "Bearer {$this->adminToken}")
                         ->deleteJson("/api/admin/tickets/{$ticket->id}");

        $response->assertStatus(204);
        $this->assertDatabaseMissing('tickets', ['id' => $ticket->id]);
    }

    public function test_client_cannot_delete_ticket(): void
    {
        $vehicle = Vehicle::factory()->create();
        $ticket = Ticket::factory()->create([
            'user_id' => $this->clientUser->id,
            'vehicle_id' => $vehicle->id,
        ]);

        $response = $this->withHeader('Authorization', "Bearer {$this->clientToken}")
                         ->deleteJson("/api/admin/tickets/{$ticket->id}");

        $response->assertStatus(403);
    }

    public function test_ticket_requires_title(): void
    {
        $response = $this->withHeader('Authorization', "Bearer {$this->clientToken}")
                         ->postJson('/api/tickets', [
                             'description' => 'Description without title',
                         ]);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['title']);
    }
}
