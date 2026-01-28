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

        // FECHAS CLAVE PARA TU REGLA DE 20 MINUTOS
        // created_at (automático al final) es cuándo pulsó el botón.
        
        // scheduled_start: Cuándo quiere el coche (Ej: 17:00). 
        // Si es reserva inmediata, aquí guardas la hora actual.
        $table->timestamp('scheduled_start'); 
        
        // activation_deadline: scheduled_start + 20 minutos.
        // Lo guardamos para hacer consultas rápidas de "qué reservas han caducado".
        $table->timestamp('activation_deadline')->nullable();
        
        $table->timestamp('cancelled_at')->nullable(); // Para saber si multar

        // ESTADO
        // pending: reservado pero no ha llegado.
        // active: motor encendido (ya hay trip).
        // expired: pasaron los 20 min y no llegó.
        // cancelled: usuario canceló.
        // completed: viaje terminado.
        $table->enum('status', ['pending', 'active', 'expired', 'completed', 'cancelled'])->default('pending');

        $table->timestamps(); // created_at y updated_at
        $table->softDeletes();
    });
}
};
