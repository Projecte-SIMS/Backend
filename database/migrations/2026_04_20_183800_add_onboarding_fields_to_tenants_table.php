<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->string('company_name')->nullable();
            $table->string('company_plan')->nullable();
            $table->string('company_theme')->nullable();
            $table->string('company_onboarding_source')->nullable();
            $table->string('entity_type')->nullable(); // individual, company
            $table->string('tax_id')->nullable();
            $table->string('phone')->nullable();
            $table->string('address')->nullable();
            $table->string('city')->nullable();
            $table->string('postal_code')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->dropColumn([
                'company_name',
                'company_plan',
                'company_theme',
                'company_onboarding_source',
                'entity_type',
                'tax_id',
                'phone',
                'address',
                'city',
                'postal_code'
            ]);
        });
    }
};
