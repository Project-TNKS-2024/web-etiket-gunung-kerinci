<?php

namespace Tests\Feature;

use App\Models\GkPost;
use Database\Seeders\TrailPostSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class TrailPostSeederTest extends TestCase
{
    use RefreshDatabase;

    private const NAMES = [
        'Kersik Tuo', 'Pintu Rimba', 'Bangku Panjang', 'Batu Lumut',
        'Pondok Panorama', 'Shelter 1', 'Shelter 2', 'Shelter 3',
        'Tugu Yudha', 'Puncak Kerinci',
    ];

    protected function setUp(): void
    {
        parent::setUp();
        $destinasi = DB::table('destinasis')->insertGetId([
            'nama' => 'Kerinci', 'kategori' => 'gunung', 'lokasi' => 'Jambi',
            'created_at' => now(), 'updated_at' => now(),
        ]);
        DB::table('gk_gates')->insert([
            ['id' => 1, 'nama' => 'Gate lain', 'id_destinasi' => $destinasi],
            ['id' => 10, 'nama' => 'Desa Kersik Tuo', 'id_destinasi' => $destinasi],
            ['id' => 20, 'nama' => 'Solok Selatan', 'id_destinasi' => $destinasi],
        ]);
    }

    public function test_seeds_ten_ordered_posts_with_estimated_coordinates_on_the_correct_gate(): void
    {
        $this->seed(TrailPostSeeder::class);
        $posts = GkPost::where('id_gate', 10)->orderBy('urutan')->get();
        $this->assertSame(self::NAMES, $posts->pluck('nama')->all());
        $this->assertSame(range(1, 10), $posts->pluck('urutan')->all());
        $this->assertSame([1605, 1809, 1890, 2010, 2367, 2505, 3056, 3290, 3673, 3805], $posts->pluck('altitude')->all());
        foreach ($posts as $post) {
            $this->assertSame('TNKS_CHECKPOINT:' . $post->id, $post->qr_code_value);
            $this->assertStringContainsString('perkiraan', $post->detail);
            $this->assertTrue($post->status);
            $this->assertFileExists(public_path($post->map_image_path));
            $this->assertStringEndsWith('.webp', $post->map_image_path);
            $this->assertSame(asset($post->map_image_path), $post->map_image_url);
            $this->assertGreaterThan(-1.78, (float) $post->latitude);
            $this->assertLessThan(-1.69, (float) $post->latitude);
            $this->assertGreaterThan(101.25, (float) $post->longitude);
            $this->assertLessThan(101.28, (float) $post->longitude);
        }
        $this->assertSame(0, GkPost::where('id_gate', 1)->count());
        $this->assertSame(4, GkPost::where('id_gate', 20)->count());
    }

    public function test_reseeding_does_not_duplicate_posts_or_change_ids_and_qr_codes(): void
    {
        $this->seed(TrailPostSeeder::class);
        $before = GkPost::orderBy('id')->get()->toArray();
        $this->seed(TrailPostSeeder::class);
        $this->assertSame($before, GkPost::orderBy('id')->get()->toArray());
        $this->assertSame(14, GkPost::count());
    }

    public function test_updates_legacy_names_and_samples_while_preserving_existing_qr_and_admin_coordinates(): void
    {
        $legacy = GkPost::create([
            'nama' => 'Pos 1', 'urutan' => 2, 'latitude' => -1.7040, 'longitude' => 101.2660,
            'altitude' => 2400, 'id_gate' => 10, 'qr_code_value' => 'EXISTING-QR', 'status' => true,
        ]);
        $admin = GkPost::create([
            'nama' => 'Pos 3 (Shelter 2)', 'urutan' => 4, 'latitude' => -1.7288, 'longitude' => 101.2620,
            'altitude' => 2220, 'id_gate' => 10, 'qr_code_value' => 'SURVEYED-QR',
            'status' => false, 'detail' => 'Hasil survei admin',
            'map_image_path' => 'images/trail-maps/custom/panorama-survey.png',
        ]);
        $solok = GkPost::create([
            'nama' => 'Pos 1 Solok', 'urutan' => 1, 'latitude' => -1.71, 'longitude' => 101.255,
            'id_gate' => 20, 'qr_code_value' => 'SOLOK-QR', 'status' => false,
        ]);
        $beforeSolok = $solok->fresh()->toArray();

        $this->seed(TrailPostSeeder::class);
        $legacy->refresh();
        $this->assertSame('Bangku Panjang', $legacy->nama);
        $this->assertSame(3, $legacy->urutan);
        $this->assertSame('EXISTING-QR', $legacy->qr_code_value);
        $this->assertSame('-1.74070000', $legacy->latitude);
        $admin->refresh();
        $this->assertSame('Pondok Panorama', $admin->nama);
        $this->assertSame(5, $admin->urutan);
        $this->assertSame('-1.72880000', $admin->latitude);
        $this->assertSame('101.26200000', $admin->longitude);
        $this->assertSame(2220, $admin->altitude);
        $this->assertSame('images/trail-maps/custom/panorama-survey.png', $admin->map_image_path);
        $this->assertSame(2400, $legacy->altitude);
        $this->assertFalse($admin->status);
        $this->assertSame('SURVEYED-QR', $admin->qr_code_value);
        $this->assertSame('Hasil survei admin', $admin->detail);
        $this->seed(TrailPostSeeder::class);
        $this->assertSame(2220, $admin->fresh()->altitude);
        $this->assertSame('images/trail-maps/custom/panorama-survey.png', $admin->fresh()->map_image_path);
        foreach (['id', 'nama', 'urutan', 'latitude', 'longitude', 'altitude', 'radius_meter', 'qr_code_value', 'status', 'detail'] as $field) {
            $this->assertSame($beforeSolok[$field], $solok->fresh()->toArray()[$field]);
        }
        $this->assertSame(10, GkPost::where('id_gate', 10)->count());
        $this->assertDatabaseMissing('gk_posts', ['id_gate' => 10, 'nama' => 'Pos 1']);
    }

    public function test_ambiguous_legacy_duplicates_roll_back_without_losing_posts(): void
    {
        foreach (['Pos 1', 'Bangku Panjang'] as $i => $name) {
            GkPost::create([
                'nama' => $name, 'urutan' => 3, 'latitude' => -1.74, 'longitude' => 101.26,
                'id_gate' => 10, 'qr_code_value' => 'DUPLICATE-' . $i,
            ]);
        }
        try {
            $this->seed(TrailPostSeeder::class);
            $this->fail('Seeder must report ambiguous names instead of deleting checkpoint records.');
        } catch (\RuntimeException $e) {
            $this->assertStringContainsString('bertumpuk', $e->getMessage());
        }
        $this->assertSame(2, GkPost::count());
        $this->assertDatabaseMissing('gk_posts', ['nama' => 'Kersik Tuo']);
    }

    public function test_retires_batu_gantung_without_deleting_its_id_or_qr(): void
    {
        $old = GkPost::create([
            'nama' => 'Batu Gantung', 'urutan' => 9, 'latitude' => -1.7136, 'longitude' => 101.2652,
            'id_gate' => 10, 'qr_code_value' => 'OLD-BAYANGAN-QR', 'status' => true,
        ]);
        $this->seed(TrailPostSeeder::class);
        $this->assertFalse($old->fresh()->status);
        $this->assertSame('OLD-BAYANGAN-QR', $old->fresh()->qr_code_value);
        $this->assertSame(self::NAMES, GkPost::where('id_gate', 10)->where('status', true)->orderBy('urutan')->pluck('nama')->all());
        $this->assertSame(11, GkPost::where('id_gate', 10)->count());
    }

    public function test_demo_info_does_not_replace_existing_non_dummy_information(): void
    {
        $real = GkPost::create([
            'nama' => 'Shelter 1', 'urutan' => 6, 'latitude' => -1.7204, 'longitude' => 101.2636,
            'id_gate' => 10, 'qr_code_value' => 'REAL-INFO', 'info_is_dummy' => false,
            'water_source_status' => 'Tidak tersedia', 'camping_status' => 'Tidak boleh',
            'info_note' => 'Catatan petugas',
        ]);
        $this->seed(TrailPostSeeder::class);
        $real->refresh();
        $this->assertFalse($real->info_is_dummy);
        $this->assertSame('Tidak tersedia', $real->water_source_status);
        $this->assertSame('Tidak boleh', $real->camping_status);
        $this->assertSame('Catatan petugas', $real->info_note);
        $this->assertNull($real->temperature_c);
        $this->assertNull($real->info_updated_at);
        $this->assertSame(2505, $real->altitude);
    }
}
