<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gk_sos_chats', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_sos');
            $table->unsignedBigInteger('sender_id');
            $table->enum('sender_type', ['hiker', 'admin']);
            $table->enum('type', ['text', 'image']);
            $table->text('content');
            $table->boolean('is_read')->default(false);
            $table->timestamps();

            $table->foreign('id_sos')->references('id')->on('gk_sos')->onDelete('cascade');
            $table->foreign('sender_id')->references('id')->on('users');
            $table->index(['id_sos', 'created_at'], 'idx_sos_messages');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gk_sos_chats');
    }
};
