<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gk_emergency_messages', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_user');
            $table->unsignedBigInteger('id_destinasi');
            $table->uuid('id_pendaki')->nullable();
            $table->uuid('id_booking')->nullable();
            $table->enum('type', ['hiker_alert', 'admin_broadcast']);
            $table->string('title');
            $table->text('description');
            $table->enum('severity', ['low', 'medium', 'high', 'critical']);
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->enum('status', ['active', 'acknowledged', 'resolved'])->default('active');
            $table->unsignedBigInteger('acknowledged_by')->nullable();
            $table->unsignedBigInteger('resolved_by')->nullable();
            $table->timestamp('resolved_at')->nullable();
            $table->timestamps();

            $table->foreign('id_user')->references('id')->on('users');
            $table->foreign('id_destinasi')->references('id')->on('destinasis');
            $table->foreign('id_pendaki')->references('id')->on('gk_pendakis')->onDelete('set null');
            $table->foreign('id_booking')->references('id')->on('gk_bookings')->onDelete('set null');
            $table->index(['status', 'id_destinasi'], 'idx_active');
            $table->index(['severity', 'status'], 'idx_severity');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gk_emergency_messages');
    }
};
