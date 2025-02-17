<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\bio_pendaki;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class userSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // ===================================================== Permision
        $routes = collect(Route::getRoutes())->filter(function ($route) {
            return in_array('check.role:admin', $route->middleware()) && $route->getName();
        })->map(function ($route) {
            return $route->getName();
        })->unique();

        $permissions = [];
        foreach ($routes as $routeName) {
            $permissions[] = Permission::firstOrCreate(['name' => $routeName])->id;
        }

        $this->command->info('Permissions successfully seeded from admin routes!');


        // ===================================================== Role
        // mebuat role
        $superAdminRole = Role::firstOrCreate(['name' => 'Super Admin']);
        $superAdminRole->syncPermissions($permissions);


        // ===================================================== User dan Biodata
        // Create Superadmin
        $admin = User::create([
            'email' => 'superadmin@tnks.com',
            'password' => Hash::make('password'),
            'role' => 'admin',

            'token' => 'superadmin_token',
            'email_verified_at' => now(),
        ]);

        $admin->assignRole($superAdminRole);

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
        // Create User 1
        $biodata1 = bio_pendaki::create([
            'nik' => '1234567834567347',
            'kenegaraan' => 'wni',
            'first_name' => 'User',
            'last_name' => 'One',
            'lampiran_identitas' => 'lampiran1.jpg',
            'no_hp' => '081234567890',
            'no_hp_darurat' => '081298765432',
            'jenis_kelamin' => 'l',
            'tanggal_lahir' => '2000-01-01',
            'provinsi' => '14',
            'kabupaten' => '276',
            'kec' => '4730',
            'desa' => '26624',
            'keterangan' => 'Pendaki Pemula',
            'verified' => 'unverified',
        ]);

        User::create([
            'email' => 'user1@example.com',
            'password' => Hash::make('password'),
            'role' => 'user',
            'id_bio' => $biodata1->id,
            'token' => 'user_token_1',
            'email_verified_at' => now(),
        ]);

        // Create User 2
        $biodata2 = bio_pendaki::create([
            'nik' => '1234567834567348',
            'kenegaraan' => 'wni',
            'first_name' => 'User',
            'last_name' => 'Two',
            'lampiran_identitas' => 'lampiran2.jpg',
            'no_hp' => '081234567891',
            'no_hp_darurat' => '081298765431',
            'jenis_kelamin' => 'p',
            'tanggal_lahir' => '1998-05-15',
            'provinsi' => '8',
            'kabupaten' => '248',
            'kec' => '3341',
            'desa' => '54554',
            'keterangan' => 'Pendaki Berpengalaman',
            'verified' => 'unverified',
        ]);

        User::create([
            'email' => 'user2@example.com',
            'password' => Hash::make('password'),
            'role' => 'user',
            'id_bio' => $biodata2->id,
            'token' => 'user_token_2',
            'email_verified_at' => now(),
        ]);

        // Create User 3
        $biodata3 = bio_pendaki::create([
            'nik' => '1234567834567349',
            'kenegaraan' => 'wna',
            'first_name' => 'User',
            'last_name' => 'Three',
            'lampiran_identitas' => 'lampiran3.jpg',
            'no_hp' => '081234567892',
            'no_hp_darurat' => '081298765430',
            'jenis_kelamin' => 'l',
            'tanggal_lahir' => '1995-08-20',
            'provinsi' => '36',
            'kabupaten' => '197',
            'kec' => '596',
            'desa' => '80177',
            'keterangan' => 'Pendaki Profesional',
            'verified' => 'unverified',
        ]);

        User::create([
            'email' => 'user3@example.com',
            'password' => Hash::make('password'),
            'role' => 'user',
            'id_bio' => $biodata3->id,
            'token' => 'user_token_3',
            'email_verified_at' => null,
        ]);
    }
}
