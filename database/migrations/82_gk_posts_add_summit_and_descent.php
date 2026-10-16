<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('gk_posts', function (Blueprint $table) {
            $table->boolean('is_summit')->default(false)->after('status');
            // Durasi turun dari pos ini menuju pos berurutan tepat sebelumnya.
            $table->unsignedInteger('estimated_descent_time_minutes')->nullable()
                ->after('estimated_time_minutes');
        });

        // Backfill terarah (diperbolehkan untuk seeding/backfill yang diverifikasi):
        // puncak setiap jalur ditandai dari nama, bukan ditebak dari altitude/urutan.
        // Idempoten: hanya menyetel pos yang memang bernama puncak dan belum ditandai.
        DB::table('gk_posts')
            ->whereIn('nama', self::SUMMIT_NAMES)
            ->where('status', true)
            ->update(['is_summit' => true]);
    }

    public function down(): void
    {
        Schema::table('gk_posts', function (Blueprint $table) {
            $table->dropColumn(['estimated_descent_time_minutes', 'is_summit']);
        });
    }

    private const SUMMIT_NAMES = [
        'Puncak Kerinci',
        'Puncak Gunung Kerinci',
    ];
};
