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
        Schema::create('gk_tiket_pendakis', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_paket_tiket');
            $table->enum('kategori_pendaki', ['wna', 'wni']);
            $table->enum('kategori_hari', ['wk', 'wd']);

            $table->integer('harga_masuk');
            $table->integer('harga_kemah');
            $table->integer('harga_traking');
            $table->integer('harga_ansuransi');
            $table->timestamps();

            // Foreign key constraint for id_destinasi if needed
            $table->foreign('id_paket_tiket')->references('id')->on('gk_paket_tiket');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gk_tiket_pendakis');
    }
};
