<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gk_posts', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->integer('urutan');
            $table->decimal('latitude', 10, 8);
            $table->decimal('longitude', 11, 8);
            $table->integer('altitude')->nullable();
            $table->integer('radius_meter')->default(150);
            $table->unsignedBigInteger('id_gate');
            $table->string('qr_code_value')->unique()->nullable();
            $table->boolean('status')->default(true);
            $table->text('detail')->nullable();
            $table->timestamps();

            $table->foreign('id_gate')->references('id')->on('gk_gates')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gk_posts');
    }
};
