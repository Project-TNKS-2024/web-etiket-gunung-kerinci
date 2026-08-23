<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gk_disaster_reports', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_user');
            $table->unsignedBigInteger('id_destinasi');
            $table->uuid('id_booking')->nullable();
            $table->string('potensi_bencana');
            $table->text('deskripsi');
            $table->string('lokasi');
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->string('lampiran')->nullable();
            $table->enum('status', ['pending', 'verified', 'broadcast', 'resolved', 'rejected'])->default('pending');
            $table->unsignedBigInteger('verified_by')->nullable();
            $table->timestamp('verified_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('id_user')->references('id')->on('users');
            $table->foreign('id_destinasi')->references('id')->on('destinasis');
            $table->foreign('id_booking')->references('id')->on('gk_bookings')->onDelete('set null');
            $table->index(['status', 'id_destinasi'], 'idx_status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gk_disaster_reports');
    }
};
