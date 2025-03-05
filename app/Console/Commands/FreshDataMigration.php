<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

class FreshDataMigration extends Command
{

    protected $signature = 'migrate:freshdata';
    protected $description = 'Migrate fresh and seed all data except users table';

    public function handle()
    {
        $this->info("Menghapus semua tabel kecuali tabel yang dipertahankan...");

        // Ambil semua tabel dari database
        $tables = DB::select("SHOW TABLES");
        $dbName = env('DB_DATABASE');
        $tableKey = "Tables_in_$dbName";

        // Daftar tabel yang tidak boleh dihapus
        $excludedTables = [
            'users',
            'password_reset_tokens',
            'permissions',
            'roles',
            'model_has_permissions',
            'model_has_roles',
            'role_has_permissions'
        ];

        // Filter tabel yang akan dihapus
        $tablesToDelete = [];
        foreach ($tables as $table) {
            if (!in_array($table->$tableKey, $excludedTables)) {
                $tablesToDelete[] = $table->$tableKey;
            }
        }

        // Jika ada tabel yang perlu dihapus, jalankan perintah DROP TABLE
        if (!empty($tablesToDelete)) {
            $tablesString = implode(', ', $tablesToDelete);
            DB::statement("SET FOREIGN_KEY_CHECKS=0");
            DB::statement("DROP TABLE $tablesString");
            DB::statement("SET FOREIGN_KEY_CHECKS=1");
        }

        $this->info("Menjalankan migrasi hanya untuk tabel yang dihapus...");

        // Ambil semua file migration
        $migrationFiles = glob(database_path('migrations/*.php'));

        // Filter migrasi hanya untuk tabel yang dihapus
        $migrationPaths = [];
        foreach ($migrationFiles as $file) {
            // Lewati migrasi untuk tabel yang tidak dihapus
            foreach ($excludedTables as $excludedTable) {
                if (strpos(file_get_contents($file), "Schema::create('$excludedTable'") !== false) {
                    continue 2; // Skip iterasi ini
                }
            }
            $migrationPaths[] = 'database/migrations/' . basename($file);
        }

        // Jalankan migrasi hanya untuk tabel yang dihapus
        foreach ($migrationPaths as $path) {
            Artisan::call('migrate', ['--path' => $path], $this->getOutput());
        }

        $this->info("Menjalankan seeder...");
        Artisan::call('db:seed', [], $this->getOutput());

        $this->info("Migrate fresh selesai tanpa menghapus tabel yang dipertahankan.");
    }
}
