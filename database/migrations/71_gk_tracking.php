<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gk_tracking', function (Blueprint $table) {
            $table->id();
            $table->uuid('id_pendaki');
            $table->uuid('id_booking');
            $table->decimal('latitude', 10, 8);
            $table->decimal('longitude', 11, 8);
            $table->decimal('altitude', 7, 2)->nullable();
            $table->decimal('accuracy', 6, 2)->nullable();
            $table->tinyInteger('battery_level')->unsigned()->nullable();
            $table->timestamp('recorded_at');
            $table->timestamps();

            $table->foreign('id_pendaki')->references('id')->on('gk_pendakis')->onDelete('cascade');
            $table->foreign('id_booking')->references('id')->on('gk_bookings')->onDelete('cascade');
            $table->index(['id_pendaki', 'recorded_at'], 'idx_pendaki_time');
            $table->index('id_booking', 'idx_booking');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gk_tracking');
    }
};
