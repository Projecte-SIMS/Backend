<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reservations', function (Blueprint $table) {
            $table->id();
            
            // RELACIONES
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('vehicle_id')->constrained()->onDelete('cascade');

            // FECHAS
            // scheduled_start: Cuándo quiere el coche.
            $table->timestamp('scheduled_start'); 
            
            // activation_deadline: Tiempo límite para recoger el coche (Start + 20min).
            $table->timestamp('activation_deadline')->nullable();
            
            // CANCELACIONES
            $table->timestamp('cancelled_at')->nullable(); // Fecha de cancelación
            
            // --- NUEVA COLUMNA ---
            // Aquí guardamos la multa si cancela con menos de 24h de antelación.
            // Es nullable porque si el viaje se completa normal, esto estará vacío.
            $table->decimal('cancellation_fee', 8, 2)->nullable();

            // ESTADO
            $table->enum('status', ['pending', 'active', 'expired', 'completed', 'cancelled'])->default('pending');

            $table->timestamps(); // created_at y updated_at
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reservations');
    }
};