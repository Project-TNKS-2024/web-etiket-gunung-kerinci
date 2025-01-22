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

            'token' => 'superadmin_token',
            'nik_verified_at' => null,
            'email_verified_at' => now(),
        ]);

        // Create Admin
        User::create([
            'email' => 'admin@tnks.com',
            'password' => Hash::make('password'),
            'role' => 'admin',

            'token' => 'admin_token',
            'nik_verified_at' => null,
            'email_verified_at' => now(),
        ]);


        User::create([
            'email' => 'user1@example.com',
            'password' => Hash::make('password'),
            'role' => 'user',
            'id_bio' => null,

            'token' => 'user_token',
            'nik_verified_at' => null,
            'email_verified_at' => now(),
        ]);

        User::create([
            'email' => 'user2@example.com',
            'password' => Hash::make('password'),
            'role' => 'user',
            'id_bio' => null,

            'token' => 'user_token',
            'nik_verified_at' => null,
            'email_verified_at' => now(),
        ]);

        User::create([
            'email' => 'user3@example.com',
            'password' => Hash::make('password'),
            'role' => 'user',
            'token' => 'user_token',
            'nik_verified_at' => null,
            'email_verified_at' => null,
        ]);
    }
}
