<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('gk_posts', function (Blueprint $table) {
            $table->string('mountain_status', 100)->nullable();
            $table->string('water_source_status', 50)->default('Belum diketahui');
            $table->string('camping_status', 50)->default('Belum diketahui');
            $table->float('temperature_c')->nullable();
            $table->unsignedInteger('distance_to_next_m')->nullable();
            $table->unsignedInteger('estimated_time_minutes')->nullable();
            $table->unsignedInteger('distance_to_summit_m')->nullable();
            $table->boolean('info_is_dummy')->default(false);
            $table->text('info_note')->nullable();
            $table->timestamp('info_updated_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('gk_posts', function (Blueprint $table) {
            $table->dropColumn([
                'mountain_status', 'water_source_status', 'camping_status', 'temperature_c',
                'distance_to_next_m', 'estimated_time_minutes', 'distance_to_summit_m',
                'info_is_dummy', 'info_note', 'info_updated_at',
            ]);
        });
    }
};
