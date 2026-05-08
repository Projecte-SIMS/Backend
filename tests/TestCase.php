<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

abstract class TestCase extends BaseTestCase
{
    protected string $tenantId = 'test-tenant';

    protected function setUp(): void
    {
        parent::setUp();

        // Force all connections to :memory: to ensure isolation and consistency
        config(['database.default' => 'sqlite']);
        config(['database.connections.sqlite.database' => ':memory:']);
        config(['database.connections.central.database' => ':memory:']);
        config(['database.connections.tenant.database' => ':memory:']);
        
        // Stancl Tenancy sometimes uses its own logic to find the database name
        config(['tenancy.database.central_connection' => 'central']);

        $this->initializeTenancy();
    }

    protected function tearDown(): void
    {
        DB::disconnect('sqlite');
        DB::disconnect('central');
        DB::disconnect('tenant');
        
        parent::tearDown();
    }

    protected function initializeTenancy(): void
    {
        // 1. Configure NullDatabaseManager for sqlite tests
        $this->app->singleton(\Tests\NullDatabaseManager::class, fn() => new \Tests\NullDatabaseManager());
        config(['tenancy.database.managers.sqlite' => \Tests\NullDatabaseManager::class]);

        // 2. Ensure central schema exists
        if (!Schema::connection('central')->hasTable('tenants')) {
            try {
                Artisan::call('migrate', ['--database' => 'central', '--force' => true]);
            } catch (\Exception $e) {
                if (strpos($e->getMessage(), 'already exists') === false) {
                    throw $e;
                }
            }
        }

        // 3. Create/Fetch tenant
        $tenant = \App\Models\Tenant::firstOrCreate(['id' => $this->tenantId]);
        
        // 4. Initialize Tenancy
        tenancy()->initialize($tenant);

        // 5. Ensure tenant tables exist
        if (!Schema::hasTable('users')) {
            try {
                Artisan::call('migrate', [
                    '--path' => 'database/migrations/tenant',
                    '--realpath' => false,
                    '--force' => true
                ]);
            } catch (\Exception $e) {
                if (strpos($e->getMessage(), 'already exists') === false) {
                    throw $e;
                }
            }
        }

        // 6. Default Headers
        $this->withHeaders(['X-Tenant' => $this->tenantId]);
    }
}
