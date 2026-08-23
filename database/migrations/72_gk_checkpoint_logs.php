<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gk_checkpoint_logs', function (Blueprint $table) {
            $table->id();
            $table->uuid('id_pendaki');
            $table->unsignedBigInteger('id_post');
            $table->uuid('id_booking');
            $table->enum('method', ['qr', 'gps', 'manual']);
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->boolean('is_manual_override')->default(false);
            $table->timestamp('checked_at');
            $table->timestamps();

            $table->foreign('id_pendaki')->references('id')->on('gk_pendakis')->onDelete('cascade');
            $table->foreign('id_post')->references('id')->on('gk_posts')->onDelete('cascade');
            $table->foreign('id_booking')->references('id')->on('gk_bookings')->onDelete('cascade');
            $table->unique(['id_pendaki', 'id_post', 'id_booking'], 'unique_checkin');
            $table->index(['id_booking', 'checked_at'], 'idx_booking_progress');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gk_checkpoint_logs');
    }
};
