<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class AdminMiddlewareTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedPermissionsAndRoles();
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

    public function test_admin_can_access_admin_routes(): void
    {
        $adminUser = User::factory()->create();
        $adminUser->assignRole('Admin');
        $token = $adminUser->createToken('test')->plainTextToken;

        $response = $this->withHeader('Authorization', "Bearer {$token}")
                         ->getJson('/api/admin/users');

        $response->assertStatus(200);
    }

    public function test_client_cannot_access_admin_routes(): void
    {
        $clientUser = User::factory()->create();
        $clientUser->assignRole('Client');
        $token = $clientUser->createToken('test')->plainTextToken;

        $response = $this->withHeader('Authorization', "Bearer {$token}")
                         ->getJson('/api/admin/users');

        $response->assertStatus(403)
                 ->assertJson(['message' => 'Unauthorized. Admin access required.']);
    }

    public function test_unauthenticated_cannot_access_admin_routes(): void
    {
        $response = $this->getJson('/api/admin/users');

        $response->assertStatus(401);
    }

    public function test_admin_can_access_admin_vehicles(): void
    {
        $adminUser = User::factory()->create();
        $adminUser->assignRole('Admin');
        $token = $adminUser->createToken('test')->plainTextToken;

        $response = $this->withHeader('Authorization', "Bearer {$token}")
                         ->getJson('/api/admin/vehicles');

        $response->assertStatus(200);
    }

    public function test_client_cannot_access_admin_vehicles(): void
    {
        $clientUser = User::factory()->create();
        $clientUser->assignRole('Client');
        $token = $clientUser->createToken('test')->plainTextToken;

        $response = $this->withHeader('Authorization', "Bearer {$token}")
                         ->getJson('/api/admin/vehicles');

        $response->assertStatus(403);
    }

    public function test_admin_can_access_admin_roles(): void
    {
        $adminUser = User::factory()->create();
        $adminUser->assignRole('Admin');
        $token = $adminUser->createToken('test')->plainTextToken;

        $response = $this->withHeader('Authorization', "Bearer {$token}")
                         ->getJson('/api/admin/roles');

        $response->assertStatus(200);
    }

    public function test_client_cannot_access_admin_roles(): void
    {
        $clientUser = User::factory()->create();
        $clientUser->assignRole('Client');
        $token = $clientUser->createToken('test')->plainTextToken;

        $response = $this->withHeader('Authorization', "Bearer {$token}")
                         ->getJson('/api/admin/roles');

        $response->assertStatus(403);
    }
}
