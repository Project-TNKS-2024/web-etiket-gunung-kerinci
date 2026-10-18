<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gk_checkpoint_member_notes', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('booking_id')->constrained('gk_bookings')->cascadeOnDelete();
            $table->foreignUuid('pendaki_id')->constrained('gk_pendakis')->cascadeOnDelete();
            $table->foreignId('post_id')->constrained('gk_posts')->cascadeOnDelete();
            $table->string('phase', 10);
            $table->string('note', 500)->default('');
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->unique(['booking_id', 'pendaki_id', 'post_id', 'phase'], 'unique_member_visit_note');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gk_checkpoint_member_notes');
    }
};
