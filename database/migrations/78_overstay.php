<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Fondasi keterlambatan tiket (overstay):
     * - waktu check-in dan kepulangan aktual per pendaki (terpisah dari waktu pencatatan),
     * - snapshot tarif masuk yang dibekukan saat check-in,
     * - tagihan biaya tambahan final per pendaki + rincian per tanggal,
     * - pembayaran tambahan terpisah dari pembayaran tiket awal,
     * - audit koreksi.
     *
     * Hanya menambah kolom dan tabel baru; tidak mengubah arti field pembayaran tiket awal.
     */
    public function up(): void
    {
        Schema::table('gk_pendakis', function (Blueprint $table) {
            $table->timestamp('checkin_at')->nullable()->after('usia');
            $table->timestamp('actual_checkout_at')->nullable()->after('checkin_at');
            $table->timestamp('checkout_recorded_at')->nullable()->after('actual_checkout_at');
            $table->unsignedBigInteger('checkout_recorded_by')->nullable()->after('checkout_recorded_at');
            $table->text('checkout_reason')->nullable()->after('checkout_recorded_by');

            $table->foreign('checkout_recorded_by')->references('id')->on('users');
        });

        Schema::create('gk_pendaki_tarif_snapshots', function (Blueprint $table) {
            $table->id();
            $table->uuid('id_pendaki');
            $table->enum('kategori', ['wni', 'wna']);
            $table->integer('harga_masuk_wk');
            $table->integer('harga_masuk_wd');
            $table->enum('source', ['snapshot', 'master'])->default('snapshot');
            $table->timestamps();

            $table->unique('id_pendaki');
            $table->foreign('id_pendaki')->references('id')->on('gk_pendakis')->onDelete('cascade');
        });

        Schema::create('gk_overstay_charges', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('id_booking');
            $table->uuid('id_pendaki');
            $table->enum('status', ['final', 'needs_review'])->default('final');
            $table->integer('total');
            $table->json('snapshot')->nullable();
            $table->timestamp('finalized_at')->nullable();
            $table->unsignedBigInteger('finalized_by')->nullable();
            $table->integer('version')->default(1);
            $table->timestamps();

            $table->unique('id_pendaki');
            $table->foreign('id_booking')->references('id')->on('gk_bookings')->onDelete('cascade');
            $table->foreign('id_pendaki')->references('id')->on('gk_pendakis')->onDelete('cascade');
            $table->foreign('finalized_by')->references('id')->on('users');
        });

        Schema::create('gk_overstay_charge_items', function (Blueprint $table) {
            $table->id();
            $table->uuid('id_overstay_charge');
            $table->date('tanggal');
            $table->enum('day_type', ['weekday', 'weekend', 'holiday']);
            $table->enum('kategori', ['wni', 'wna']);
            $table->integer('harga');
            $table->enum('source', ['snapshot', 'master'])->default('snapshot');
            $table->timestamps();

            $table->unique(['id_overstay_charge', 'tanggal']);
            $table->foreign('id_overstay_charge')->references('id')->on('gk_overstay_charges')->onDelete('cascade');
        });

        Schema::create('gk_overstay_payments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('id_booking');
            $table->uuid('id_overstay_charge');
            $table->integer('amount');
            $table->string('method');
            $table->timestamp('paid_at')->nullable();
            $table->unsignedBigInteger('recorded_by')->nullable();
            $table->string('reference')->nullable();
            $table->text('note')->nullable();
            $table->timestamps();

            $table->unique('id_overstay_charge');
            $table->unique('reference');
            $table->foreign('id_booking')->references('id')->on('gk_bookings')->onDelete('cascade');
            $table->foreign('id_overstay_charge')->references('id')->on('gk_overstay_charges')->onDelete('cascade');
            $table->foreign('recorded_by')->references('id')->on('users');
        });

        Schema::create('gk_overstay_audits', function (Blueprint $table) {
            $table->id();
            $table->uuid('id_booking')->nullable();
            $table->uuid('id_pendaki')->nullable();
            $table->string('action');
            $table->json('detail')->nullable();
            $table->unsignedBigInteger('performed_by')->nullable();
            $table->timestamps();

            $table->foreign('id_booking')->references('id')->on('gk_bookings')->onDelete('cascade');
            $table->foreign('id_pendaki')->references('id')->on('gk_pendakis')->onDelete('cascade');
            $table->foreign('performed_by')->references('id')->on('users');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gk_overstay_audits');
        Schema::dropIfExists('gk_overstay_payments');
        Schema::dropIfExists('gk_overstay_charge_items');
        Schema::dropIfExists('gk_overstay_charges');
        Schema::dropIfExists('gk_pendaki_tarif_snapshots');

        Schema::table('gk_pendakis', function (Blueprint $table) {
            $table->dropForeign(['checkout_recorded_by']);
            $table->dropColumn(['checkin_at', 'actual_checkout_at', 'checkout_recorded_at', 'checkout_recorded_by', 'checkout_reason']);
        });
    }
};
