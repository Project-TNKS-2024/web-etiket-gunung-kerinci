<?php

namespace Database\Seeders;

use App\Models\bio_pendaki;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSampelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            [
                'email' => 'user1@example.com',
                'bio' => [
                    'nik' => '1234567834567347',
                    'kenegaraan' => 'ID',
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
                    'verified' => 'verified',
                ],
            ],
            [
                'email' => 'user2@example.com',
                'bio' => [
                    'nik' => '1234567834567348',
                    'kenegaraan' => 'ID',
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
                    'verified' => 'verified',
                ],
            ],
            [
                'email' => 'user3@example.com',
                'bio' => [
                    'nik' => '1234567834567349',
                    'kenegaraan' => 'SA',
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
                    'verified' => 'verified',
                ],
            ],
        ];

        foreach ($users as $user) {
            if (!User::where('email', $user['email'])->exists()) {
                $biodata = bio_pendaki::create($user['bio']);
                User::create([
                    'email' => $user['email'],
                    'password' => Hash::make('password'),
                    'role' => 'user',
                    'gauth_type' => 'manual',
                    'id_bio' => $biodata->id,
                    'token' => 'user_token_' . explode('@', $user['email'])[0],
                    'email_verified_at' => now(),
                ]);
            }
        }




        for ($i = 1; $i <= 50; $i++) {
            $email = 'user' . $i . '@example.com';
            if (User::where('email', $email)->exists()) {
                continue;
            }

            $biodata = bio_pendaki::create([
                'nik' => '111sampel' . $i,
                'kenegaraan' => 'ID',
                'first_name' => 'User',
                'last_name' => $i,
                'lampiran_identitas' => 'lampiran1.jpg',
                'no_hp' => '081234567890',
                // beri nilai random untuk jenis kelamin l/p dan tangggal lahir >17 tahun
                'jenis_kelamin' => 'l',
                'tanggal_lahir' => '2000-01-01',

                'provinsi' => '14',
                'kabupaten' => '276',
                'kec' => '4730',
                'desa' => '26624',
                'keterangan' => 'Pendaki Pemula',
                'verified' => 'verified',
            ]);

            User::create([
                'email' => $email,
                'password' => Hash::make('password'),
                'role' => 'user',
                'token' => 'user_token',
                'email_verified_at' => now(),
                'id_bio' => $biodata->id,
            ]);
        }
    }
}
