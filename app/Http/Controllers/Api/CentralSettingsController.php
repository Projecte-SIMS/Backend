<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CentralSettingsController extends Controller
{
    /**
     * Get all public central settings
     */
    public function index(): JsonResponse
    {
        $settings = DB::table('central_settings')
            ->pluck('value', 'key');

        return response()->json([
            'success' => true,
            'data' => $settings,
        ]);
    }

    /**
     * Update a specific central setting
     */
    public function update(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'key' => 'required|string|max:50',
            'value' => 'required|string',
        ]);

        DB::table('central_settings')
            ->updateOrInsert(
                ['key' => $validated['key']],
                [
                    'value' => $validated['value'],
                    'updated_at' => now(),
                ]
            );

        return response()->json([
            'success' => true,
            'message' => "Ajuste '{$validated['key']}' actualizado correctamente",
            'data' => [
                'key' => $validated['key'],
                'value' => $validated['value'],
            ],
        ]);
    }
}
