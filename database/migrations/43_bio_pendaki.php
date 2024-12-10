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
            $table->id();

            $table->string('nik')->unique();
            $table->enum('kategori_pendaki', ['wna', 'wni']);
            $table->string('first_name');
            $table->string('last_name');
            $table->string('lampiran_identitas');

            $table->string('no_hp');
            $table->string('no_hp_darurat');
            $table->enum('jenis_kelamin', ['l', 'p']);
            $table->date('tanggal_lahir');

            $table->string('provinsi');
            $table->string('kabupaten');
            $table->string('kec');
            $table->string('desa');

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
