<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Spatie\Permission\Models\Permission;
use Illuminate\Http\JsonResponse;

class PermissionController extends Controller
{
    /**
     * Get all permissions grouped by module
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $user = auth()->user();
        if (request()->is('admin/*') && (!$user || $user->role !== 'admin')) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        try {
            $permissions = Permission::all()->groupBy(function ($permission) {
                // Extract module from permission name (e.g., "can.view.vehicles" -> "vehicles")
                $parts = explode('.', (string) $permission->name);
                if (count($parts) >= 3) {
                    $module = $parts[count($parts) - 1];
                    return ucfirst($module);
                }
                return 'Other';
            });

            $grouped = [];
            foreach ($permissions as $module => $perms) {
                $grouped[$module] = $perms->map(function ($permission) use ($module) {
                    return [
                        'id' => $permission->id,
                        'name' => (string) $permission->name,
                        'description' => $permission->description ?? '',
                        'module' => $module,
                    ];
                })->values()->toArray();
            }

            return response()->json($grouped);
        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'Failed to load permissions',
            ], 500);
        }
    }

    /**
     * Store a new permission
     */
    public function store(Request $request): JsonResponse
    {
        $user = auth()->user();
        if (request()->is('admin/*') && (!$user || $user->role !== 'admin')) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:permissions,name'],
            'description' => ['sometimes', 'string', 'max:255'],
        ]);
        $permission = Permission::create($data);
        return response()->json($permission, 201);
    }

    /**
     * Update a permission
     */
    public function update(Request $request, $id): JsonResponse
    {
        $user = auth()->user();
        if (request()->is('admin/*') && (!$user || $user->role !== 'admin')) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        $permission = Permission::findOrFail($id);
        $data = $request->validate([
            'name' => ['sometimes', 'string', 'max:255', 'unique:permissions,name,' . $id],
            'description' => ['sometimes', 'string', 'max:255'],
        ]);
        $permission->update($data);
        return response()->json($permission);
    }

    /**
     * Delete a permission
     */
    public function destroy($id): JsonResponse
    {
        $user = auth()->user();
        if (request()->is('admin/*') && (!$user || $user->role !== 'admin')) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        $permission = Permission::findOrFail($id);
        $permission->delete();
        return response()->json(['message' => 'Permission deleted successfully']);
    }
}

