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
        Schema::create('biodatas', function (Blueprint $table) {
            // $table->id();
            $table->uuid('id')->primary();

            $table->string('nik');  //v
            $table->string('kenegaraan'); //v
            $table->string('first_name');
            $table->string('last_name')->nullable(); //v
            $table->string('lampiran_identitas'); //v

            $table->string('no_hp'); //v
            $table->string('no_hp_darurat')->nullable(); //v
            $table->enum('jenis_kelamin', ['l', 'p']); //v
            $table->date('tanggal_lahir'); //v

            $table->string('provinsi')->nullable(); //v
            $table->string('kabupaten')->nullable(); //v
            $table->string('kec')->nullable(); //v
            $table->string('desa')->nullable(); //v

            $table->string('keterangan')->nullable(); //v
            $table->enum('verified', ['unverified', 'pending', 'verified'])->default("unverified"); //v
            $table->timestamp('verified_at')->nullable(); //v

            // $table->unsignedBigInteger('validator')->nullable();
            // $table->foreign('validator')->references('id')->on('users');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('biodatas');
    }
};
