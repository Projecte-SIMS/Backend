<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Auth\Access\AuthorizationException;

class UserController extends Controller
{
    public function index()
    {
        return response()->json(User::all());
    }

    public function show(User $user)
    {
        return response()->json($user);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:6'],
            'active' => ['sometimes', 'boolean'],
            'role_id' => ['nullable', 'integer', 'exists:roles,id'],
        ]);

        // Extract role_id before creating user (not a fillable attribute on User model)
        $roleId = $data['role_id'] ?? null;
        unset($data['role_id']);

        // Check authorization: only Admin users can create users with non-Client roles
        if ($roleId) {
            $role = Role::find($roleId);
            
            // If trying to assign a role other than Client, require Admin authentication
            if ($role && $role->name !== 'Client') {
                $user = auth('sanctum')->user();
                
                if (!$user) {
                    throw new AuthenticationException('You must be authenticated to create users with this role.');
                }
                
                if (!$user->hasRole('Admin')) {
                    throw new AuthorizationException('Only Admin users can create users with this role.');
                }
            }
        }

        $data['password'] = Hash::make($data['password']);
        $user = User::create($data);
        
        // Assign role if provided
        if ($roleId) {
            $role = Role::find($roleId);
            if ($role) {
                $user->assignRole($role);
            }
        }

        return response()->json($user, 201);
    }

    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'name' => ['sometimes', 'string', 'max:255'],
            'username' => ['sometimes', 'string', 'max:255'],
            'email' => [
                'sometimes',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($user->id),
            ],
            'password' => ['nullable', 'string', 'min:6'],
            'active' => ['sometimes', 'boolean'],
            'role_id' => ['nullable', 'integer', 'exists:roles,id'],
        ]);

        if (isset($data['password']) && $data['password'] !== null) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }
        
        // Extract role_id before updating user (not a fillable attribute on User model)
        $roleId = $data['role_id'] ?? null;
        unset($data['role_id']);

        $user->update($data);
        
        // Update role if provided
        if ($roleId !== null) {
            $role = Role::find($roleId);
            if ($role) {
                // Remove all existing roles and assign the new one
                $user->syncRoles([$role]);
            }
        }

        return response()->json($user);
    }

    public function destroy(User $user)
    {
        $user->delete();

        return response()->json(User::all());
    }
}
