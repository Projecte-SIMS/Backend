<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schema;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->runTenantMigrationsForTesting();
    }

    protected function runTenantMigrationsForTesting(): void
    {
        if (Schema::hasTable('permissions') && Schema::hasTable('users')) {
            return;
        }

        Artisan::call('migrate', [
            '--path' => 'database/migrations/tenant',
            '--realpath' => false,
            '--force' => true,
        ]);
    }
}
