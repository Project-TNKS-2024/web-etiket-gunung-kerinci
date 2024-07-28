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
        Schema::create('gk_tikets', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_golongan');
            $table->unsignedBigInteger('id_kategori');
            $table->unsignedBigInteger('id_gate');
            $table->unsignedBigInteger('id_destinasi');
            $table->string('nama');
            $table->decimal('price_traking', 10, 2);
            $table->decimal('price_kemah', 10, 2);
            $table->decimal('price_ansuransi', 10, 2);
            $table->integer('min_visitor');
            $table->boolean('penugasan')->default(false);
            $table->text('keterangan')->nullable();
            $table->decimal('harga');
            $table->string('tipe', 3);
            $table->timestamps();

            // Foreign key constraint for id_destinasi if needed
            $table->foreign('id_golongan')->references('id')->on('golongans');
            $table->foreign('id_destinasi')->references('id')->on('destinasis');
            $table->foreign('id_kategori')->references('id')->on('kategoris');
            $table->foreign('id_gate')->references('id')->on('gk_gates');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gk_tikets');
    }
};
