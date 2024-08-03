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
        Schema::create('gk_gates', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->boolean('status')->default(true);
            $table->unsignedBigInteger('id_destinasi');
            $table->integer('max_pendaki_hari');
            $table->integer('min_pendaki_booking');
            $table->string('lokasi'); // lokasi maps
            $table->string('lokasi_maps'); // lokasi maps
            $table->text('detail')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gk_gates');
    }
};
