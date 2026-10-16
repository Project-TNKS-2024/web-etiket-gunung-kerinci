<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Backfill fase pendaki: pendaki yang sudah punya check-in summit naik valid
        // menjadi "turun"; sisanya tetap "naik". Idempoten (tidak menggandakan data,
        // tidak membuat catatan turun fiktif, tidak mengubah status booking/checkout).
        $summitIds = DB::table('gk_posts')
            ->where('is_summit', true)
            ->where('status', true)
            ->pluck('id');

        if ($summitIds->isEmpty()) {
            return;
        }

        $pendakiIds = DB::table('gk_checkpoint_logs')
            ->whereIn('id_post', $summitIds)
            ->where('phase', 'naik')
            ->pluck('id_pendaki')
            ->unique();

        if ($pendakiIds->isEmpty()) {
            return;
        }

        DB::table('gk_pendakis')
            ->whereIn('id', $pendakiIds)
            ->update(['tracking_phase' => 'turun']);
    }

    public function down(): void
    {
        // Backfill fase dibalik: semua pendaki kembali "naik".
        DB::table('gk_pendakis')->update(['tracking_phase' => 'naik']);
    }
};
