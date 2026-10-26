<?php

namespace Tests\Feature;

use App\Models\GkPost;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class PostAdminInfoTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private int $kersik;
    private int $solok;
    private int $outside;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::create(['email' => 'posts-admin@example.test', 'password' => 'test-only', 'role' => 'admin']);
        $destination = DB::table('destinasis')->insertGetId(['nama' => 'Kerinci', 'kategori' => 'gunung', 'lokasi' => 'Jambi']);
        $other = DB::table('destinasis')->insertGetId(['nama' => 'Luar wilayah', 'kategori' => 'gunung', 'lokasi' => 'Lain']);
        $this->admin->destinasis()->attach($destination);
        $this->kersik = DB::table('gk_gates')->insertGetId(['nama' => 'Kersik Tuo', 'id_destinasi' => $destination]);
        $this->solok = DB::table('gk_gates')->insertGetId(['nama' => 'Solok Selatan', 'id_destinasi' => $destination]);
        $this->outside = DB::table('gk_gates')->insertGetId(['nama' => 'Gate lain', 'id_destinasi' => $other]);
        $this->actingAs($this->admin);
    }

    private function payload(int $gate): array
    {
        return ['id_gate' => $gate, 'nama' => 'Shelter uji', 'urutan' => 2, 'latitude' => -1.7, 'longitude' => 101.2,
            'altitude' => 2505, 'distance_to_summit_m' => 3500, 'water_source_status' => 'Tersedia', 'camping_status' => 'Boleh'];
    }

    public function test_admin_saves_information_and_both_api_versions_return_it(): void
    {
        $this->post('/admin/posts', $this->payload($this->kersik))->assertRedirect()->assertSessionHasNoErrors();
        $post = GkPost::where('nama', 'Shelter uji')->firstOrFail();
        $this->assertSame('TNKS_CHECKPOINT:' . $post->id, $post->qr_code_value);
        $this->assertNotNull($post->info_updated_at);
        foreach (['/api/tracking/posts/', '/api/tracking/v2/posts/'] as $endpoint) {
            $this->getJson($endpoint . $this->kersik)->assertOk()
                ->assertJsonPath('data.0.altitude', 2505)
                ->assertJsonPath('data.0.water_source_status', 'Tersedia')
                ->assertJsonPath('data.0.camping_status', 'Boleh')
                ->assertJsonPath('data.0.distance_to_summit_m', 3500);
        }
        $post->update(['info_is_dummy' => true]);
        $this->put('/admin/posts/' . $post->id, array_replace($this->payload($this->kersik), [
            'altitude' => 0, 'distance_to_summit_m' => 0, 'water_source_status' => 'Tidak tersedia', 'camping_status' => 'Tidak boleh',
        ]))->assertRedirect()->assertSessionHasNoErrors();
        $post->refresh();
        $this->assertEquals(0, $post->altitude);
        $this->assertSame(0, $post->distance_to_summit_m);
        $this->assertTrue($post->info_is_dummy);
        $this->get('/admin/posts')->assertOk()->assertSee('0 mdpl')->assertSee('0 m')->assertSee('Data contoh');
        $this->put('/admin/posts/' . $post->id, array_replace($this->payload($this->kersik), [
            'altitude' => '', 'distance_to_summit_m' => '', 'water_source_status' => 'Belum diketahui', 'camping_status' => 'Belum diketahui',
        ]))->assertRedirect()->assertSessionHasNoErrors();
        $this->assertNull($post->fresh()->altitude);
        $this->assertNull($post->fresh()->distance_to_summit_m);
        $this->get('/admin/posts')->assertOk()->assertSee('Belum lengkap');
    }

    public function test_list_filters_route_name_and_preserves_route_order(): void
    {
        $second = GkPost::create($this->payload($this->kersik));
        $first = GkPost::create(array_replace($this->payload($this->kersik), ['nama' => 'Awal Kersik', 'urutan' => 1, 'status' => false]));
        GkPost::create(array_replace($this->payload($this->solok), ['nama' => 'Awal Solok']));
        GkPost::create(array_replace($this->payload($this->outside), ['nama' => 'Pos rahasia']));
        $this->get('/admin/posts?gate_id=' . $this->kersik)->assertOk()
            ->assertViewHas('posts', fn ($posts) => $posts->pluck('id')->all() === [$first->id, $second->id])
            ->assertSee('Pos Jalur')->assertSee('Nonaktif')->assertDontSee('Pos rahasia');
        $this->get('/admin/posts?gate_id=' . $this->solok . '&q=Awal')->assertOk()
            ->assertViewHas('posts', fn ($posts) => $posts->count() === 1 && $posts->first()->nama === 'Awal Solok');
    }

    public function test_invalid_values_preserve_form_input(): void
    {
        $this->from('/admin/posts?gate_id=' . $this->kersik)->post('/admin/posts', array_replace($this->payload($this->kersik), [
            '_form' => 'add', 'altitude' => -1, 'distance_to_summit_m' => -1, 'water_source_status' => 'asal', 'camping_status' => 'asal',
        ]))->assertRedirect('/admin/posts?gate_id=' . $this->kersik)
            ->assertSessionHasErrors(['altitude', 'distance_to_summit_m', 'water_source_status', 'camping_status'])
            ->assertSessionHasInput('nama', 'Shelter uji');
        $this->assertDatabaseCount('gk_posts', 0);
    }

    public function test_admin_cannot_create_move_update_or_delete_outside_managed_destination(): void
    {
        $owned = GkPost::create($this->payload($this->kersik));
        $outside = GkPost::create($this->payload($this->outside));
        $this->post('/admin/posts', $this->payload($this->outside))->assertSessionHasErrors('id_gate');
        $this->put('/admin/posts/' . $owned->id, $this->payload($this->outside))->assertSessionHasErrors('id_gate');
        $this->put('/admin/posts/' . $outside->id, $this->payload($this->kersik))->assertNotFound();
        $this->delete('/admin/posts/' . $outside->id)->assertNotFound();
        $this->assertDatabaseCount('gk_posts', 2);
        $this->getJson('/admin/posts?gate_id=' . $this->outside)->assertUnprocessable();
        $this->admin->update(['role' => 'user']);
        $this->get('/admin/posts')->assertForbidden();
    }
}
