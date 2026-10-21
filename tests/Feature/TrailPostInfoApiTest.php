<?php

namespace Tests\Feature;

use App\Models\GkPost;
use App\Models\User;
use Database\Seeders\TrailPostSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class TrailPostInfoApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $destination = DB::table('destinasis')->insertGetId(['nama' => 'Kerinci', 'kategori' => 'gunung', 'lokasi' => 'Jambi']);
        DB::table('gk_gates')->insert([
            ['id' => 10, 'nama' => 'Desa Kersik Tuo', 'id_destinasi' => $destination],
            ['id' => 20, 'nama' => 'Solok Selatan', 'id_destinasi' => $destination],
        ]);
    }

    private function login(): void
    {
        $this->actingAs(User::create(['email' => 'post-info@example.test', 'password' => 'test-only']), 'sanctum');
    }

    public function test_post_information_still_requires_authentication(): void
    {
        $this->getJson('/api/tracking/posts/10')->assertUnauthorized();
    }

    public function test_api_returns_static_demo_information_for_all_ten_route_points(): void
    {
        $this->seed(TrailPostSeeder::class);
        $this->login();
        $response = $this->getJson('/api/tracking/posts/10')->assertOk()
            ->assertJsonPath('success', true)->assertJsonCount(10, 'data')
            ->assertJsonPath('data.0.nama', 'Kersik Tuo')
            ->assertJsonPath('data.0.map_image_url', asset('images/trail-maps/kersik-tuo/kersik-tuo.webp'))
            ->assertJsonPath('data.0.mountain_status', 'Normal')
            ->assertJsonPath('data.0.temperature_c', 20)
            ->assertJsonPath('data.0.distance_to_next_m', 2000)
            ->assertJsonPath('data.0.estimated_time_minutes', 45)
            ->assertJsonPath('data.0.distance_to_summit_m', 9400)
            ->assertJsonPath('data.9.distance_to_next_m', 0)
            ->assertJsonPath('data.9.estimated_time_minutes', 0)
            ->assertJsonPath('data.9.distance_to_summit_m', 0);
        foreach ($response->json('data') as $post) {
            $this->assertTrue($post['info_is_dummy']);
            $this->assertStringContainsString('DATA DEMO', $post['info_note']);
            $this->assertNotNull($post['info_updated_at']);
            $this->assertIsInt($post['distance_to_next_m']);
            $this->assertIsInt($post['estimated_time_minutes']);
        }
        $this->assertSame(4, GkPost::where('id_gate', 20)->where('info_is_dummy', true)->count());
    }

    public function test_both_api_versions_return_the_matching_position_map_in_route_order(): void
    {
        $this->seed(TrailPostSeeder::class);
        $this->login();
        $files = [
            'kersik-tuo', 'pintu-rimba', 'pos-1-bangku-panjang', 'pos-2-batu-lumut',
            'pos-3-pondok-panorama', 'shelter-1', 'shelter-2', 'shelter-3', 'tugu-yudha', 'puncak-kerinci',
        ];
        // Existing PNG paths must also return WebP without reseeding the database.
        foreach (GkPost::where('id_gate', 10)->orderBy('urutan')->get() as $index => $post) {
            $post->update(['map_image_path' => "images/trail-maps/kersik-tuo/{$files[$index]}.png"]);
        }
        foreach (['/api/tracking/posts/10', '/api/tracking/v2/posts/10'] as $endpoint) {
            $response = $this->getJson($endpoint)->assertOk()->assertJsonCount(10, 'data');
            foreach ($files as $index => $file) {
                $response->assertJsonPath("data.$index.map_image_url", asset("images/trail-maps/kersik-tuo/$file.webp"));
            }
        }
        $response = $this->getJson('/api/tracking/posts/20')->assertOk();
        foreach ($response->json('data') as $post) {
            $this->assertNull($post['map_image_url']);
        }
    }

    public function test_posts_without_information_return_unknown_and_null_instead_of_false_facts(): void
    {
        GkPost::create(['nama' => 'Pos belum diisi', 'urutan' => 1, 'latitude' => -1.7, 'longitude' => 101.26, 'id_gate' => 10]);
        $this->login();
        $this->getJson('/api/tracking/posts/10')->assertOk()
            ->assertJsonPath('data.0.info_is_dummy', false)
            ->assertJsonPath('data.0.water_source_status', 'Belum diketahui')
            ->assertJsonPath('data.0.camping_status', 'Belum diketahui')
            ->assertJsonPath('data.0.mountain_status', null)
            ->assertJsonPath('data.0.map_image_url', null)
            ->assertJsonPath('data.0.temperature_c', null)
            ->assertJsonPath('data.0.distance_to_next_m', null)
            ->assertJsonPath('data.0.estimated_time_minutes', null)
            ->assertJsonPath('data.0.distance_to_summit_m', null);
    }
}
