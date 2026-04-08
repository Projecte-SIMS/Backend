<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DebugController
{
    public function testAuth(Request $request)
    {
        try {
            $user = auth()->user();
            
            return response()->json([
                'auth_check' => auth()->check(),
                'user' => $user,
                'tenancy_initialized' => tenancy()->initialized,
                'tenant_id' => tenancy()->tenant?->id,
                'connection' => DB::connection()->getName(),
                'token_table_count' => DB::table('personal_access_tokens')->count(),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ], 500);
        }
    }
}
