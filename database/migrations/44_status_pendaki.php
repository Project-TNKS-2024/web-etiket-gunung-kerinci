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
        Schema::create('gk_status_pendaki', function (Blueprint $table) {
            $table->id();
            $table->integer('status');
            $table->uuid('id_pendaki');
            $table->text('detail')->nullable();
            $table->timestamps();

            $table->foreign('id_pendaki')->references('id')->on('gk_pendakis')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gk_status_pendaki');
    }
};
