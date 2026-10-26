<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('gk_posts', function (Blueprint $table) {
            $table->dropColumn('info_is_dummy');
        });
    }

    public function down(): void
    {
        Schema::table('gk_posts', function (Blueprint $table) {
            $table->boolean('info_is_dummy')->default(false);
        });
    }
};
