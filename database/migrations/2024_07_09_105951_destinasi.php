<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('destinasis', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->boolean('status');
            $table->enum('kategori', ['taman', 'gunung']);
            $table->string('lokasi');
            $table->text('detail');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('destinasis');
    }
};
