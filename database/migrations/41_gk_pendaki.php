<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\File;

return new class extends Migration
// masih dk seuaai
{
    public function up()
    {
        Schema::create('gk_pendakis', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('booking_id');
            $table->integer('tagihan')->nullable();

            // $table->string('nik');
            $table->uuid('id_bio');

            $table->integer('usia');
            $table->integer('tinggi');
            $table->integer('berat');

            $table->string('lampiran_surat_izin_ortu')->nullable();
            $table->timestamps();

            $table->foreign('booking_id')->references('id')->on('gk_bookings')->onDelete('cascade');
            $table->foreign('id_bio')->references('id')->on('biodatas')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('gk_pendakis');
        // hapus hasil upload

    }
};
