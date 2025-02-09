<?php

namespace Database\Seeders;

use App\Models\gk_checkpoint;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CheckpointSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $checkpoints = [
            [
                'nama' => 'pos 1',
                'deskripsi_naik' => 'anda berada di pos 1',
                'deskripsi_turun' => 'anda nak turun',
                'longitude' => 101.260282,
                'latitude' => -1.740797,
                'urutan' => 1,
            ],
            [
                'nama' => 'pos 2',
                'deskripsi_naik' => 'anda berada di pos 2',
                'deskripsi_turun' => 'anda nak turun',
                'longitude' => 101.260394,
                'latitude' => -1.734911,
                'urutan' => 2,
            ],
            [
                'nama' => 'pos 3',
                'deskripsi_naik' => 'anda berada di pos 3',
                'deskripsi_turun' => 'anda nak turun',
                'longitude' => 101.262280,
                'latitude' => -1.729099,
                'urutan' => 3,
            ],
            [
                'nama' => 'shelter 1',
                'deskripsi_naik' => 'anda berada di shelter 1',
                'deskripsi_turun' => 'anda nak turun',
                'longitude' => 101.263685,
                'latitude' => -1.717429,
                'urutan' => 4,
            ],
            [
                'nama' => 'shelter 2',
                'deskripsi_naik' => 'anda berada di shelter 2',
                'deskripsi_turun' => 'anda nak turun',
                'longitude' => 101.267970,
                'latitude' => -1.709754,
                'urutan' => 5,
            ],
            [
                'nama' => 'shelter 3',
                'deskripsi_naik' => 'anda berada di shelter 3',
                'deskripsi_turun' => 'anda nak turun',
                'longitude' => 101.267357,
                'latitude' => -1.705718,
                'urutan' => 6,
            ],
            [
                'nama' => 'tugu yuda',
                'deskripsi_naik' => 'anda berada di tugu yuda',
                'deskripsi_turun' => 'anda nak turun',
                'longitude' => 101.264275,
                'latitude' => -1.699219,
                'urutan' => 7,
            ],
            [
                'nama' => 'puncak',
                'deskripsi_naik' => 'anda berada di puncak',
                'deskripsi_turun' => 'anda nak turun',
                'longitude' => 101.264467,
                'latitude' => -1.697089,
                'urutan' => 8,
            ],
        ];

        foreach ($checkpoints as $checkpoint) {
            gk_checkpoint::create($checkpoint);
        }
    }
}

