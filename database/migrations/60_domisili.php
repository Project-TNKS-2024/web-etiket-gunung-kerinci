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
        Schema::create('d_provinsis', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->unsignedBigInteger("code");
            $table->timestamps();
        });

        Schema::create('d_kabupatens', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->unsignedBigInteger("code");
            $table->string("type");
            $table->string("full_code");
            $table->unsignedBigInteger("provinsi_id");
            $table->timestamps();
        });

        Schema::create('d_kecamatans', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('name');
            $table->unsignedBigInteger("code");
            $table->string("full_code");
            $table->unsignedBigInteger("kabupaten_id");
        });

        Schema::create('d_kelurahans', function (Blueprint $table) {
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
        // Schema::dropIfExists('d_provinsis');
        // Schema::dropIfExists('d_kabupatens');
        // Schema::dropIfExists('d_kecamatans');
        // Schema::dropIfExists('d_kelurahans');
    }
};
