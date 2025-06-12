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

        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('email')->unique();  //v
            $table->string('password');  //v
            // $table->string('role')->default('user'); 
            $table->enum('role', ['user', 'admin'])->default('user');  //v         
            $table->string('avatar')->nullable();  //v

            $table->uuid('id_bio')->unique()->nullable();  //v

            // $table->string('token')->nullable();   //-------------------
            $table->timestamp('email_verified_at')->nullable();  //v
            $table->string('gauth_id')->nullable();  //v
            // $table->string('gauth_type')->nullable(); //v
            $table->enum('gauth_type', ['manual', 'google'])->default('manual'); //v
            $table->rememberToken();
            $table->timestamps();

            $table->foreign('id_bio')->references('id')->on('biodatas');
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
