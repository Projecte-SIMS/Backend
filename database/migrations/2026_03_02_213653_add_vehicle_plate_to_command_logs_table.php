<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('command_logs', function (Blueprint $table) {
            $table->string('vehicle_plate')->nullable()->after('device_id');
        });
    }

    public function down(): void
    {
        Schema::table('command_logs', function (Blueprint $table) {
            $table->dropColumn('vehicle_plate');
        });
    }
};
