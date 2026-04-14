<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class ChatbotTest extends TestCase
{
    use RefreshDatabase;

    private User $clientUser;
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
        $this->clientUser = User::factory()->create(['email' => 'client@test.com']);
        $this->clientUser->assignRole('Client');
        $this->clientToken = $this->clientUser->createToken('test')->plainTextToken;
    }

    public function test_unauthenticated_user_cannot_access_chatbot(): void
    {
        $response = $this->postJson('/api/chatbot/chat', [
            'messages' => [
                ['role' => 'user', 'content' => 'Hola']
            ]
        ]);

        $response->assertStatus(401);
    }

    public function test_chatbot_requires_messages_array(): void
    {
        $response = $this->withHeader('Authorization', "Bearer {$this->clientToken}")
                         ->postJson('/api/chatbot/chat', []);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['messages']);
    }

    public function test_chatbot_validates_message_structure(): void
    {
        $response = $this->withHeader('Authorization', "Bearer {$this->clientToken}")
                         ->postJson('/api/chatbot/chat', [
                             'messages' => [
                                 ['role' => 'invalid', 'content' => 'Test']
                             ]
                         ]);

        $response->assertStatus(422);
    }

    public function test_chatbot_returns_error_when_not_configured(): void
    {
        putenv('OPEN_WEBUI_API_KEY=');
        putenv('OPEN_WEBUI_BASE_URL=');
        putenv('OPEN_WEBUI_MODEL=');
        unset($_ENV['OPEN_WEBUI_API_KEY'], $_ENV['OPEN_WEBUI_BASE_URL'], $_ENV['OPEN_WEBUI_MODEL']);
        unset($_SERVER['OPEN_WEBUI_API_KEY'], $_SERVER['OPEN_WEBUI_BASE_URL'], $_SERVER['OPEN_WEBUI_MODEL']);

        // Without proper env configuration, should return error
        $response = $this->withHeader('Authorization', "Bearer {$this->clientToken}")
                         ->postJson('/api/chatbot/chat', [
                             'messages' => [
                                 ['role' => 'user', 'content' => 'Hola']
                             ]
                         ]);

        // Should return 500 if not configured (env vars missing)
        $response->assertStatus(500);
    }
}
