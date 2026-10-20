<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // Legacy application dates were UTC wall times. Run once before seeding.
    // DATE values and epoch integers are deliberately not converted.
    public function up(): void
    {
        $this->shiftDates(7);
    }

    public function down(): void
    {
        $this->shiftDates(-7);
    }

    private function shiftDates(int $hours): void
    {
        $driver = DB::connection()->getDriverName();
        if (!in_array($driver, ['mysql', 'mariadb', 'sqlite'], true)) {
            throw new RuntimeException('Konversi WIB hanya mendukung MySQL/MariaDB dan SQLite.');
        }

        $grammar = DB::connection()->getQueryGrammar();
        DB::transaction(function () use ($driver, $grammar, $hours): void {
            foreach (Schema::getTableListing() as $table) {
                $updates = [];
                foreach (Schema::getColumns($table) as $column) {
                    if (!in_array(strtolower($column['type_name']), ['timestamp', 'datetime'], true)) {
                        continue;
                    }
                    $name = $grammar->wrap($column['name']);
                    $expression = $driver === 'sqlite'
                        ? "datetime($name, '$hours hours')"
                        : "DATE_ADD($name, INTERVAL $hours HOUR)";
                    $updates[$column['name']] = DB::raw($expression);
                }
                if ($updates !== [] && DB::table($table)->exists()) {
                    DB::table($table)->update($updates);
                }
            }
        });
    }
};
