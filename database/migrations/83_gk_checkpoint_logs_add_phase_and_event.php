<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('gk_checkpoint_logs', function (Blueprint $table) {
            // Fase kejadian check-in ("naik"/"turun"). Log lama tetap naik.
            $table->string('phase', 10)->default('naik')->after('id_post');
            // Identitas kejadian untuk retry idempoten. Dibuat sekali di mobile.
            $table->uuid('client_event_id')->nullable()->after('phase');

            // MySQL memakai index unik lama sebagai penopang FK id_pendaki
            // (prefix paling kiri). Sediakan index khusus lebih dulu agar index
            // lama bisa dihapus.
            $table->index('id_pendaki', 'idx_logs_pendaki');

            // Ganti unique index 3 kolom dengan 4 kolom (termasuk fase).
            $table->dropUnique('unique_checkin');
            $table->unique(
                ['id_pendaki', 'id_post', 'id_booking', 'phase'],
                'unique_checkin_phase'
            );

            // Unik hanya untuk nilai terisi (MySQL mengizinkan banyak NULL).
            $table->unique('client_event_id', 'unique_client_event_id');
        });
    }

    public function down(): void
    {
        Schema::table('gk_checkpoint_logs', function (Blueprint $table) {
            $table->dropUnique('unique_client_event_id');
            $table->dropUnique('unique_checkin_phase');
            // CATATAN rollback: jika sudah ada dua kunjungan pos yang sama
            // (naik + turun), membuat ulang index 3 kolom bisa gagal. Jangan
            // menghapus log turun otomatis untuk memaksa rollback.
            $table->unique(['id_pendaki', 'id_post', 'id_booking'], 'unique_checkin');
            $table->dropIndex('idx_logs_pendaki');
            $table->dropColumn(['client_event_id', 'phase']);
        });
    }
};
