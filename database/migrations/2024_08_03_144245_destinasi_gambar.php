<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('destinasi_gambars', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_destinasi');
            $table->string('nama');
            $table->text('detail');
            $table->text('src');
            $table->timestamps();

            // Foreign key constraint for id_destinasi if needed
            $table->foreign('id_destinasi')->references('id')->on('destinasi');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('destinasi_gambars');
    }
};
