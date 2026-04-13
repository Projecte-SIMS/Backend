<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->string('billing_provider', 30)->nullable()->after('id');
            $table->string('billing_status', 30)->default('inactive')->after('billing_provider');
            $table->string('billing_customer_id')->nullable()->after('billing_status');
            $table->string('billing_subscription_id')->nullable()->after('billing_customer_id');
            $table->string('billing_price_id')->nullable()->after('billing_subscription_id');
            $table->string('billing_currency', 3)->default('EUR')->after('billing_price_id');
            $table->unsignedInteger('billing_monthly_amount_cents')->nullable()->after('billing_currency');
            $table->timestamp('billing_current_period_end')->nullable()->after('billing_monthly_amount_cents');
            $table->timestamp('billing_last_invoice_at')->nullable()->after('billing_current_period_end');
            $table->string('billing_last_invoice_status', 30)->nullable()->after('billing_last_invoice_at');
        });
    }

    public function down(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->dropColumn([
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
            ]);
        });
    }
};
