<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
// masih dk seuaai
{
    public function up()
    {
        Schema::create('gk_pendakis', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('booking_id');
            $table->boolean('wni')->default(false);
            $table->string('nik');
            $table->string('nama');
            $table->string('lampiran_identitas');
            $table->string('no_hp');
            $table->string('no_hp_darurat');
            $table->date('tanggal_lahir');
            $table->integer('usia');
            $table->integer('tinggi');
            $table->integer('berat');
            $table->string('alamat');
            $table->string('provinsi');
            $table->string('kabupaten');
            $table->string('kec');
            $table->string('desa');
            $table->string('lampiran_surat_kesehatan');
            $table->string('lampiran_simaksi');
            $table->boolean('ketua');
            $table->timestamps();

            $table->foreign('booking_id')->references('id')->on('gk_bookings')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('gk_pendakis');
    }
};
