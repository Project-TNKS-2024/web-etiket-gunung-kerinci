<?php

namespace Database\Seeders;

use App\Models\GkPost;
use App\Models\gk_gates;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TrailPostSeeder extends Seeder
{
    // DATA DEMO: [sumber air, berkemah, suhu C, jarak berikutnya m, waktu menit, jarak puncak m].
    // Angka ditentukan langsung, bukan hasil pengukuran atau perhitungan jalur.
    private const DEMO_INFO = [
        'Kersik Tuo' => ['Tersedia', 'Boleh', 20, 2000, 45, 9400],
        'Pintu Rimba' => ['Belum diketahui', 'Tidak boleh', 19, 900, 30, 7400],
        'Bangku Panjang' => ['Tidak tersedia', 'Tidak boleh', 18, 700, 25, 6500],
        'Batu Lumut' => ['Tersedia', 'Tidak boleh', 17, 1000, 40, 5800],
        'Pondok Panorama' => ['Tersedia', 'Belum diketahui', 15, 1300, 60, 4800],
        'Shelter 1' => ['Tersedia', 'Boleh', 13, 1200, 75, 3500],
        'Shelter 2' => ['Tersedia', 'Boleh', 10, 600, 45, 2300],
        'Shelter 3' => ['Tersedia', 'Boleh', 8, 800, 60, 1700],
        'Batu Gantung' => ['Tidak tersedia', 'Tidak boleh', 6, 500, 30, 900],
        'Tugu Yudha' => ['Tidak tersedia', 'Tidak boleh', 5, 400, 25, 400],
        'Puncak Kerinci' => ['Tidak tersedia', 'Tidak boleh', 4, 0, 0, 0],
        'Pos 1 Solok' => ['Belum diketahui', 'Tidak boleh', 18, 1800, 60, 5100],
        'Pos 2 Solok' => ['Tersedia', 'Boleh', 12, 1600, 70, 3300],
        'Pos 3 Solok' => ['Belum diketahui', 'Boleh', 9, 1700, 90, 1700],
    ];

    public function run(): void
    {
        DB::transaction(fn () => $this->seedPosts());
    }

    private function seedPosts(): void
    {
        // Resolve by name: gate IDs can differ between databases.
        $kersikGate = gk_gates::whereIn('nama', ['Desa Kersik Tuo', 'Kersik Tuo', 'Desa Kresik Tuo', 'Kresik Tuo'])->firstOrFail();
        $solokGate = gk_gates::where('nama', 'Solok Selatan')->firstOrFail();

        // ESTIMATES for development, read approximately from the 2020.1 trail map.
        // https://www.gunungbagging.com/wp-content/maps/peta-jalur-pendakian-gunung-kerinci.pdf
        // Names, order and elevations follow the author's latest list.
        // Kersik Tuo is an estimated approach/basecamp point; Batu Gantung is
        // interpolated between Shelter 3 and Tugu Yudha. No field survey yet.
        $kersikTuoPosts = [
            ['nama' => 'Kersik Tuo', 'urutan' => 1, 'latitude' => -1.7650, 'longitude' => 101.2650, 'altitude' => 1605, 'radius_meter' => 150],
            ['nama' => 'Pintu Rimba', 'urutan' => 2, 'latitude' => -1.7483, 'longitude' => 101.2594, 'altitude' => 1809, 'radius_meter' => 150],
            ['nama' => 'Bangku Panjang', 'urutan' => 3, 'latitude' => -1.7407, 'longitude' => 101.2601, 'altitude' => 1890, 'radius_meter' => 150],
            ['nama' => 'Batu Lumut', 'urutan' => 4, 'latitude' => -1.7348, 'longitude' => 101.2603, 'altitude' => 2010, 'radius_meter' => 150],
            ['nama' => 'Pondok Panorama', 'urutan' => 5, 'latitude' => -1.7290, 'longitude' => 101.2622, 'altitude' => 2367, 'radius_meter' => 150],
            ['nama' => 'Shelter 1', 'urutan' => 6, 'latitude' => -1.7204, 'longitude' => 101.2636, 'altitude' => 2505, 'radius_meter' => 150],
            ['nama' => 'Shelter 2', 'urutan' => 7, 'latitude' => -1.7096, 'longitude' => 101.2675, 'altitude' => 3056, 'radius_meter' => 150],
            ['nama' => 'Shelter 3', 'urutan' => 8, 'latitude' => -1.7056, 'longitude' => 101.2673, 'altitude' => 3290, 'radius_meter' => 150],
            ['nama' => 'Batu Gantung', 'urutan' => 9, 'latitude' => -1.7029, 'longitude' => 101.2652, 'altitude' => 3528, 'radius_meter' => 150],
            ['nama' => 'Tugu Yudha', 'urutan' => 10, 'latitude' => -1.6992, 'longitude' => 101.2642, 'altitude' => 3673, 'radius_meter' => 150],
            ['nama' => 'Puncak Kerinci', 'urutan' => 11, 'latitude' => -1.6974, 'longitude' => 101.2661, 'altitude' => 3805, 'radius_meter' => 200],
        ];

        $aliases = [
            'Bangku Panjang' => ['Pos 1', 'Bangku Panjang (Pos 1)'],
            'Batu Lumut' => ['Pos 2', 'Batu Lumut (Pos 2)'],
            'Pondok Panorama' => ['Pos 3 (Shelter 2)', 'Pondok Panorama (Pos 3)'],
            'Puncak Kerinci' => ['Puncak Gunung Kerinci'],
        ];
        $legacyCoordinates = [
            'Kersik Tuo' => [-1.7515, 101.2595],
            'Shelter 1' => [-1.7080, 101.2680],
            'Bangku Panjang' => [-1.7040, 101.2660],
            'Batu Lumut' => [-1.7000, 101.2645],
            'Pondok Panorama' => [-1.6970, 101.2635],
            'Puncak Kerinci' => [-1.6974, 101.2642],
        ];
        $estimateNote = 'Koordinat perkiraan untuk pengembangan; acuan peta Gunung Bagging 2020.1 dan interpolasi. Ketinggian mengikuti daftar penulis. Belum diverifikasi dengan GPS lapangan.';

        foreach ($kersikTuoPosts as $post) {
            $matches = GkPost::where('id_gate', $kersikGate->id)
                ->whereIn('nama', array_merge([$post['nama']], $aliases[$post['nama']] ?? []))->get();
            if ($matches->count() > 1) {
                throw new \RuntimeException("Nama pos lama dan baru bertumpuk untuk {$post['nama']}; periksa duplikat tanpa menghapus riwayat check-in.");
            }

            $created = $matches->first();
            if (!$created) {
                $created = GkPost::create(array_merge($post, [
                    'id_gate' => $kersikGate->id, 'qr_code_value' => null,
                    'status' => true, 'detail' => $estimateNote,
                ]));
            } else {
                $legacy = $legacyCoordinates[$post['nama']] ?? null;
                $isLegacyCoordinate = $legacy
                    && abs((float) $created->latitude - $legacy[0]) < 0.00000001
                    && abs((float) $created->longitude - $legacy[1]) < 0.00000001;
                // Update old sample positions, but preserve coordinates edited by admins.
                $changes = ['nama' => $post['nama'], 'urutan' => $post['urutan'], 'altitude' => $post['altitude']];
                if ($isLegacyCoordinate) {
                    $changes += array_intersect_key($post, array_flip(['latitude', 'longitude', 'altitude']));
                    $changes['detail'] = trim(($created->detail ?? '') . "\n" . $estimateNote);
                }
                $created->update($changes);
            }
            if (!$created->qr_code_value || $created->qr_code_value === 'PLACEHOLDER') {
                $created->update(['qr_code_value' => 'TNKS_CHECKPOINT:' . $created->id]);
            }
            $this->seedDemoInfo($created);
        }

        // Retain old IDs and attendance history; omit this point from the active route.
        GkPost::where('id_gate', $kersikGate->id)->where('nama', 'Bayangan Shelter 2')->update(['status' => false]);

        // Gate 2: Solok Selatan route
        $solokPosts = [
            ['nama' => 'Pos 1 Solok', 'urutan' => 1, 'latitude' => -1.7100, 'longitude' => 101.2550, 'altitude' => 2200, 'radius_meter' => 150],
            ['nama' => 'Pos 2 Solok', 'urutan' => 2, 'latitude' => -1.7050, 'longitude' => 101.2580, 'altitude' => 2700, 'radius_meter' => 150],
            ['nama' => 'Pos 3 Solok', 'urutan' => 3, 'latitude' => -1.7000, 'longitude' => 101.2610, 'altitude' => 3100, 'radius_meter' => 150],
            ['nama' => 'Puncak Kerinci', 'urutan' => 4, 'latitude' => -1.6974, 'longitude' => 101.2642, 'altitude' => 3805, 'radius_meter' => 200],
        ];

        foreach ($solokPosts as $post) {
            $created = GkPost::firstOrCreate(
                ['nama' => $post['nama'], 'id_gate' => $solokGate->id],
                array_merge($post, [
                    'id_gate' => $solokGate->id,
                    // Opsi D: nilai QR deterministik = TNKS_CHECKPOINT:<id pos> (cocok dengan parser mobile)
                    'qr_code_value' => 'PLACEHOLDER',
                    'status' => true,
                ])
            );
            if ($created->qr_code_value === 'PLACEHOLDER') {
                $created->update(['qr_code_value' => 'TNKS_CHECKPOINT:' . $created->id]);
            }
            $this->seedDemoInfo($created);
        }

        $this->command?->info('Trail posts seeded: 11 (Kersik Tuo) + 4 (Solok Selatan). Koordinat Kersik Tuo masih perkiraan.');
    }

    private function seedDemoInfo(GkPost $post): void
    {
        // Jangan menimpa informasi non-dummy yang telah diisi untuk pos ini.
        $hasInfo = $post->mountain_status !== null
            || ($post->water_source_status ?? 'Belum diketahui') !== 'Belum diketahui'
            || ($post->camping_status ?? 'Belum diketahui') !== 'Belum diketahui'
            || $post->temperature_c !== null || $post->distance_to_next_m !== null
            || $post->estimated_time_minutes !== null || $post->distance_to_summit_m !== null
            || $post->info_note !== null || $post->info_updated_at !== null;
        if (!$post->info_is_dummy && $hasInfo) {
            return;
        }

        [$water, $camping, $temperature, $distance, $minutes, $summit] = self::DEMO_INFO[$post->nama];
        $info = [
            'mountain_status' => 'Normal',
            'water_source_status' => $water,
            'camping_status' => $camping,
            'temperature_c' => $temperature,
            'distance_to_next_m' => $distance,
            'estimated_time_minutes' => $minutes,
            'distance_to_summit_m' => $summit,
            'info_is_dummy' => true,
            'info_note' => 'DATA DEMO: contoh untuk pengembangan, bukan kondisi lapangan, prakiraan cuaca, atau izin berkemah yang sebenarnya.',
        ];
        $post->fill($info);
        if ($post->isDirty(array_keys($info))) {
            $post->info_updated_at = now();
            $post->save();
        }
    }
}
