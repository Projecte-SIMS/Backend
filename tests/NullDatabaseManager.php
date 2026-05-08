<?php

namespace Tests;

use Stancl\Tenancy\Contracts\TenantDatabaseManager;
use Stancl\Tenancy\Contracts\TenantWithDatabase;

class NullDatabaseManager implements TenantDatabaseManager
{
    public function createDatabase(TenantWithDatabase $tenant): bool { return true; }
    public function deleteDatabase(TenantWithDatabase $tenant): bool { return true; }
    public function databaseExists(string $name): bool { return false; } // Must be false to skip "already exists" check
    public function makeConnectionConfig(array $baseConfig, string $databaseName): array {
        return array_merge($baseConfig, ['database' => ':memory:']);
    }
    public function setConnection(string $connection): void {}
}
