<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $this->pointTo('gk_paket_tikets');
    }

    public function down(): void
    {
        $this->pointTo('gk_tiket_pendakis');
    }

    private function pointTo(string $table): void
    {
        // Both booking APIs and gk_booking::gktiket() use package IDs.
        // Refuse to reinterpret existing bookings when a matching target is absent.
        if (DB::table('gk_bookings')->whereNotIn('id_tiket', DB::table($table)->select('id'))->exists()) {
            throw new RuntimeException("Relasi booking tidak dapat dialihkan ke $table: ada ID yang tidak cocok. Data booking tidak diubah.");
        }

        // Existing hosting databases may have no FK, a custom name, or an
        // already corrected FK. Do not assume Laravel's generated name exists.
        $foreignKeys = collect(Schema::getForeignKeys('gk_bookings'))
            ->filter(fn (array $key) => in_array('id_tiket', $key['columns'], true));

        foreach ($foreignKeys as $key) {
            if ($key['columns'] !== ['id_tiket']) {
                throw new RuntimeException('Constraint gabungan id_tiket perlu diperiksa manual. Data booking tidak diubah.');
            }
        }

        $target = DB::connection()->getTablePrefix().$table;
        if ($foreignKeys->count() === 1) {
            $key = $foreignKeys->first();
            if ($key['foreign_table'] === $target && $key['foreign_columns'] === ['id']
                && $key['on_delete'] === 'restrict') {
                return;
            }
        }

        foreach ($foreignKeys as $key) {
            Schema::table('gk_bookings', function (Blueprint $blueprint) use ($key): void {
                // SQLite identifies foreign keys by columns, MySQL by name.
                $blueprint->dropForeign($key['name'] ?? ['id_tiket']);
            });
        }

        Schema::table('gk_bookings', function (Blueprint $blueprint) use ($table): void {
            $blueprint->foreign('id_tiket')->references('id')->on($table)->onDelete('restrict');
        });
    }
};
