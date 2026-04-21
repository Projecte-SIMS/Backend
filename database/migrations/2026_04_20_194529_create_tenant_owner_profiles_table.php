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
        // 1. Crear la tabla de perfiles de propietario
        Schema::create('tenant_owner_profiles', function (Blueprint $table) {
            $table->id();
            $table->string('tenant_id')->unique();
            
            // Información del propietario/persona
            $table->string('owner_name');
            $table->string('owner_email');
            
            // Datos fiscales y de contacto
            $table->string('entity_type')->default('company'); // individual, company
            $table->string('company_name')->nullable();
            $table->string('tax_id')->nullable();
            $table->string('phone')->nullable();
            $table->string('address')->nullable();
            $table->string('city')->nullable();
            $table->string('postal_code')->nullable();
            
            $table->timestamps();

            // Relación con tenants
            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
        });

        // 2. Limpiar la tabla tenants de las columnas que ya no pertenecen ahí
        Schema::table('tenants', function (Blueprint $table) {
            $table->dropColumn([
                'company_name',
                'entity_type',
                'tax_id',
                'phone',
                'address',
                'city',
                'postal_code'
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->string('company_name')->nullable();
            $table->string('entity_type')->nullable();
            $table->string('tax_id')->nullable();
            $table->string('phone')->nullable();
            $table->string('address')->nullable();
            $table->string('city')->nullable();
            $table->string('postal_code')->nullable();
        });

        Schema::dropIfExists('tenant_owner_profiles');
    }
};
