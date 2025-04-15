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
        // menambahkan kolom validator pada tabel bio_pendaki, pembayaran, statusPendaki, gk_booking
        Schema::table('biodatas', function (Blueprint $table) {});
        Schema::table('pembayarans', function (Blueprint $table) {});
        Schema::table('gk_status_pendaki', function (Blueprint $table) {
            $table->unsignedBigInteger('validator')->nullable();
            $table->foreign('validator')->references('id')->on('users');
        });
        Schema::table('gk_bookings', function (Blueprint $table) {
            $table->unsignedBigInteger('validator')->nullable();
            $table->foreign('validator')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
        Schema::table('bio_pendakis', function (Blueprint $table) {
            $table->dropColumn('validator');
        });
        Schema::table('pembayarans', function (Blueprint $table) {
            $table->dropColumn('validator');
        });
        Schema::table('statusPendakis', function (Blueprint $table) {
            $table->dropColumn('validator');
        });
        Schema::table('gk_bookings', function (Blueprint $table) {
            $table->dropColumn('validator');
        });
    }
};
