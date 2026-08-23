<?php

namespace Database\Seeders;

use App\Models\GkPost;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class TrailPostSeeder extends Seeder
{
    public function run(): void
    {
        // Gate 1: Desa Kersik Tuo route
        $kersikTuoPosts = [
            ['nama' => 'Shelter 1', 'urutan' => 1, 'latitude' => -1.7080, 'longitude' => 101.2680, 'altitude' => 2100, 'radius_meter' => 150],
            ['nama' => 'Pos 1', 'urutan' => 2, 'latitude' => -1.7040, 'longitude' => 101.2660, 'altitude' => 2400, 'radius_meter' => 150],
            ['nama' => 'Pos 2', 'urutan' => 3, 'latitude' => -1.7000, 'longitude' => 101.2645, 'altitude' => 2800, 'radius_meter' => 150],
            ['nama' => 'Pos 3 (Shelter 2)', 'urutan' => 4, 'latitude' => -1.6970, 'longitude' => 101.2635, 'altitude' => 3200, 'radius_meter' => 150],
            ['nama' => 'Puncak Kerinci', 'urutan' => 5, 'latitude' => -1.6974, 'longitude' => 101.2642, 'altitude' => 3805, 'radius_meter' => 200],
        ];

        foreach ($kersikTuoPosts as $post) {
            GkPost::firstOrCreate(
                ['nama' => $post['nama'], 'id_gate' => 1],
                array_merge($post, [
                    'id_gate' => 1,
                    'qr_code_value' => 'POST-' . strtoupper(Str::random(12)),
                    'status' => true,
                ])
            );
        }

        // Gate 2: Solok Selatan route
        $solokPosts = [
            ['nama' => 'Pos 1 Solok', 'urutan' => 1, 'latitude' => -1.7100, 'longitude' => 101.2550, 'altitude' => 2200, 'radius_meter' => 150],
            ['nama' => 'Pos 2 Solok', 'urutan' => 2, 'latitude' => -1.7050, 'longitude' => 101.2580, 'altitude' => 2700, 'radius_meter' => 150],
            ['nama' => 'Pos 3 Solok', 'urutan' => 3, 'latitude' => -1.7000, 'longitude' => 101.2610, 'altitude' => 3100, 'radius_meter' => 150],
            ['nama' => 'Puncak Kerinci', 'urutan' => 4, 'latitude' => -1.6974, 'longitude' => 101.2642, 'altitude' => 3805, 'radius_meter' => 200],
        ];

        foreach ($solokPosts as $post) {
            GkPost::firstOrCreate(
                ['nama' => $post['nama'], 'id_gate' => 2],
                array_merge($post, [
                    'id_gate' => 2,
                    'qr_code_value' => 'POST-' . strtoupper(Str::random(12)),
                    'status' => true,
                ])
            );
        }

        $this->command->info('Trail posts seeded: 5 (Kersik Tuo) + 4 (Solok Selatan)');
    }
}
