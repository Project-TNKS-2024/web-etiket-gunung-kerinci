<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('gk_checkpoint_logs', function (Blueprint $table) {
            $table->renameColumn('client_event_id', 'id_client_event');
        });
        Schema::table('gk_checkpoint_member_notes', function (Blueprint $table) {
            $table->renameColumn('booking_id', 'id_booking');
            $table->renameColumn('pendaki_id', 'id_pendaki');
            $table->renameColumn('post_id', 'id_post');
        });
        Schema::table('gk_disaster_reports', function (Blueprint $table) {
            $table->renameColumn('client_event_id', 'id_client_event');
        });
    }

    public function down(): void
    {
        Schema::table('gk_disaster_reports', function (Blueprint $table) {
            $table->renameColumn('id_client_event', 'client_event_id');
        });
        Schema::table('gk_checkpoint_member_notes', function (Blueprint $table) {
            $table->renameColumn('id_post', 'post_id');
            $table->renameColumn('id_pendaki', 'pendaki_id');
            $table->renameColumn('id_booking', 'booking_id');
        });
        Schema::table('gk_checkpoint_logs', function (Blueprint $table) {
            $table->renameColumn('id_client_event', 'client_event_id');
        });
    }
};
