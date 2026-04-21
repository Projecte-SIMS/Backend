<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Models\Tenant;

class TenantSettingsController extends Controller
{
    /**
     * Get current tenant settings (branding, etc.)
     */
    public function index(): JsonResponse
    {
        $tenant = tenancy()->tenant;
        
        return response()->json([
            'success' => true,
            'data' => [
                'id' => $tenant->id,
                'company_plan' => $tenant->company_plan ?? 'base',
                'company_theme' => $tenant->company_theme ?? 'indigo',
                'onboarding_source' => $tenant->company_onboarding_source,
                'created_at' => $tenant->created_at,
            ],
        ]);
    }

    /**
     * Update tenant branding/settings
     */
    public function update(Request $request): JsonResponse
    {
        $tenant = tenancy()->tenant;

        $validated = $request->validate([
            'company_theme' => 'nullable|string|max:30',
        ]);

        if (isset($validated['company_theme'])) {
            $tenant->company_theme = $validated['company_theme'];
        }

        $tenant->save();

        return response()->json([
            'success' => true,
            'message' => 'Configuración actualizada correctamente',
            'data' => [
                'company_theme' => $tenant->company_theme,
            ],
        ]);
    }
}
