<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schema;

abstract class TestCase extends BaseTestCase
{
    protected string $tenantId = 'test-tenant';

    protected function setUp(): void
    {
        parent::setUp();

        // Ensure we are using SQLite for tests
        config(['database.default' => 'sqlite']);
        config(['database.connections.sqlite.database' => ':memory:']);
        config(['database.connections.central.database' => ':memory:']);

        $this->initializeTenancy();
    }

    protected function initializeTenancy(): void
    {
        // 1. Configure NullDatabaseManager for sqlite tests
        $this->app->singleton(\Tests\NullDatabaseManager::class, fn() => new \Tests\NullDatabaseManager());
        config(['tenancy.database.managers.sqlite' => \Tests\NullDatabaseManager::class]);

        // 2. Ensure central schema exists
        if (!Schema::connection('central')->hasTable('tenants')) {
            Artisan::call('migrate', ['--database' => 'central', '--force' => true]);
        }

        // 3. Create/Fetch tenant
        $tenant = \App\Models\Tenant::firstOrCreate(['id' => $this->tenantId]);
        
        // 4. Initialize Tenancy
        tenancy()->initialize($tenant);

        // 5. Ensure tenant tables exist
        if (!Schema::hasTable('users')) {
            Artisan::call('migrate', [
                '--path' => 'database/migrations/tenant',
                '--realpath' => false,
                '--force' => true
            ]);
        }

        // 6. Default Headers
        $this->withHeaders(['X-Tenant' => $this->tenantId]);
    }
}
