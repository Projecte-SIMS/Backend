<?php

namespace App\Models;

use Stancl\Tenancy\Database\Models\Tenant as BaseTenant;
use Stancl\Tenancy\Contracts\TenantWithDatabase;
use Stancl\Tenancy\Database\Concerns\HasDatabase;
use Stancl\Tenancy\Database\Concerns\HasDomains;

class Tenant extends BaseTenant implements TenantWithDatabase
{
    use HasDatabase, HasDomains;

    /**
     * Get custom columns for the tenants table
     */
    public static function getCustomColumns(): array
    {
        return [
            'id',
            'billing_provider',
            'billing_status',
            'billing_customer_id',
            'billing_subscription_id',
            'billing_price_id',
            'billing_currency',
            'billing_monthly_amount_cents',
            'billing_current_period_end',
            'billing_last_invoice_at',
            'billing_last_invoice_status',
            'data',
        ];
    }
}
