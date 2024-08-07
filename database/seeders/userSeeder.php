<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
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
            'email' => 'superadmin@example.com',
            'nik' => '1234567890',
            'token' => 'superadmin_token',
            'no_hp' => '08123456789',
            'role' => 'superadmin',
            'password' => Hash::make('password'),
            'fullname' => 'Superadmin User',
            'path_foto' => 'superadmin.jpg',
            'email_verified_at' => now(),
        ]);

        // Create Admin
        User::create([
            'email' => 'admin@example.com',
            'nik' => '0987654321',
            'token' => 'admin_token',
            'no_hp' => '08765432109',
            'role' => 'admin',
            'password' => Hash::make('password'),
            'fullname' => 'Admin User',
            'path_foto' => 'admin.jpg',
            'email_verified_at' => now(),
        ]);

        // Create two Users
        User::create([
            'email' => 'user1@example.com',
            'nik' => '0887654321',
            'token' => 'admin_token',
            'no_hp' => '08765432509',
            'role' => 'user',
            'password' => Hash::make('password'),
            'fullname' => 'User 1',
            'path_foto' => 'user 1.jpg',
        ]);
        // Create two Users
        User::create([
            'email' => 'user2@example.com',
            'nik' => '088765456321',
            'token' => 'admin_token',
            'no_hp' => '08765782509',
            'role' => 'user',
            'password' => Hash::make('password'),
            'fullname' => 'User 2',
            'path_foto' => 'user 1.jpg',
        ]);
    }
}
