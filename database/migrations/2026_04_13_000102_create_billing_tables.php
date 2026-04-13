<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('billing_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->string('tenant_id');
            $table->string('provider', 30)->default('stripe');
            $table->string('provider_subscription_id')->unique();
            $table->string('provider_customer_id');
            $table->string('status', 30);
            $table->string('price_id')->nullable();
            $table->string('currency', 3)->default('EUR');
            $table->unsignedInteger('amount_cents')->nullable();
            $table->timestamp('current_period_start')->nullable();
            $table->timestamp('current_period_end')->nullable();
            $table->timestamp('canceled_at')->nullable();
            $table->json('raw_payload')->nullable();
            $table->timestamps();

            $table->foreign('tenant_id')->references('id')->on('tenants')->cascadeOnDelete();
            $table->index(['tenant_id', 'status']);
        });

        Schema::create('billing_invoices', function (Blueprint $table) {
            $table->id();
            $table->string('tenant_id');
            $table->string('provider', 30)->default('stripe');
            $table->string('provider_invoice_id')->unique();
            $table->string('provider_customer_id');
            $table->string('provider_subscription_id')->nullable();
            $table->string('status', 30);
            $table->string('currency', 3)->default('EUR');
            $table->unsignedInteger('amount_due_cents')->default(0);
            $table->unsignedInteger('amount_paid_cents')->default(0);
            $table->timestamp('invoice_created_at')->nullable();
            $table->timestamp('due_date')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->string('hosted_invoice_url')->nullable();
            $table->string('invoice_pdf')->nullable();
            $table->json('raw_payload')->nullable();
            $table->timestamps();

            $table->foreign('tenant_id')->references('id')->on('tenants')->cascadeOnDelete();
            $table->index(['tenant_id', 'status']);
        });

        Schema::create('billing_events', function (Blueprint $table) {
            $table->id();
            $table->string('provider', 30)->default('stripe');
            $table->string('provider_event_id')->unique();
            $table->string('type');
            $table->string('tenant_id')->nullable();
            $table->timestamp('processed_at');
            $table->json('payload');
            $table->timestamps();

            $table->index(['provider', 'type']);
            $table->index('tenant_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('billing_events');
        Schema::dropIfExists('billing_invoices');
        Schema::dropIfExists('billing_subscriptions');
    }
};
