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
        // media sosial
        setting::create([
            'id' => '0000bank',
            'nama' => 'Bank Penembayaran',
            'text1' => '011701004912301',
            'text2' => 'BRI RPL 013 PS BBTNKS UN',
            'canDelete' => false,
        ]);

        setting::create([
            'id' => '0000facebook',
            'nama' => 'Media Sosial : Facebook',
            'text1' => 'https://www.facebook.com/groups/124512191616',
            'text2' => '',
            'canDelete' => false,
        ]);
        setting::create([
            'id' => '0000instagram',
            'nama' => 'Media Sosial : Instagram',
            'text1' => 'https://www.instagram.com/bbtn_kerinciseblatofficial/',
            'text2' => '',
            'canDelete' => false,
        ]);
        setting::create([
            'id' => '0000youtube',
            'nama' => 'Media Sosial : Youtube',
            'text1' => '',
            'text2' => '',
            'canDelete' => false,
        ]);

        // tentang kami
        setting::create([
            'id' => '0000website',
            'nama' => 'Web Utama',
            'text1' => 'https://tnkerinciseblat.com/',
            'text2' => '',
            'canDelete' => false,
        ]);

        setting::create([
            'id' => '0000alamat',
            'nama' => 'Alamat Kantor',
            'text1' => 'Jl. Basuki Rahmat No.11 Kec. Pesisir Bukit, Kota Sungai Penuh, Jambi 37101',
            'text2' => '',
            'canDelete' => false,
        ]);

        setting::create([
            'id' => '0000email',
            'nama' => 'Alamat Email',
            'text1' => 'bbtn.kerinciseblat@gmail.com',
            'text2' => '',
            'canDelete' => false,
        ]);

        setting::create([
            'id' => '0000telp',
            'nama' => 'Nomor Telepon',
            'text1' => '081272223888',
            'text2' => '',
            'canDelete' => false,
        ]);

        setting::create([
            'id' => '0000tutorial',
            'nama' => 'Tutorial',
            'text1' => 'https://youtu.be/dQw4w9WgXcQ',
            'text2' => '',
            'canDelete' => false,
        ]);
    }
}
