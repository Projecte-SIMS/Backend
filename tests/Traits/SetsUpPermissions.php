<?php

namespace Tests\Traits;

use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

/**
 * Trait to setup permissions and roles for testing.
 * Provides helper methods for creating test users with different roles.
 */
trait SetsUpPermissions
{
    protected User $adminUser;
    protected User $clientUser;
    protected string $adminToken;
    protected string $clientToken;

    /**
     * Seed all necessary permissions and roles for testing.
     */
    protected function seedPermissionsAndRoles(): void
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
        $maintenanceRole = Role::firstOrCreate(['name' => 'Maintenance', 'guard_name' => 'web']);

        // Admin gets all permissions
        $adminRole->syncPermissions(Permission::all());

        // Client gets limited permissions
        $clientRole->syncPermissions([
            'vehicles.view',
            'tickets.view', 'tickets.manage',
            'reservations.view', 'reservations.manage',
        ]);

        // Maintenance gets vehicle permissions
        $maintenanceRole->syncPermissions([
            'vehicles.view', 'vehicles.manage',
        ]);
    }

    /**
     * Create test users with Admin and Client roles.
     */
    protected function createTestUsers(): void
    {
        $this->adminUser = User::factory()->create(['email' => 'admin@test.com']);
        $this->adminUser->assignRole('Admin');
        $this->adminToken = $this->adminUser->createToken('test')->plainTextToken;

        $this->clientUser = User::factory()->create(['email' => 'client@test.com']);
        $this->clientUser->assignRole('Client');
        $this->clientToken = $this->clientUser->createToken('test')->plainTextToken;
    }

    /**
     * Create a user with a specific role.
     */
    protected function createUserWithRole(string $roleName, array $attributes = []): User
    {
        $user = User::factory()->create($attributes);
        $user->assignRole($roleName);
        return $user;
    }

    /**
     * Get a Bearer token for a user.
     */
    protected function getTokenForUser(User $user): string
    {
        return $user->createToken('test')->plainTextToken;
    }
}
