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
            $table->unsignedBigInteger('id_destinasi');
            $table->string('nama');
            $table->decimal('price_traking', 10, 2);
            $table->decimal('price_kemah', 10, 2);
            $table->decimal('price_ansuransi', 10, 2);
            $table->integer('min_visitor');
            $table->boolean('penugasan')->default(false);
            // $table->string('penugasan')->default("false");
            $table->decimal('wni_weekday', 10, 2);
            $table->decimal('wni_weekend', 10, 2);
            $table->decimal('wna_weekend', 10, 2);
            $table->decimal('wna_weekday', 10, 2);
            $table->text('keterangan')->nullable();
            $table->timestamps();

            // Foreign key constraint for id_destinasi if needed
            $table->foreign('id_destinasi')->references('id')->on('destinasis');
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
