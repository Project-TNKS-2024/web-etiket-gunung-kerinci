<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\gk_booking;
use App\Models\gk_pendaki;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Tests\TestCase;

class MyTiketApiTest extends TestCase
{
    use RefreshDatabase;

    private User $leader;
    private User $member;
    private gk_booking $booking;

    protected function setUp(): void
    {
        parent::setUp();

        $now = now();
        $leaderBio = (string) Str::uuid();
        $memberBio = (string) Str::uuid();

        foreach ([$leaderBio => 'Leader', $memberBio => 'Member'] as $id => $name) {
            DB::table('biodatas')->insert([
                'id' => $id, 'nik' => $id, 'kenegaraan' => 'ID', 'first_name' => $name,
                'lampiran_identitas' => 'test.jpg', 'no_hp' => '081234567890',
                'jenis_kelamin' => 'l', 'tanggal_lahir' => '1990-01-01',
                'created_at' => $now, 'updated_at' => $now,
            ]);
        }

        $this->leader = User::create(['email' => 'leader@test.id', 'password' => 'password', 'id_bio' => $leaderBio]);
        $this->member = User::create(['email' => 'member@test.id', 'password' => 'password', 'id_bio' => $memberBio]);

        $dest = DB::table('destinasis')->insertGetId([
            'nama' => 'Kerinci', 'kategori' => 'gunung', 'lokasi' => 'Jambi',
            'created_at' => $now, 'updated_at' => $now,
        ]);
        $gate = DB::table('gk_gates')->insertGetId([
            'nama' => 'Pintu', 'id_destinasi' => $dest, 'created_at' => $now, 'updated_at' => $now,
        ]);
        $pkg = DB::table('gk_paket_tikets')->insertGetId([
            'id_destinasi' => $dest, 'nama' => 'Pendakian', 'created_at' => $now, 'updated_at' => $now,
        ]);
        $tiket = DB::table('gk_tiket_pendakis')->insertGetId([
            'id_paket_tiket' => $pkg, 'kategori_pendaki' => 'wni', 'harga_masuk_wk' => 0, 'harga_masuk_wd' => 0,
            'harga_kemah' => 0, 'harga_traking' => 0, 'harga_ansuransi' => 0,
            'created_at' => $now, 'updated_at' => $now,
        ]);

        $this->booking = gk_booking::create([
            'id_user' => $this->leader->id, 'id_tiket' => $tiket,
            'tanggal_masuk' => '2026-10-17', 'tanggal_keluar' => '2026-10-19',
            'total_hari' => 3, 'total_pendaki_wni' => 2, 'total_pendaki_wna' => 0,
            'gate_masuk' => $gate, 'gate_keluar' => $gate,
            'status_booking' => 6, 'total_pembayaran' => 0,
        ]);
        gk_pendaki::create(['booking_id' => $this->booking->id, 'id_bio' => $leaderBio, 'usia' => 30]);
        gk_pendaki::create(['booking_id' => $this->booking->id, 'id_bio' => $memberBio, 'usia' => 30]);
    }

    public function test_member_mytiket_does_not_expose_sensitive_biodata(): void
    {
        $this->actingAs($this->member, 'sanctum');

        $response = $this->getJson('/api/mytiket')->assertOk();

        $response->assertJsonPath('data.0.id', $this->booking->id);
        $biodata = $response->json('data.0.pendakis.0.biodata');
        $this->assertIsArray($biodata);
        $this->assertArrayNotHasKey('nik', $biodata);
        $this->assertArrayNotHasKey('no_hp', $biodata);
        $this->assertArrayNotHasKey('no_hp_darurat', $biodata);
    }

    public function test_owner_mytiket_also_hides_sensitive_biodata(): void
    {
        $this->actingAs($this->leader, 'sanctum');

        $response = $this->getJson('/api/mytiket')->assertOk();

        $biodata = $response->json('data.0.pendakis.0.biodata');
        $this->assertArrayNotHasKey('nik', $biodata);
        $this->assertArrayNotHasKey('no_hp', $biodata);
    }
}
