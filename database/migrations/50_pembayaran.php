<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('pembayarans', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('id_booking');
            $table->string('spesial')->default(null); //snap_code
            $table->decimal('amount', 10, 2); // Jumlah pembayarans
            $table->string('status'); // Status pembayarans (contoh: pending, success, failed)
            $table->string('payment_method'); // Metode pembayarans (contoh: transfer bank, kartu kredit, dll.)
            $table->date('deadline'); // Batas waktu pembayarans
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('pembayarans');
    }
};
