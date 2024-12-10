<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\bio_pendaki;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class userSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Superadmin
        User::create([
            'email' => 'superadmin@tnks.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'nik' => '1234567890',

            'token' => 'superadmin_token',
            'nik_verified_at' => null,
            'email_verified_at' => now(),
        ]);

        // Create Admin
        User::create([
            'email' => 'admin@tnks.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'nik' => '0987654321',

            'token' => 'admin_token',
            'nik_verified_at' => null,
            'email_verified_at' => now(),
        ]);

        // Create two Users
        $data = User::create([
            'email' => 'user1@example.com',
            'password' => Hash::make('password'),
            'role' => 'user',
            'nik' => '5228432867528394',

            'token' => 'user_token',
            'nik_verified_at' => now(),
            'email_verified_at' => now(),
        ]);

        bio_pendaki::create([
            'nik' => $data->nik,
            'kategori_pendaki' => 'wni',
            'first_name' => 'Bilhuda',
            'last_name' => '',
            'lampiran_identitas' => 'lampiran_identitas.jpg',
            'no_hp' => '1234567890',
            'no_hp_darurat' => '0987654321',
            'jenis_kelamin' => 'l',
            'tanggal_lahir' => '2000-01-01',
            'provinsi' => '',
            'kabupaten' => '',
            'kec' => '',
            'desa' => '',
        ]);

        $data = User::create([
            'email' => 'user2@example.com',
            'password' => Hash::make('password'),
            'role' => 'user',
            'nik' => '1118433857827394',

            'token' => 'user_token',
            'nik_verified_at' => now(),
            'email_verified_at' => now(),
        ]);

        bio_pendaki::create([
            'nik' => $data->nik,
            'kategori_pendaki' => 'wni',
            'first_name' => 'Muhammmad',
            'last_name' => 'Elfatih',
            'lampiran_identitas' => 'lampiran_identitas.jpg',
            'no_hp' => '1234567890',
            'no_hp_darurat' => '0987654321',
            'jenis_kelamin' => 'l',
            'tanggal_lahir' => '2000-01-01',
            'provinsi' => '',
            'kabupaten' => '',
            'kec' => '',
            'desa' => '',
        ]);

        User::create([
            'email' => 'user3@example.com',
            'password' => Hash::make('password'),
            'role' => 'user',
            'nik' => '2228432867528394',
            'token' => 'user_token',
            'nik_verified_at' => null,
            'email_verified_at' => null,
        ]);
    }
}
