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
            $table->id();
            $table->uuid('booking_id');
            // enum wna/wni
            $table->enum('kategori_pendaki', ['wna', 'wni']);
            $table->string('first_name');
            $table->string('last_name');
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
        });
    }

    public function down()
    {
        Schema::dropIfExists('gk_pendakis');
        // hapus hasil upload

    }
};
