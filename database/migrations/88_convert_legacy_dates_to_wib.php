<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    // Keep this identity for installations that already ran the old migration.
    // A blanket shift is unsafe for TIMESTAMP session conversions/mixed data.
    // Legacy repairs require a verified backup and explicit affected rows.
    public function up(): void
    {
    }

    public function down(): void
    {
        // Never subtract seven hours from records written by a newer release.
    }
};
