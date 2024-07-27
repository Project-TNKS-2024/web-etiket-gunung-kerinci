<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('tikets', function (Blueprint $table) {
            $table->id(); // Ini akan membuat kolom id auto-increment
            $table->unsignedBigInteger('id_destinasi');
            $table->string('nama');
            $table->string('spesial')->nullable();
            $table->string('keterangan')->nullable();
            $table->decimal('harga wna', 10, 2);
            $table->decimal('harga wni', 10, 2);
            $table->unsignedBigInteger('gate');
            $table->integer('jenisTiket');
            $table->decimal('harga', 10, 2);
            $table->timestamps(); // Tambahkan created_at dan updated_at

            // Jika perlu, tambahkan indeks atau constraint lainnya di sini
            // ============================================================================================
            $table->foreign('id_destinasi')->references('id')->on('destinasis');
            $table->foreign('gate')->references('id')->on('gk_gates');
        });
    }

    public function down()
    {
        Schema::dropIfExists('tikets');
    }
};
