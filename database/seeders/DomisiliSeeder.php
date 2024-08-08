<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\d_Provinsi as Provinsi;
use App\Models\d_Kabupaten as Kabupaten;
use App\Models\d_Kecamatan as Kecamatan;
use App\Models\d_Kelurahan as Kelurahan;
use Illuminate\Support\Facades\File;

class DomisiliSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $this->provinsis();
        $this->kabupatens();
        $this->kecamatans();
        $this->kelurahans();
    }

    public function provinsis()
    {
        $json = File::get(public_path('assets/json/provinsi.json'));
        $data = json_decode($json, true);

        foreach ($data as $item) {
            Provinsi::create([
                'id' => $item['id'],
                'name' => $item['name'],
                'code' => $item['code'],
            ]);
        }
    }

    public function kabupatens()
    {
        $json = File::get(public_path('assets/json/kabupaten.json'));
        $data = json_decode($json, true);

        foreach ($data as $item) {
            Kabupaten::create([
                'id' => $item['id'],
                'name' => $item['name'],
                'type' => $item['type'],
                'code' => $item['code'],
                'full_code' => $item['full_code'],
                'provinsi_id' => $item['provinsi_id'],
            ]);
        }
    }

    public function kecamatans()
    {
        $json = File::get(public_path('assets/json/kecamatan.json'));
        $data = json_decode($json, true);

        foreach ($data as $item) {
            Kecamatan::create([
                'id' => $item['id'],
                'name' => $item['name'],
                'code' => $item['code'],
                'full_code' => $item['full_code'],
                'kabupaten_id' => $item['kabupaten_id'],
            ]);
        }
    }

    public function kelurahans()
    {
        $json = File::get(public_path('assets/json/kelurahan.json'));
        $data = json_decode($json, true);

        foreach ($data as $item) {
            Kelurahan::create([
                'id' => $item['id'],
                'name' => $item['name'],
                'code' => $item['code'],
                'full_code' => $item['full_code'],
                'pos_code' => $item['pos_code'],
                'kecamatan_id' => $item['kecamatan_id'],
            ]);
        }
    }
}
