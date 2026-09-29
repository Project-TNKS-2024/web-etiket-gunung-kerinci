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

class CheckpointAttendanceApiTest extends TestCase
{
    use RefreshDatabase;

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
    }

    public function test_leader_can_scan_for_self_and_member_and_progress_tracks_both(): void
    {
        $this->actingAs($this->leader, 'sanctum');

        $this->postJson('/api/tracking/checkpoint/qr', ['qr_code_value' => 'POST-TEST-1'])
            ->assertCreated()->assertJsonPath('data.pendaki_id', $this->leaderHiker->id);

        $this->postJson('/api/tracking/checkpoint/qr', [
            'qr_code_value' => 'POST-TEST-1', 'pendaki_id' => $this->memberHiker->id,
        ])->assertCreated()
            ->assertJsonPath('data.pendaki_id', $this->memberHiker->id)
            ->assertJsonPath('data.progress.completed', 1);

        $this->assertSame(2, GkCheckpointLog::where('id_booking', $this->booking->id)->count());
        $this->getJson('/api/tracking/progress/' . $this->booking->id)
            ->assertOk()->assertJsonPath('data.hikers.0.completed', 1)
            ->assertJsonPath('data.hikers.1.completed', 1);
    }

    public function test_member_can_scan_self_but_cannot_represent_leader_or_other_member(): void
    {
        $thirdHiker = gk_pendaki::create(['booking_id' => $this->booking->id, 'id_bio' => $this->outsider->id_bio, 'usia' => 36]);
        $this->actingAs($this->member, 'sanctum');

        foreach ([$this->leaderHiker->id, $thirdHiker->id] as $id) {
            $this->postJson('/api/tracking/checkpoint/qr', [
                'qr_code_value' => 'POST-TEST-1', 'pendaki_id' => $id,
            ])->assertForbidden()->assertJsonPath('success', false);
        }
        $this->assertSame(0, GkCheckpointLog::count());

        $this->postJson('/api/tracking/checkpoint/qr', ['qr_code_value' => 'POST-TEST-1'])
            ->assertCreated()->assertJsonPath('data.pendaki_id', $this->memberHiker->id);
    }

    public function test_leader_cannot_represent_another_booking_or_nonmember_and_duplicate_is_rejected(): void
    {
        $otherBooking = $this->booking->replicate();
        $otherBooking->id_user = $this->outsider->id;
        $otherBooking->save();
        $otherHiker = gk_pendaki::create(['booking_id' => $otherBooking->id, 'id_bio' => $this->outsider->id_bio, 'usia' => 36]);
        $this->actingAs($this->leader, 'sanctum');

        foreach ([$otherHiker->id, (string) Str::uuid()] as $id) {
            $this->postJson('/api/tracking/checkpoint/qr', [
                'qr_code_value' => 'POST-TEST-1', 'pendaki_id' => $id,
            ])->assertForbidden();
        }
        $this->postJson('/api/tracking/checkpoint/qr', [
            'qr_code_value' => 'POST-TEST-1', 'pendaki_id' => $this->memberHiker->id,
        ])->assertCreated();
        $this->postJson('/api/tracking/checkpoint/qr', [
            'qr_code_value' => 'POST-TEST-1', 'pendaki_id' => $this->memberHiker->id,
        ])->assertStatus(409);
        $this->assertSame(1, GkCheckpointLog::where('id_pendaki', $this->memberHiker->id)->count());
    }

    public function test_member_self_scan_after_leader_represents_them_is_duplicate(): void
    {
        $this->actingAs($this->leader, 'sanctum')->postJson('/api/tracking/checkpoint/qr', [
            'qr_code_value' => 'POST-TEST-1', 'pendaki_id' => $this->memberHiker->id,
        ])->assertCreated();

        $this->actingAs($this->member, 'sanctum')->postJson('/api/tracking/checkpoint/qr', [
            'qr_code_value' => 'POST-TEST-1',
        ])->assertStatus(409);
    }

    public function test_invalid_target_and_missing_active_membership_do_not_record_attendance(): void
    {
        $this->actingAs($this->leader, 'sanctum')->postJson('/api/tracking/checkpoint/qr', [
            'qr_code_value' => 'POST-TEST-1', 'pendaki_id' => 'invalid',
        ])->assertUnprocessable()->assertJsonValidationErrors('pendaki_id');
        $this->actingAs($this->outsider, 'sanctum')->postJson('/api/tracking/checkpoint/qr', [
            'qr_code_value' => 'POST-TEST-1',
        ])->assertForbidden();
        $this->booking->update(['status_booking' => 7]);
        $this->actingAs($this->member, 'sanctum')->postJson('/api/tracking/checkpoint/qr', [
            'qr_code_value' => 'POST-TEST-1',
        ])->assertForbidden();
        $this->assertSame(0, GkCheckpointLog::count());
    }

    public function test_member_can_view_team_progress_but_outsider_cannot(): void
    {
        $this->actingAs($this->member, 'sanctum')
            ->getJson('/api/tracking/progress/' . $this->booking->id)
            ->assertOk()->assertJsonPath('data.booking_id', $this->booking->id);

        $this->actingAs($this->outsider, 'sanctum')
            ->getJson('/api/tracking/progress/' . $this->booking->id)
            ->assertNotFound();
    }

    public function test_leader_can_cancel_member_checkin_and_rescan_afterwards(): void
    {
        $this->actingAs($this->leader, 'sanctum');

        $scan = $this->postJson('/api/tracking/checkpoint/qr', [
            'qr_code_value' => 'POST-TEST-1', 'pendaki_id' => $this->memberHiker->id,
        ])->assertCreated();

        $this->deleteJson('/api/tracking/checkpoint/' . $scan->json('data.checkpoint_log_id'))
            ->assertOk()->assertJsonPath('data.pendaki_id', $this->memberHiker->id)
            ->assertJsonPath('data.progress.completed', 0);
        $this->getJson('/api/tracking/progress/' . $this->booking->id)
            ->assertOk()->assertJsonPath('data.hikers.1.checkpoints.0.completed', false)
            ->assertJsonPath('data.hikers.1.checkpoints.0.checked_at', null);

        $this->postJson('/api/tracking/checkpoint/qr', [
            'qr_code_value' => 'POST-TEST-1', 'pendaki_id' => $this->memberHiker->id,
        ])->assertCreated()->assertJsonPath('data.progress.completed', 1);
        $this->assertSame(1, GkCheckpointLog::where('id_pendaki', $this->memberHiker->id)->count());
    }

    public function test_member_can_cancel_own_checkin_even_if_leader_created_it(): void
    {
        $scan = $this->actingAs($this->leader, 'sanctum')->postJson('/api/tracking/checkpoint/qr', [
            'qr_code_value' => 'POST-TEST-1', 'pendaki_id' => $this->memberHiker->id,
        ])->assertCreated();

        $this->actingAs($this->member, 'sanctum')
            ->deleteJson('/api/tracking/checkpoint/' . $scan->json('data.checkpoint_log_id'))
            ->assertOk()->assertJsonPath('data.progress.completed', 0);
        $this->postJson('/api/tracking/checkpoint/qr', ['qr_code_value' => 'POST-TEST-1'])
            ->assertCreated()->assertJsonPath('data.pendaki_id', $this->memberHiker->id);
    }

    public function test_member_cannot_cancel_leader_or_other_member_checkin(): void
    {
        $leaderLog = $this->actingAs($this->leader, 'sanctum')
            ->postJson('/api/tracking/checkpoint/qr', ['qr_code_value' => 'POST-TEST-1'])
            ->assertCreated()->json('data.checkpoint_log_id');

        $this->actingAs($this->member, 'sanctum')
            ->deleteJson('/api/tracking/checkpoint/' . $leaderLog)->assertForbidden();
        $this->assertDatabaseHas('gk_checkpoint_logs', ['id' => $leaderLog]);
    }

    public function test_outsider_cannot_cancel_foreign_booking_checkin(): void
    {
        $logId = $this->actingAs($this->leader, 'sanctum')
            ->postJson('/api/tracking/checkpoint/qr', [
                'qr_code_value' => 'POST-TEST-1', 'pendaki_id' => $this->memberHiker->id,
            ])->assertCreated()->json('data.checkpoint_log_id');

        $this->actingAs($this->outsider, 'sanctum')
            ->deleteJson('/api/tracking/checkpoint/' . $logId)->assertForbidden();
        $this->assertDatabaseHas('gk_checkpoint_logs', ['id' => $logId]);
    }

    public function test_cancel_rejects_missing_invalid_or_inactive_checkin(): void
    {
        $logId = $this->actingAs($this->leader, 'sanctum')
            ->postJson('/api/tracking/checkpoint/qr', ['qr_code_value' => 'POST-TEST-1'])
            ->assertCreated()->json('data.checkpoint_log_id');

        $this->deleteJson('/api/tracking/checkpoint/not-an-id')->assertNotFound();
        $this->deleteJson('/api/tracking/checkpoint/999999999')->assertNotFound();
        $this->booking->update(['status_booking' => 7]);
        $this->deleteJson('/api/tracking/checkpoint/' . $logId)->assertForbidden();
        $this->assertDatabaseHas('gk_checkpoint_logs', ['id' => $logId]);
    }

    public function test_cancel_requires_auth_and_cannot_cancel_twice(): void
    {
        $this->deleteJson('/api/tracking/checkpoint/1')->assertUnauthorized();

        $logId = $this->actingAs($this->leader, 'sanctum')
            ->postJson('/api/tracking/checkpoint/qr', ['qr_code_value' => 'POST-TEST-1'])
            ->assertCreated()->json('data.checkpoint_log_id');
        $this->deleteJson('/api/tracking/checkpoint/' . $logId)->assertOk();
        $this->deleteJson('/api/tracking/checkpoint/' . $logId)->assertNotFound();
    }

    public function test_leader_cannot_cancel_a_different_booking_even_if_member_of_both(): void
    {
        $otherBooking = $this->booking->replicate();
        $otherBooking->id_user = $this->outsider->id;
        $otherBooking->save();
        $otherHiker = gk_pendaki::create([
            'booking_id' => $otherBooking->id, 'id_bio' => $this->outsider->id_bio, 'usia' => 36,
        ]);
        $otherLog = GkCheckpointLog::create([
            'id_booking' => $otherBooking->id, 'id_pendaki' => $otherHiker->id,
            'id_post' => $this->post->id, 'method' => 'qr', 'checked_at' => now(),
        ]);

        $this->actingAs($this->leader, 'sanctum')
            ->deleteJson('/api/tracking/checkpoint/' . $otherLog->id)->assertNotFound();
        $this->assertDatabaseHas('gk_checkpoint_logs', ['id' => $otherLog->id]);
    }

    public function test_member_can_cancel_own_manual_checkin(): void
    {
        $scan = $this->actingAs($this->member, 'sanctum')
            ->postJson('/api/tracking/checkpoint/manual', [
                'post_id' => $this->post->id, 'latitude' => -1.704, 'longitude' => 101.266,
            ])->assertCreated();

        $this->deleteJson('/api/tracking/checkpoint/' . $scan->json('data.checkpoint_log_id'))
            ->assertOk()->assertJsonPath('data.progress.completed', 0);
        $this->assertSame(0, GkCheckpointLog::count());
    }
}
