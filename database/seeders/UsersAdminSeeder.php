<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\bio_pendaki;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class UsersAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Superadmin
        $admin = User::create([
            'email' => 'superadmin@tnks.com',
            'password' => Hash::make('password'),
            'role' => 'admin',

            'token' => 'superadmin_token',
            'email_verified_at' => now(),
        ]);


        // Create Admin
        User::create([
            'email' => 'admin@tnks.com',
            'password' => Hash::make('password'),
            'role' => 'admin',

            'token' => 'admin_token',
            'email_verified_at' => now(),
        ]);

        for ($i = 1; $i < 3; $i++) {
            User::create([
                'email' => 'superadmi' . $i . 'n@tnks.com',
                'password' => Hash::make('password'),
                'role' => 'admin',

                'token' => 'admin_token',
                'email_verified_at' => now(),
            ]);
        }
    }
}
