<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('gk_pendakis', function (Blueprint $table) {
            // Fase perjalanan milik PENDATAKI (bukan rombongan). Nilai "naik"/"turun".
            $table->string('tracking_phase', 10)->default('naik')->after('usia');
        });
    }

    public function down(): void
    {
        Schema::table('gk_pendakis', function (Blueprint $table) {
            $table->dropColumn('tracking_phase');
        });
    }
};
