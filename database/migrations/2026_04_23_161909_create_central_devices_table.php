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
        Schema::create('central_devices', function (Blueprint $table) {
            $table->id();
            $table->string('hardware_id')->unique(); // Ej: raspi-123
            $table->string('display_name'); // Ej: Coche 01
            $table->string('ip_address');
            $table->string('ssh_user')->default('pi');
            $table->string('tenant_id')->nullable(); // Empresa a la que pertenece
            $table->string('api_key')->nullable();
            $table->string('last_status')->nullable();
            $table->timestamp('last_sync_at')->nullable();
            $table->boolean('use_docker')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('central_devices');
    }
};
