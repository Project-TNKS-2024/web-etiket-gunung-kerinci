<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();
        // Seed ulang tidak menghapus berkas unggahan yang sudah digunakan data lama.
        $this->call([
            UsersAdminSeeder::class,
            UserSampelSeeder::class,
            destinasi_gate::class,
            AdminDestinationSeeder::class,
            paket_tiket::class,
            settingSeeder::class,
            // permisionSeeder me-reset seluruh permission & role dari middleware route,
            // jadi harus jalan SEBELUM seeder permission tambahan (EmergencyPermissionSeeder).
            permisionSeeder::class,
            EmergencyPermissionSeeder::class,
            RescueContactSeeder::class,
            TrailPostSeeder::class,
            BookingSeeder::class,
            CompletedBookingSeeder::class,
            BookingStatusSeeder::class,
            EmergencyMessageSeeder::class,
        ]);
    }
}
