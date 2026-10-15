<?php

namespace Tests\Feature;

use App\Models\GkCheckpointLog;
use App\Models\GkPost;
use App\Models\User;
use App\Models\gk_booking;
use App\Models\gk_pendaki;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Tests\TestCase;

class AdminSafetyTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private int $destinationId;
    private int $otherDestinationId;
    private \App\Models\GkSos $sos;
    private User $leader;
    private User $member;
    private User $outsider;
    private gk_booking $booking;
    private gk_pendaki $leaderHiker;
    private gk_pendaki $memberHiker;
    private GkPost $post;

    protected function setUp(): void
    {
        parent::setUp();

        $now = now();
        $bioIds = [];
        foreach (['Leader', 'Member', 'Outsider'] as $name) {
            $id = (string) Str::uuid();
            $bioIds[$name] = $id;
            DB::table('biodatas')->insert([
                'id' => $id, 'nik' => $id, 'kenegaraan' => 'ID', 'first_name' => $name,
                'lampiran_identitas' => 'test.jpg', 'no_hp' => '081234567890',
                'jenis_kelamin' => 'l', 'tanggal_lahir' => '1990-01-01',
                'created_at' => $now, 'updated_at' => $now,
            ]);
        }

        $this->leader = User::create(['email' => 'leader@example.test', 'password' => 'password', 'id_bio' => $bioIds['Leader']]);
        $this->member = User::create(['email' => 'member@example.test', 'password' => 'password', 'id_bio' => $bioIds['Member']]);
        $this->outsider = User::create(['email' => 'outsider@example.test', 'password' => 'password', 'id_bio' => $bioIds['Outsider']]);

        $destinationId = DB::table('destinasis')->insertGetId([
            'nama' => 'Kerinci', 'kategori' => 'gunung', 'lokasi' => 'Jambi',
            'created_at' => $now, 'updated_at' => $now,
        ]);
        $this->destinationId = $destinationId;
        $this->otherDestinationId = DB::table('destinasis')->insertGetId(['nama' => 'Other', 'kategori' => 'gunung', 'lokasi' => 'Jambi']);
        $this->admin = User::create(['email' => 'admin-safety@example.test', 'password' => 'password', 'role' => 'admin']);
        $this->admin->destinasis()->attach($destinationId);
        \Illuminate\Support\Facades\Event::fake([\App\Events\SOSMessageSent::class, \App\Events\SOSStatusUpdated::class, \App\Events\EmergencyBroadcast::class]);
        $gateId = DB::table('gk_gates')->insertGetId([
            'nama' => 'Pintu masuk', 'id_destinasi' => $destinationId,
            'created_at' => $now, 'updated_at' => $now,
        ]);
        $packageId = DB::table('gk_paket_tikets')->insertGetId([
            'id_destinasi' => $destinationId, 'nama' => 'Pendakian',
            'created_at' => $now, 'updated_at' => $now,
        ]);
        $ticketId = DB::table('gk_tiket_pendakis')->insertGetId([
            'id_paket_tiket' => $packageId, 'kategori_pendaki' => 'wni',
            'harga_masuk_wk' => 0, 'harga_masuk_wd' => 0, 'harga_kemah' => 0,
            'harga_traking' => 0, 'harga_ansuransi' => 0,
            'created_at' => $now, 'updated_at' => $now,
        ]);
        $this->booking = gk_booking::create([
            'id_user' => $this->leader->id, 'id_tiket' => $ticketId,
            'tanggal_masuk' => today(), 'tanggal_keluar' => today()->addDays(2),
            'total_hari' => 3, 'total_pendaki_wni' => 2, 'total_pendaki_wna' => 0,
            'gate_masuk' => $gateId, 'gate_keluar' => $gateId,
            'status_booking' => 6, 'total_pembayaran' => 0,
        ]);
        $this->leaderHiker = gk_pendaki::create(['booking_id' => $this->booking->id, 'id_bio' => $bioIds['Leader'], 'usia' => 36]);
        $this->memberHiker = gk_pendaki::create(['booking_id' => $this->booking->id, 'id_bio' => $bioIds['Member'], 'usia' => 36]);
        $this->post = GkPost::create([
            'nama' => 'Pos 1', 'urutan' => 1, 'latitude' => -1.704,
            'longitude' => 101.266, 'id_gate' => $gateId,
            'qr_code_value' => 'POST-TEST-1', 'status' => true,
        ]);
        $this->sos = \App\Models\GkSos::create([
            'id_pendaki' => $this->leaderHiker->id, 'id_booking' => $this->booking->id,
            'id_destinasi' => $destinationId, 'severity' => 'high', 'message' => 'Help',
            'latitude' => -1.7, 'longitude' => 101.26,
        ]);
    }


    public function test_web_admin_can_send_chat_without_a_sanctum_token(): void
    {
        $this->actingAs($this->admin, 'web')->post('/admin/sos/'.$this->sos->id.'/messages', ['type' => 'text', 'content' => 'Tim sedang menuju lokasi'])
            ->assertRedirect(route('admin.sos.detail', $this->sos->id));
        $this->assertDatabaseHas('gk_sos_chats', ['id_sos' => $this->sos->id, 'sender_type' => 'admin', 'content' => 'Tim sedang menuju lokasi']);
        \Illuminate\Support\Facades\Event::assertDispatched(\App\Events\SOSMessageSent::class);
    }

    public function test_admin_pages_render_and_display_sidebar_and_chat_form(): void
    {
        $this->actingAs($this->admin);
        $this->get('/admin/sos')->assertOk()->assertSee('SOS dan Chat')->assertSee('data-admin-live="sos-list"', false);
        $this->get('/admin/emergency')->assertOk()->assertSee('Peringatan Dini Bahaya');
        $this->get('/admin/sos/'.$this->sos->id)->assertOk()->assertSee(route('admin.sos.messages.send', $this->sos->id), false);
    }

    public function test_scoped_summary_and_refreshed_page_include_new_sos_messages(): void
    {
        $this->actingAs($this->admin)->getJson('/admin/safety/summary')->assertOk()->assertJsonPath('sos_count', 1)->assertJsonPath('hiker_messages', 0);
        \App\Models\GkSosChat::create(['id_sos' => $this->sos->id, 'sender_id' => $this->leader->id, 'sender_type' => 'hiker', 'type' => 'text', 'content' => 'Pesan baru dari pendaki']);
        $this->get('/admin/sos/'.$this->sos->id)->assertOk()->assertSee('Pesan baru dari pendaki');
        $this->getJson('/admin/safety/summary')->assertOk()->assertJsonPath('hiker_messages', 1)->assertHeader('Cache-Control', 'no-store, private');
        $this->sos->update(['id_destinasi' => $this->otherDestinationId]);
        $this->getJson('/admin/safety/summary')->assertOk()->assertJsonPath('sos_count', 0)->assertJsonPath('hiker_messages', 0);
    }

    public function test_admin_cannot_read_send_or_change_sos_outside_assignment(): void
    {
        $this->sos->update(['id_destinasi' => $this->otherDestinationId]);
        $this->actingAs($this->admin)->get('/admin/sos/'.$this->sos->id)->assertNotFound();
        $this->post('/admin/sos/'.$this->sos->id.'/messages', ['type' => 'text', 'content' => 'Forbidden'])->assertNotFound();
        $this->put('/admin/sos/'.$this->sos->id.'/status', ['status' => 'resolved'])->assertNotFound();
        $this->assertDatabaseCount('gk_sos_chats', 0);
        $this->assertSame('active', $this->sos->fresh()->status);
    }

    public function test_emergency_and_disaster_actions_are_scoped_to_admin_destination(): void
    {
        $emergency = \App\Models\GkEmergencyMessage::create(['id_user' => $this->leader->id, 'id_destinasi' => $this->otherDestinationId, 'type' => 'hiker_alert', 'title' => 'Danger', 'description' => 'Test', 'severity' => 'high']);
        $report = \App\Models\GkDisasterReport::create(['id_user' => $this->leader->id, 'id_destinasi' => $this->otherDestinationId, 'potensi_bencana' => 'Longsor', 'deskripsi' => 'Test', 'lokasi' => 'Test']);
        $this->actingAs($this->admin)->put('/admin/emergency/'.$emergency->id.'/acknowledge')->assertNotFound();
        $this->put('/admin/emergency/'.$emergency->id.'/resolve')->assertNotFound();
        $this->put('/admin/disaster-report/'.$report->id.'/verify', ['status' => 'verified'])->assertNotFound();
        $this->assertSame('active', $emergency->fresh()->status);
        $this->assertSame('pending', $report->fresh()->status);
        $emergency->update(['id_destinasi' => $this->destinationId]);
        $report->update(['id_destinasi' => $this->destinationId]);
        $this->put('/admin/emergency/'.$emergency->id.'/acknowledge')->assertRedirect();
        $this->put('/admin/emergency/'.$emergency->id.'/resolve')->assertRedirect();
        $this->put('/admin/disaster-report/'.$report->id.'/verify', ['status' => 'verified'])->assertRedirect();
        $this->assertSame('resolved', $emergency->fresh()->status);
        $this->assertSame('verified', $report->fresh()->status);
    }

    public function test_guest_and_hiker_cannot_use_admin_chat_or_summary(): void
    {
        $this->getJson('/admin/safety/summary')->assertRedirect(route('login'));
        $this->post('/admin/sos/'.$this->sos->id.'/messages', ['type' => 'text', 'content' => 'No login'])->assertRedirect(route('login'));
        $this->actingAs($this->leader)->getJson('/admin/safety/summary')->assertForbidden();
        $this->post('/admin/sos/'.$this->sos->id.'/messages', ['type' => 'text', 'content' => 'Not admin'])->assertForbidden();
        $this->assertDatabaseCount('gk_sos_chats', 0);
    }

    public function test_empty_chat_and_closed_sos_do_not_create_messages(): void
    {
        $this->actingAs($this->admin)->postJson('/admin/sos/'.$this->sos->id.'/messages', ['type' => 'text', 'content' => ''])->assertUnprocessable();
        $this->sos->update(['status' => 'resolved']);
        $this->postJson('/admin/sos/'.$this->sos->id.'/messages', ['type' => 'text', 'content' => 'Closed'])->assertUnprocessable();
        $this->assertDatabaseCount('gk_sos_chats', 0);
    }
}
