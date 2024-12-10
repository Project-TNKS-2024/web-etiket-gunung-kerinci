<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('gk_barang_bawaans', function (Blueprint $table) {
            $table->id();
            $table->uuid('id_booking');
            $table->string('nama_barang');
            $table->integer('jumlah');
            $table->timestamps();

            $table->foreign('id_booking')->references('id')->on('gk_bookings')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('gk_barang_bawaans');
    }
};
