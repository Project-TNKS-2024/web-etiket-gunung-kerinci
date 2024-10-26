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
            $table->uuid('booking_id');
            $table->unsignedBigInteger('tiket_id');
            // enum wna/wni
            $table->enum('kategori_pendaki', ['wna', 'wni']);
            $table->string('nama');
            $table->string('nik');
            $table->string('lampiran_identitas');

            $table->string('no_hp');
            $table->string('no_hp_darurat');
            $table->enum('jenis_kelamin', ['l', 'p']);
            $table->date('tanggal_lahir');
            $table->integer('usia');

            $table->string('provinsi');
            $table->string('kabupaten');
            $table->string('kec');
            $table->string('desa');


            $table->string('lampiran_surat_kesehatan');
            $table->string('lampiran_surat_izin_ortu');
            $table->integer('tagihan');
            $table->timestamps();

            $table->foreign('booking_id')->references('id')->on('gk_bookings')->onDelete('cascade');
            $table->foreign('tiket_id')->references('id')->on('gk_tiket_pendakis')->onDelete('restrict');
        });
    }

    public function down()
    {
        Schema::dropIfExists('gk_pendakis');
    }
};
