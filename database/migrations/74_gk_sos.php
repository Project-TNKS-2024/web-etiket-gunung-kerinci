<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gk_sos', function (Blueprint $table) {
            $table->id();
            $table->uuid('id_pendaki');
            $table->uuid('id_booking');
            $table->unsignedBigInteger('id_destinasi');
            $table->enum('severity', ['low', 'medium', 'high']);
            $table->text('message')->nullable();
            $table->decimal('latitude', 10, 8);
            $table->decimal('longitude', 11, 8);
            $table->decimal('altitude', 7, 2)->nullable();
            $table->enum('status', ['active', 'acknowledged', 'dispatched', 'resolved', 'false_alarm'])->default('active');
            $table->unsignedBigInteger('acknowledged_by')->nullable();
            $table->unsignedBigInteger('resolved_by')->nullable();
            $table->timestamp('resolved_at')->nullable();
            $table->text('resolution_notes')->nullable();
            $table->timestamps();

            $table->foreign('id_pendaki')->references('id')->on('gk_pendakis')->onDelete('cascade');
            $table->foreign('id_booking')->references('id')->on('gk_bookings')->onDelete('cascade');
            $table->foreign('id_destinasi')->references('id')->on('destinasis');
            $table->index(['status', 'id_destinasi'], 'idx_active_sos');
            $table->index('id_pendaki', 'idx_pendaki');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gk_sos');
    }
};
