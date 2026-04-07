<?php

namespace Database\Seeders;

use App\Models\Tenant;
use Illuminate\Database\Seeder;

class CentralSeeder extends Seeder
{
    /**
     * Seed the central database with a default tenant if none exists.
     */
    public function run(): void
    {
        // Check if any tenant exists
        if (Tenant::count() === 0) {
            $defaultTenantId = env('DEFAULT_TENANT_ID', 'demo');
            $defaultDomain = env('DEFAULT_TENANT_DOMAIN', 'demo.localhost');

            echo "\n🏢 Creating default tenant: {$defaultTenantId}\n";
            
            $tenant = Tenant::create(['id' => $defaultTenantId]);
            $tenant->domains()->create(['domain' => $defaultDomain]);
            
            echo "✅ Tenant '{$defaultTenantId}' created with domain '{$defaultDomain}'\n";
            echo "📧 Users: admin@sims.com, client@sims.com, maint@sims.com\n";
            echo "🔑 Password: password\n";
        } else {
            echo "\n✅ Tenants already exist. Skipping default tenant creation.\n";
        }
    }
}
