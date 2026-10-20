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

            // Pertahankan seluruh pesan yang sudah ada, termasuk pesan operasional.
            if (GkEmergencyMessage::exists()) {
                $this->command?->info('EmergencyMessageSeeder: pesan sudah ada, tidak diubah atau ditambah.');

                return;
            }

            $admin = User::where('email', 'superadmin@tnks.com')->where('role', 'admin')->firstOrFail();
            foreach ([
                'medium' => '[DEMO] Peringatan jalur licin',
                'high' => '[DEMO] Peringatan cuaca buruk',
            ] as $severity => $title) {
                // Simpan contoh tanpa memanggil controller/event broadcast.
                GkEmergencyMessage::withoutEvents(fn () => GkEmergencyMessage::create([
                    'id_user' => $admin->id,
                    'id_destinasi' => $destination->id,
                    'type' => 'admin_broadcast',
                    'title' => $title,
                    'description' => 'DATA DEMO untuk pengujian peringatan dini bahaya. Bukan laporan kondisi lapangan atau peringatan resmi.',
                    'severity' => $severity,
                    'status' => 'active',
                ]));
            }
        });
    }
}
