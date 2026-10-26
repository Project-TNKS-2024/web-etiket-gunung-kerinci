<?php

namespace Database\Seeders;

use App\Models\destinasi;
use App\Models\GkEmergencyMessage;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EmergencyMessageSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function (): void {
            $destination = destinasi::where('nama', 'Gunung Kerinci')->lockForUpdate()->firstOrFail();

            foreach ([
                '[DEMO] Peringatan jalur licin' => ['Peringatan jalur licin', 'Jalur licin. Berjalan dengan hati-hati dan ikuti arahan petugas.'],
                '[DEMO] Peringatan cuaca buruk' => ['Peringatan cuaca buruk', 'Cuaca buruk. Hentikan perjalanan di tempat aman dan ikuti arahan petugas.'],
            ] as $oldTitle => [$title, $description]) {
                GkEmergencyMessage::where('id_destinasi', $destination->id)->where('title', $oldTitle)
                    ->update(['title' => $title, 'description' => $description]);
            }

            // Pertahankan seluruh pesan yang sudah ada, termasuk pesan operasional.
            if (GkEmergencyMessage::exists()) {
                $this->command?->info('EmergencyMessageSeeder: pesan sudah ada, tidak diubah atau ditambah.');

                return;
            }

            $admin = User::where('email', 'superadmin@tnks.com')->where('role', 'admin')->firstOrFail();
            foreach ([
                'medium' => 'Peringatan jalur licin',
                'high' => 'Peringatan cuaca buruk',
            ] as $severity => $title) {
                // Simpan contoh tanpa memanggil controller/event broadcast.
                GkEmergencyMessage::withoutEvents(fn () => GkEmergencyMessage::create([
                    'id_user' => $admin->id,
                    'id_destinasi' => $destination->id,
                    'type' => 'admin_broadcast',
                    'title' => $title,
                    'description' => $severity === 'medium'
                        ? 'Jalur licin. Berjalan dengan hati-hati dan ikuti arahan petugas.'
                        : 'Cuaca buruk. Hentikan perjalanan di tempat aman dan ikuti arahan petugas.',
                    'severity' => $severity,
                    'status' => 'active',
                ]));
            }
        });
    }
}
