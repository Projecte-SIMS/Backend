<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

abstract class TestCase extends BaseTestCase
{
    protected string $tenantId = 'test-tenant';

    /**
     * Creates the application.
     */
    public function createApplication()
    {
        $app = require __DIR__.'/../bootstrap/app.php';
        $app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();
        return $app;
    }

    protected function setUp(): void
    {
        parent::setUp();

        // Configure Null manager to avoid SQLite file creation
        config(['tenancy.database.managers.sqlite' => \Tests\NullDatabaseManager::class]);
        $this->app->singleton(\Tests\NullDatabaseManager::class, fn() => new \Tests\NullDatabaseManager());

        $this->initializeTenancy();
    }

    protected function tearDown(): void
    {
        if ($this->app) {
            DB::disconnect('central');
            DB::disconnect('tenant');
        }
        parent::tearDown();
    }

    protected function initializeTenancy(): void
    {
        // 1. Migrate Central
        if (!Schema::connection('central')->hasTable('tenants')) {
            Artisan::call('migrate', ['--database' => 'central', '--force' => true]);
        }

        // 2. Create/Fetch tenant
        $tenant = \App\Models\Tenant::firstOrCreate(['id' => $this->tenantId]);
        
        // 3. Initialize Tenancy
        tenancy()->initialize($tenant);

        // 4. Migrate Tenant
        if (!Schema::hasTable('users')) {
            Artisan::call('migrate', [
                '--path' => 'database/migrations/tenant',
                '--realpath' => false,
                '--force' => true
            ]);
        }

        $this->withHeaders(['X-Tenant' => $this->tenantId]);
    }
}
