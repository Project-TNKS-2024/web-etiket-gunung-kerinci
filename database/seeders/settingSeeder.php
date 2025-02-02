<?php

namespace Database\Seeders;

use App\Models\setting;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class settingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        setting::create([
            'nama' => 'Bank Penembayaran',
            'text1' => '011701004912301',
            'text2' => 'BRI RPL 013 PS BBTNKS UN',
            'canDelete' => false,
        ]);

        setting::create([
            'nama' => 'Media Sosial : Facebook',
            'text1' => '',
            'text2' => '',
            'canDelete' => false,
        ]);
        setting::create([
            'nama' => 'Media Sosial : Instagram',
            'text1' => 'https://www.instagram.com/bbtn_kerinciseblatofficial/',
            'text2' => '',
            'canDelete' => false,
        ]);
        setting::create([
            'nama' => 'Media Sosial : Youtube',
            'text1' => '',
            'text2' => '',
            'canDelete' => false,
        ]);
        setting::create([
            'nama' => 'Web Utama',
            'text1' => '',
            'text2' => '',
            'canDelete' => false,
        ]);
    }
}
