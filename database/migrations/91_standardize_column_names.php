<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->renameColumn('canDelete', 'can_delete');
        });
        Schema::table('destinasis', function (Blueprint $table) {
            $table->renameColumn('statusGunung', 'status_gunung');
        });
        Schema::table('gk_bookings', function (Blueprint $table) {
            $table->renameColumn('dataStruk', 'data_struk');
        });
    }

    public function down(): void
    {
        Schema::table('gk_bookings', function (Blueprint $table) {
            $table->renameColumn('data_struk', 'dataStruk');
        });
        Schema::table('destinasis', function (Blueprint $table) {
            $table->renameColumn('status_gunung', 'statusGunung');
        });
        Schema::table('settings', function (Blueprint $table) {
            $table->renameColumn('can_delete', 'canDelete');
        });
    }
};
