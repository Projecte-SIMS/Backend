<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\Tenant;

class CheckDatabase extends Command
{
    protected $signature = 'db:check';
    protected $description = 'Check database structure for multi-tenant system';

    public function handle()
    {
        $this->info('📊 Checking PostgreSQL Database Structure');
        $this->info('=====================================');
        $this->newLine();

        // Check central database tables
        $this->checkCentralDatabase();
        
        // Check tenant databases
        $this->checkTenantDatabases();
    }

    private function checkCentralDatabase()
    {
        $this->info('🏢 CENTRAL DATABASE');
        
        // Check tenants table
        $tenants = Tenant::all();
        $this->info("  Total tenants: " . $tenants->count());
        
        foreach ($tenants as $tenant) {
            $this->info("    - {$tenant->id} (created: {$tenant->created_at})");
        }
        
        $this->newLine();
    }

    private function checkTenantDatabases()
    {
        $tenants = Tenant::all();
        
        foreach ($tenants as $tenant) {
            $this->info("📁 TENANT: {$tenant->id}");
            
            try {
                $tenant->run(function () use ($tenant) {
                    // Check tables
                    $tables = DB::select("
                        SELECT table_name 
                        FROM information_schema.tables 
                        WHERE table_schema = 'public'
                        ORDER BY table_name
                    ");
                    
                    $this->info("  Tables: " . count($tables));
                    
                    foreach ($tables as $table) {
                        $count = DB::table($table->table_name)->count();
                        $this->info("    - {$table->table_name}: $count records");
                    }
                    
                    // Check users
                    try {
                        $users = DB::table('users')->select('id', 'email', 'username', 'active')->get();
                        $this->info("  Users:");
                        foreach ($users as $user) {
                            $active = $user->active ? '✅' : '❌';
                            $this->info("    $active {$user->email} ({$user->username})");
                        }
                    } catch (\Exception $e) {
                        $this->error("    ❌ Users table error: " . $e->getMessage());
                    }
                });
            } catch (\Exception $e) {
                $this->error("  ❌ Error accessing tenant: " . $e->getMessage());
            }
            
            $this->newLine();
        }
    }
}
