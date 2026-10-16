<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $this->pointTo('gk_paket_tikets');
    }

    public function down(): void
    {
        $this->pointTo('gk_tiket_pendakis');
    }

    private function pointTo(string $table): void
    {
        // Both booking APIs and gk_booking::gktiket() use package IDs.
        // Refuse to reinterpret existing bookings when a matching target is absent.
        if (DB::table('gk_bookings')->whereNotIn('id_tiket', DB::table($table)->select('id'))->exists()) {
            throw new RuntimeException("Relasi booking tidak dapat dialihkan ke $table: ada ID yang tidak cocok. Data booking tidak diubah.");
        }

        Schema::table('gk_bookings', function (Blueprint $blueprint) use ($table) {
            $blueprint->dropForeign(['id_tiket']);
            $blueprint->foreign('id_tiket')->references('id')->on($table)->onDelete('restrict');
        });
    }
};
