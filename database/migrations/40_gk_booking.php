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
            $table->date('tanggal_masuk');
            $table->date('tanggal_keluar');
            $table->enum('kategori_hari', ['wk', 'wd']); //==================
            $table->integer('total_hari');
            $table->integer('total_pendaki_wni');
            $table->integer('total_pendaki_wna');
            $table->unsignedBigInteger('gate_masuk');
            $table->unsignedBigInteger('gate_keluar');
            $table->unsignedTinyInteger('status_booking'); // (1, 2, 3)
            $table->integer('total_pembayaran');
            $table->boolean('status_pembayaran')->default(false);

            $table->text('lampiran_simaksi');
            $table->text('lampiran_stugas');
            $table->string('unique_code')->nullable();
            $table->text('keterangan')->nullable();

            $table->unsignedBigInteger('id_booking_master')->nullable();
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('id_user')->references('id')->on('users')->onDelete('restrict');
            $table->foreign('id_tiket')->references('id')->on('gk_tiket_pendakis')->onDelete('restrict');
            $table->foreign('gate_masuk')->references('id')->on('gk_gates')->onDelete('restrict');
            $table->foreign('gate_keluar')->references('id')->on('gk_gates')->onDelete('restrict');
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
