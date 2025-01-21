<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('pengajuans', function (Blueprint $table) {
            $table->id();
            $table->uuid('booking_id'); // UUID for booking_id
            $table->string('bukti'); // File path for the proof
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending'); // Enum for status
            $table->text('keterangan')->nullable(); // Nullable description
            $table->timestamps(); // created_at and updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengajuans');
    }
};
