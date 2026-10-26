<?php

namespace Database\Seeders;

use App\Models\GkPost;
use App\Models\gk_gates;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class TrailPostSeeder extends Seeder
{
    // Nama, mdpl, jarak ke titik berikutnya (m), estimasi naik (menit).
    private const KERSIK_ROUTE = [
        ['Kersik Tuo', null, 2985, 30],
        ['Pintu Rimba', 1809, 787, 20],
        ['Bangku Panjang', 1890, 736, 30],
        ['Batu Lumut', 2010, 1900, 60],
        ['Pondok Panorama', 2367, 538, 90],
        ['Shelter 1', 2505, 959, 180],
        ['Shelter 2', 3056, 534, 70],
        ['Shelter 3', 3290, 584, 40],
        ['Batu Gantung', 3528, 344, 30],
        ['Tugu Yudha', 3673, 344, 30],
        ['Puncak Kerinci', 3805, 0, 0],
    ];

    // Titik lama dipakai saat membuat database baru; koordinat tersimpan dipertahankan.
    private const COORDINATES = [
        'Kersik Tuo' => [-1.7650, 101.2650],
        'Pintu Rimba' => [-1.7483, 101.2594],
        'Bangku Panjang' => [-1.7407, 101.2601],
        'Batu Lumut' => [-1.7348, 101.2603],
        'Pondok Panorama' => [-1.7290, 101.2622],
        'Shelter 1' => [-1.7204, 101.2636],
        'Shelter 2' => [-1.7096, 101.2675],
        'Shelter 3' => [-1.7056, 101.2673],
        'Batu Gantung' => [-1.69666667, 101.26555556],
        'Tugu Yudha' => [-1.6992, 101.2642],
        'Puncak Kerinci' => [-1.6974, 101.2661],
    ];

    public function run(): void
    {
        DB::transaction(function (): void {
            $gate = gk_gates::whereIn('nama', ['Desa Kersik Tuo', 'Kersik Tuo', 'Desa Kresik Tuo', 'Kresik Tuo'])->firstOrFail();
            $aliases = [
                'Bangku Panjang' => ['Pos 1', 'Bangku Panjang (Pos 1)'],
                'Batu Lumut' => ['Pos 2', 'Batu Lumut (Pos 2)'],
                'Pondok Panorama' => ['Pos 3 (Shelter 2)', 'Pondok Panorama (Pos 3)'],
                'Puncak Kerinci' => ['Puncak Gunung Kerinci'],
            ];
            $remaining = array_sum(array_column(self::KERSIK_ROUTE, 2));
            foreach (self::KERSIK_ROUTE as $order => [$name, $altitude, $distance, $minutes]) {
                $matches = GkPost::where('id_gate', $gate->id)
                    ->whereIn('nama', array_merge([$name], $aliases[$name] ?? []))->get();
                if ($matches->count() > 1) {
                    throw new \RuntimeException("Pos bernama {$name} bertumpuk; periksa data tanpa menghapus riwayat.");
                }
                $post = $matches->first() ?? new GkPost();
                [$latitude, $longitude] = self::COORDINATES[$name];
                $post->fill([
                    'nama' => $name, 'id_gate' => $gate->id, 'urutan' => $order,
                    'latitude' => $post->exists ? $post->latitude : $latitude,
                    'longitude' => $post->exists ? $post->longitude : $longitude,
                    'altitude' => $altitude ?? $post->altitude,
                    'radius_meter' => $post->radius_meter ?: ($name === 'Puncak Kerinci' ? 200 : 150),
                    'status' => true, 'is_summit' => $name === 'Puncak Kerinci',
                    'water_source_status' => in_array($name, ['Batu Lumut', 'Shelter 3'], true) ? 'Tersedia' : 'Tidak tersedia',
                    'camping_status' => in_array($name, ['Shelter 1', 'Shelter 2', 'Shelter 3'], true) ? 'Boleh' : 'Tidak boleh',
                    'distance_to_next_m' => $distance, 'estimated_time_minutes' => $minutes,
                    'distance_to_summit_m' => $remaining,
                    'estimated_descent_time_minutes' => null, 'temperature_c' => null,
                    'detail' => "Jalur pendakian melalui {$name}.",
                    'info_is_dummy' => false, 'info_note' => null,
                    'map_image_path' => str_starts_with((string) $post->map_image_path, 'storage/post-images/')
                        ? $post->map_image_path
                        : 'images/trail-maps/kersik-tuo/'.Str::slug($name).'-naik.webp',
                ]);
                if ($post->isDirty()) {
                    $post->info_updated_at = now();
                    $post->save();
                }
                if (!$post->qr_code_value || $post->qr_code_value === 'PLACEHOLDER') {
                    $post->update(['qr_code_value' => 'TNKS_CHECKPOINT:'.$post->id]);
                }
                $remaining -= $distance;
            }
            GkPost::where('id_gate', $gate->id)->where('nama', 'Bayangan Shelter 2')->update(['status' => false]);
            $this->seedSolok();
        });
        $this->command?->info('Pos jalur tersimpan: 11 Kersik Tuo dan 4 Solok Selatan.');
    }

    private function seedSolok(): void
    {
        $gate = gk_gates::where('nama', 'Solok Selatan')->firstOrFail();
        foreach ([
            ['Pos 1 Solok', 1, -1.7100, 101.2550, 2200],
            ['Pos 2 Solok', 2, -1.7050, 101.2580, 2700],
            ['Pos 3 Solok', 3, -1.7000, 101.2610, 3100],
            ['Puncak Kerinci', 4, -1.6974, 101.2642, 3805],
        ] as [$name, $order, $latitude, $longitude, $altitude]) {
            $post = GkPost::firstOrCreate(['nama' => $name, 'id_gate' => $gate->id], [
                'urutan' => $order, 'latitude' => $latitude, 'longitude' => $longitude,
                'altitude' => $altitude, 'radius_meter' => $name === 'Puncak Kerinci' ? 200 : 150,
                'status' => true,
            ]);
            if ($post->info_is_dummy) {
                $post->fill([
                    'temperature_c' => null, 'distance_to_next_m' => null,
                    'estimated_time_minutes' => null, 'estimated_descent_time_minutes' => null,
                    'distance_to_summit_m' => null, 'water_source_status' => 'Belum diketahui',
                    'camping_status' => 'Belum diketahui', 'info_is_dummy' => false, 'info_note' => null,
                ]);
            }
            $post->is_summit = $name === 'Puncak Kerinci';
            if (!$post->qr_code_value || $post->qr_code_value === 'PLACEHOLDER') {
                $post->qr_code_value = 'TNKS_CHECKPOINT:'.$post->id;
            }
            if ($post->isDirty()) {
                $post->info_updated_at = now();
                $post->save();
            }
        }
    }
}
