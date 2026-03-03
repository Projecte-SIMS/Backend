<?php

use Illuminate\Database\Migrations\Migration;

/**
 * DEPRECATED: Esta migración ya no se usa.
 * Los roles ahora son manejados por Spatie Permission (2026_01_26_181523_create_permission_tables.php)
 */
return new class extends Migration {
    public function up(): void
    {
        // No-op: Spatie Permission maneja los roles
    }

    public function down(): void
    {
        // No-op
    }
};
