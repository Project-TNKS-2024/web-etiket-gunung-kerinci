<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('galeri_images', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->boolean('private')->default(false);
            $table->binary('image');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('galeri_images');
    }
};
