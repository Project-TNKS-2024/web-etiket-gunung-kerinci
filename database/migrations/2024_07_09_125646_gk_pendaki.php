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
            $table->string('nama');
            $table->string('nik');
            $table->string('no_hp');
            $table->string('alamat');
            $table->string('tanggal_lahir');
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
