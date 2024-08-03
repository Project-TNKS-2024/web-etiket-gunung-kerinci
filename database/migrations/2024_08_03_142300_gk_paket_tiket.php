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
        Schema::create('gk_paket_tikets', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_destinasi');
            $table->string('nama');
            $table->integer('min_pendaki');
            $table->boolean('penugasan')->default(false);
            $table->text('keterangan')->nullable();
            $table->timestamps();

            // Foreign key constraint for id_destinasi if needed
            $table->foreign('id_destinasi')->references('id')->on('destinasis');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gk_paket_tikets');
    }
};
