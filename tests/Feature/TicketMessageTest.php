<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Vehicle;
use App\Models\Ticket;
use App\Models\TicketMessage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class TicketMessageTest extends TestCase
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

    public function test_client_can_add_message_to_own_ticket(): void
    {
        $vehicle = Vehicle::factory()->create();
        $ticket = Ticket::factory()->create([
            'user_id' => $this->clientUser->id,
            'vehicle_id' => $vehicle->id,
        ]);

        $response = $this->withHeader('Authorization', "Bearer {$this->clientToken}")
                         ->postJson("/api/tickets/{$ticket->id}/messages", [
                             'ticket_id' => $ticket->id,
                             'message' => 'This is a test message',
                         ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('ticket_messages', [
            'ticket_id' => $ticket->id,
            'user_id' => $this->clientUser->id,
            'message' => 'This is a test message',
        ]);
    }

    public function test_client_cannot_add_message_to_other_user_ticket(): void
    {
        $otherUser = User::factory()->create();
        $vehicle = Vehicle::factory()->create();
        $ticket = Ticket::factory()->create([
            'user_id' => $otherUser->id,
            'vehicle_id' => $vehicle->id,
        ]);

        $response = $this->withHeader('Authorization', "Bearer {$this->clientToken}")
                         ->postJson("/api/tickets/{$ticket->id}/messages", [
                             'ticket_id' => $ticket->id,
                             'message' => 'Unauthorized message',
                         ]);

        $response->assertStatus(403);
    }

    public function test_admin_can_add_message_to_any_ticket(): void
    {
        $vehicle = Vehicle::factory()->create();
        $ticket = Ticket::factory()->create([
            'user_id' => $this->clientUser->id,
            'vehicle_id' => $vehicle->id,
        ]);

        $response = $this->withHeader('Authorization', "Bearer {$this->adminToken}")
                         ->postJson("/api/admin/tickets/{$ticket->id}/messages", [
                             'ticket_id' => $ticket->id,
                             'message' => 'Admin response message',
                         ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('ticket_messages', [
            'ticket_id' => $ticket->id,
            'user_id' => $this->adminUser->id,
            'message' => 'Admin response message',
        ]);
    }

    public function test_user_can_delete_own_message(): void
    {
        $vehicle = Vehicle::factory()->create();
        $ticket = Ticket::factory()->create([
            'user_id' => $this->clientUser->id,
            'vehicle_id' => $vehicle->id,
        ]);
        $message = TicketMessage::create([
            'ticket_id' => $ticket->id,
            'user_id' => $this->clientUser->id,
            'message' => 'Message to delete',
        ]);

        $response = $this->withHeader('Authorization', "Bearer {$this->clientToken}")
                         ->deleteJson("/api/messages/{$message->id}");

        $response->assertStatus(204);
        $this->assertDatabaseMissing('ticket_messages', ['id' => $message->id]);
    }

    public function test_user_cannot_delete_other_user_message(): void
    {
        $otherUser = User::factory()->create();
        $vehicle = Vehicle::factory()->create();
        $ticket = Ticket::factory()->create([
            'user_id' => $otherUser->id,
            'vehicle_id' => $vehicle->id,
        ]);
        $message = TicketMessage::create([
            'ticket_id' => $ticket->id,
            'user_id' => $otherUser->id,
            'message' => 'Other user message',
        ]);

        $response = $this->withHeader('Authorization', "Bearer {$this->clientToken}")
                         ->deleteJson("/api/messages/{$message->id}");

        $response->assertStatus(403);
    }

    public function test_admin_can_delete_any_message(): void
    {
        $vehicle = Vehicle::factory()->create();
        $ticket = Ticket::factory()->create([
            'user_id' => $this->clientUser->id,
            'vehicle_id' => $vehicle->id,
        ]);
        $message = TicketMessage::create([
            'ticket_id' => $ticket->id,
            'user_id' => $this->clientUser->id,
            'message' => 'Client message',
        ]);

        $response = $this->withHeader('Authorization', "Bearer {$this->adminToken}")
                         ->deleteJson("/api/messages/{$message->id}");

        $response->assertStatus(204);
        $this->assertDatabaseMissing('ticket_messages', ['id' => $message->id]);
    }

    public function test_adding_message_reopens_inactive_ticket(): void
    {
        $vehicle = Vehicle::factory()->create();
        $ticket = Ticket::factory()->create([
            'user_id' => $this->clientUser->id,
            'vehicle_id' => $vehicle->id,
            'active' => false,
        ]);

        $response = $this->withHeader('Authorization', "Bearer {$this->clientToken}")
                         ->postJson("/api/tickets/{$ticket->id}/messages", [
                             'ticket_id' => $ticket->id,
                             'message' => 'Reopening ticket',
                         ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('tickets', [
            'id' => $ticket->id,
            'active' => true,
        ]);
    }

    public function test_message_requires_content(): void
    {
        $vehicle = Vehicle::factory()->create();
        $ticket = Ticket::factory()->create([
            'user_id' => $this->clientUser->id,
            'vehicle_id' => $vehicle->id,
        ]);

        $response = $this->withHeader('Authorization', "Bearer {$this->clientToken}")
                         ->postJson("/api/tickets/{$ticket->id}/messages", [
                             'ticket_id' => $ticket->id,
                         ]);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['message']);
    }
}
