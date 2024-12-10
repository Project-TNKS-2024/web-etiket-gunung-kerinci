<?php

namespace Database\Seeders;

use App\Models\bio_pendaki;
use App\Models\gk_booking;
use App\Models\gk_gates;
use App\Models\gk_pendaki;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;

class Booking extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $bookings = [
            gk_booking::create([
                'id_user' => 3,
                'id_tiket' => 1, // Assuming ticket id 1 exists
                'tanggal_masuk' => Carbon::now()->format('Y-m-d'),
                'tanggal_keluar' => Carbon::now()->addDays(2)->format('Y-m-d'),
                'kategori_hari' => 'wk',
                'total_hari' => 2,
                'total_pendaki_wni' => 2,
                'total_pendaki_wna' => 0,
                'gate_masuk' => 1, // Assuming gate id 1 exists
                'gate_keluar' => 1, // Assuming gate id 1 exists
                'status_booking' => 1,
                'total_pembayaran' => 500000,
                'status_pembayaran' => false,
                'lampiran_simaksi' => '',
                'lampiran_stugas' => '',
                'unique_code' => Str::random(10),
                'keterangan' => 'Booking for a weekend trip',
                'id_booking_master' => null,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]),
            gk_booking::create([
                'id_user' => 4,
                'id_tiket' => 2, // Assuming ticket id 2 exists
                'tanggal_masuk' => Carbon::now()->format('Y-m-d'),
                'tanggal_keluar' => Carbon::now()->addDays(3)->format('Y-m-d'),
                'kategori_hari' => 'wd',
                'total_hari' => 3,
                'total_pendaki_wni' => 1,
                'total_pendaki_wna' => 1,
                'gate_masuk' => 2, // Assuming gate id 2 exists
                'gate_keluar' => 2, // Assuming gate id 2 exists
                'status_booking' => 2,
                'total_pembayaran' => 750000,
                'status_pembayaran' => true,
                'lampiran_simaksi' => '',
                'lampiran_stugas' => '',
                'unique_code' => Str::random(10),
                'keterangan' => 'Booking for a weekday trip',
                'id_booking_master' => null,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]),
        ];

        foreach ($bookings as $booking) {
            for ($i = 1; $i <= 2; $i++) {

                $data = gk_pendaki::create([
                    'booking_id' => $booking->id,
                    'tagihan' => 0,

                    'nik' => Str::random(16),
                    'usia' => 25 + $i,
                    'tinggi' => 13 + $i,
                    'berat' => 25 + $i,


                    'lampiran_surat_izin_ortu' => '',
                ]);

                bio_pendaki::create([
                    'nik' => $data->nik,
                    'kategori_pendaki' => 'wni',
                    'first_name' => 'Pendaki',
                    'last_name' => 'gunung' . $i,
                    'lampiran_identitas' => 'path/to/identitas' . $i . '.pdf',

                    'no_hp' => '08123456789' . $i,
                    'no_hp_darurat' => '08123456780' . $i,

                    'jenis_kelamin' => 'l',
                    'tanggal_lahir' => Carbon::now()->subYears(25 + $i)->format('Y-m-d'),

                    'provinsi' =>   $i,
                    'kabupaten' =>  $i,
                    'kec' =>  $i,
                    'desa' =>  $i,

                ]);
            }
        }
    }
}
