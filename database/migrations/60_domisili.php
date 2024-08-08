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
        //
        Schema::create('provinsis', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('name');
            $table->unsignedBigInteger("code");
        });

        Schema::create('kabupatens', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('name');
            $table->unsignedBigInteger("code");
            $table->string("type");
            $table->string("full_code");
            $table->unsignedBigInteger("provinsi_id");
        });

        Schema::create('kecamatans', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('name');
            $table->unsignedBigInteger("code");
            $table->string("full_code");
            $table->unsignedBigInteger("kabupaten_id");
        });

        Schema::create('kelurahans', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('name');
            $table->unsignedBigInteger("code");
            $table->string("full_code");
            $table->string("pos_code");
            $table->unsignedBigInteger("kecamatan_id");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
