<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Models\Tenant;

class DatabaseCheckController extends Controller
{
    /**
     * Check database structure (debug endpoint)
     */
    public function check()
    {
        $result = [
            'status' => 'ok',
            'central' => $this->checkCentral(),
            'tenants' => $this->checkTenants(),
        ];

        return response()->json($result);
    }

    private function checkCentral()
    {
        try {
            $tenants = Tenant::all();
            $data = [
                'success' => true,
                'total_tenants' => $tenants->count(),
                'tenants' => [],
            ];

            foreach ($tenants as $tenant) {
                $data['tenants'][] = [
                    'id' => $tenant->id,
                    'created_at' => $tenant->created_at,
                ];
            }

            return $data;
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    private function checkTenants()
    {
        $tenants = Tenant::all();
        $result = [];

        foreach ($tenants as $tenant) {
            try {
                $tenantData = [
                    'id' => $tenant->id,
                    'success' => true,
                    'tables' => [],
                    'users' => [],
                    'error' => null,
                ];

                $tenant->run(function () use (&$tenantData) {
                    // Get tables
                    $tables = DB::select("
                        SELECT table_name 
                        FROM information_schema.tables 
                        WHERE table_schema = 'public'
                        ORDER BY table_name
                    ");

                    foreach ($tables as $table) {
                        try {
                            $count = DB::table($table->table_name)->count();
                            $tenantData['tables'][] = [
                                'name' => $table->table_name,
                                'records' => $count,
                            ];
                        } catch (\Exception $e) {
                            $tenantData['tables'][] = [
                                'name' => $table->table_name,
                                'error' => $e->getMessage(),
                            ];
                        }
                    }

                    // Get users
                    try {
                        $users = DB::table('users')
                            ->select('id', 'email', 'username', 'active')
                            ->get();

                        foreach ($users as $user) {
                            $tenantData['users'][] = [
                                'email' => $user->email,
                                'username' => $user->username,
                                'active' => $user->active,
                            ];
                        }
                    } catch (\Exception $e) {
                        $tenantData['users_error'] = $e->getMessage();
                    }
                });

                $result[] = $tenantData;
            } catch (\Exception $e) {
                $result[] = [
                    'id' => $tenant->id,
                    'success' => false,
                    'error' => $e->getMessage(),
                ];
            }
        }

        return $result;
    }
}
