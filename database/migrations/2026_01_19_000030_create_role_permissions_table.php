<?php

use Illuminate\Database\Migrations\Migration;

/**
 * DEPRECATED: Esta migración ya no se usa.
 * La relación role-permissions ahora es manejada por Spatie Permission (2026_01_26_181523_create_permission_tables.php)
 */
return new class extends Migration {
    public function up(): void
    {
        // No-op: Spatie Permission maneja las relaciones
    }

    public function down(): void
    {
        // No-op
    }
};
