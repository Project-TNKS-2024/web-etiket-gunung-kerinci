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
            $table->integer('status')->default(1);
            $table->enum('kategori', ['taman', 'gunung']);
            $table->string('lokasi');
            $table->text('detail')->nullable();
            $table->text('sop')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('destinasis');
    }
};
