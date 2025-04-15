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
        Schema::table('gk_bookings', function (Blueprint $table) {
            $table->index('tanggal_masuk');
            $table->index('tanggal_keluar');
            $table->index('status_booking');
        });

        Schema::table('pembayarans', function (Blueprint $table) {
            $table->index('id_booking');
            $table->index('status');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('gk_bookings', function (Blueprint $table) {
            $table->dropIndex(['tanggal_masuk']);
            $table->dropIndex(['tanggal_keluar']);
            $table->dropIndex(['status_booking']);
        });

        Schema::table('pembayarans', function (Blueprint $table) {
            $table->dropIndex(['id_booking']);
            $table->dropIndex(['status']);
            $table->dropIndex(['created_at']);
        });
    }
};
