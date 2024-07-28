<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('gk_bookings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_user');
            $table->unsignedBigInteger('id_tiket');
            $table->unsignedTinyInteger('status'); // (1, 2, 3)
            $table->unsignedBigInteger('id_booking_master')->nullable();
            $table->unsignedInteger('total_pendaki')->nullable();
            $table->integer('wni');
            $table->integer('wna');
            $table->text('keterangan')->nullable();
            $table->string('QR')->nullable();
            $table->boolean('pembayaran')->default(false);
            $table->unsignedBigInteger('gate_masuk');
            $table->unsignedBigInteger('gate_keluar');
            $table->date('tanggal_masuk');
            $table->date('tanggal_keluar');
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('id_user')->references('id')->on('users');
            $table->foreign('id_tiket')->references('id')->on('gk_tikets');
            $table->foreign('gate_masuk')->references('id')->on('gk_gates');
            $table->foreign('gate_keluar')->references('id')->on('gk_gates');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('gk_bookings');
    }
};
