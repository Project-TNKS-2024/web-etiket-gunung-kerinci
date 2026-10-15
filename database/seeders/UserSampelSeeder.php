<?php

namespace Database\Seeders;

use App\Models\bio_pendaki;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSampelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Hanya nama yang mengikuti pengguna; identitas/kontak lain adalah data contoh.
        // Ganti email akun lama tanpa membuat user/biodata baru atau memutus booking.
        $users = [
            [
                'email' => 'satria@gmail.com',
                'legacy_email' => 'user1@example.com',
                'bio' => [
                    'nik' => '1234567834567347',
                    'kenegaraan' => 'ID',
                    'first_name' => 'Satria',
                    'last_name' => 'Bahari',
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
                'email' => 'mukhtada@gmail.com',
                'legacy_email' => 'user2@example.com',
                'bio' => [
                    'nik' => '1234567834567348',
                    'kenegaraan' => 'ID',
                    'first_name' => 'Mukhtada',
                    'last_name' => 'Billah Nasution',
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
                'email' => 'opi@gmail.com',
                'legacy_email' => 'user3@example.com',
                'bio' => [
                    'nik' => '1234567834567349',
                    'kenegaraan' => 'SA',
                    'first_name' => 'Opinur',
                    'last_name' => '',
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
            $existing = User::where('email', $user['email'])->first();
            $legacy = User::where('email', $user['legacy_email'])->first();
            if ($existing && $legacy && $existing->id !== $legacy->id) {
                throw new \RuntimeException("Email {$user['email']} sudah dipakai akun berbeda; penggantian dibatalkan.");
            }
            $existing ??= $legacy;
            if ($existing) {
                $existing->update(['email' => $user['email']]);
                $biodata = $existing->biodata;
                if (!$biodata) {
                    $biodata = bio_pendaki::firstOrCreate(['nik' => $user['bio']['nik']], $user['bio']);
                    $existing->update(['id_bio' => $biodata->id]);
                }
            } else {
                $biodata = bio_pendaki::firstOrCreate(['nik' => $user['bio']['nik']], $user['bio']);
                User::create([
                    'email' => $user['email'],
                    'password' => Hash::make('password'),
                    'role' => 'user',
                    'gauth_type' => 'manual',
                    'id_bio' => $biodata->id,
                    // 'token' => 'user_token_' . explode('@', $user['email'])[0],
                    'email_verified_at' => now(),
                ]);
            }
            // Perbarui nama sampel tanpa mereset password atau biodata lain.
            $biodata->update([
                'first_name' => $user['bio']['first_name'],
                'last_name' => $user['bio']['last_name'],
            ]);
        }
    }
}
