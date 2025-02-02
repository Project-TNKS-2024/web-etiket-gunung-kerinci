<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();
        $path = public_path('upload');
        if (File::exists($path)) {
            File::deleteDirectory($path);
        }
        $this->call([
            userSeeder::class,
            destinasi_gate::class,
            paket_tiket::class,
            settingSeeder::class,
        ]);
    }
}
