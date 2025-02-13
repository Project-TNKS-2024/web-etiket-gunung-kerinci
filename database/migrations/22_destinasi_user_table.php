<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('destinasi_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('destinasi_id')->constrained()->onDelete('cascade');
            $table->boolean('is_penanggungjawab')->default(false); // Menandakan apakah admin ini bertanggung jawab atau tidak
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('destinasi_user');
    }
};
