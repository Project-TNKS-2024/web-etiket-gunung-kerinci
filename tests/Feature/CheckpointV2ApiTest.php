<?php

namespace Tests\Feature;

use App\Models\GkCheckpointLog;
use App\Models\GkPost;
use App\Models\gk_booking;
use App\Models\gk_pendaki;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Tests\TestCase;

class CheckpointV2ApiTest extends TestCase
{
    use RefreshDatabase;

    private User $leader;
    private User $member;
    private gk_booking $booking;
    private gk_pendaki $leaderHiker;
    private gk_pendaki $memberHiker;
    private GkPost $post1;
    private GkPost $post2;
    private GkPost $summit;

    protected function setUp(): void
    {
        parent::setUp();

        $now = now();
        $bioIds = [];
        foreach (['Leader', 'Member'] as $name) {
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

        $this->post1 = GkPost::create([
            'nama' => 'Pos 1', 'urutan' => 1, 'latitude' => -1.70, 'longitude' => 101.26,
            'id_gate' => $gateId, 'qr_code_value' => 'V2-POST-1', 'status' => true,
            'estimated_time_minutes' => 30, 'distance_to_next_m' => 1000,
        ]);
        $this->post2 = GkPost::create([
            'nama' => 'Pos 2', 'urutan' => 2, 'latitude' => -1.71, 'longitude' => 101.27,
            'id_gate' => $gateId, 'qr_code_value' => 'V2-POST-2', 'status' => true,
            'estimated_time_minutes' => 30, 'distance_to_next_m' => 1000,
            'estimated_descent_time_minutes' => 40,
        ]);
        $this->summit = GkPost::create([
            'nama' => 'Puncak', 'urutan' => 3, 'latitude' => -1.72, 'longitude' => 101.28,
            'id_gate' => $gateId, 'qr_code_value' => 'V2-SUMMIT', 'status' => true,
            'is_summit' => true, 'estimated_descent_time_minutes' => 50,
        ]);
    }

    private function scan(
        string $phase,
        GkPost $post,
        ?string $pendakiId = null,
        ?string $clientEventId = null,
        string $recordedAt = '2026-10-16T08:00:00+07:00'
    ) {
        $payload = [
            'qr_code_value' => $post->qr_code_value,
            'phase' => $phase,
            'client_event_id' => $clientEventId ?? (string) Str::uuid(),
            'recorded_at' => $recordedAt,
        ];
        if ($pendakiId !== null) {
            $payload['pendaki_id'] = $pendakiId;
        }

        return $this->postJson('/api/tracking/v2/checkpoint/qr', $payload);
    }

    public function test_first_naik_checkin_succeeds_and_duplicate_is_rejected(): void
    {
        $this->actingAs($this->leader, 'sanctum');
        $this->scan('naik', $this->post1)->assertCreated()
            ->assertJsonPath('data.phase', 'naik')
            ->assertJsonPath('data.tracking_phase', 'naik')
            ->assertJsonPath('data.visit_key', 'naik:' . $this->post1->id);

        $this->scan('naik', $this->post1)->assertStatus(409)
            ->assertJsonPath('errors.code', 'CHECKPOINT_ALREADY_RECORDED');

        $this->assertSame(1, GkCheckpointLog::where('id_pendaki', $this->leaderHiker->id)->count());

        // Explicit identity must bind attendance to the selected booking, even with another active hike.
        $otherBooking = $this->booking->replicate();
        $otherBooking->id = (string) Str::uuid();
        $otherBooking->save();
        $otherHiker = gk_pendaki::create([
            'booking_id' => $otherBooking->id, 'id_bio' => $this->leader->id_bio, 'usia' => 36,
        ]);
        $this->scan('naik', $this->post2, $this->leaderHiker->id)->assertCreated()
            ->assertJsonPath('data.pendaki_id', $this->leaderHiker->id);
        $this->scan('naik', $this->post1, $otherHiker->id)->assertCreated()
            ->assertJsonPath('data.pendaki_id', $otherHiker->id)
            ->assertJsonPath('data.next_visit_key', 'naik:' . $this->post2->id);
        $this->getJson('/api/tracking/v2/progress/' . $otherBooking->id)->assertOk()
            ->assertJsonPath('data.hikers.0.visits.0.completed', true);
        $this->postJson('/api/tracking/v2/checkpoint/manual', [
            'pendaki_id' => $otherHiker->id, 'post_id' => $this->post2->id,
            'phase' => 'naik', 'client_event_id' => (string) Str::uuid(),
            'recorded_at' => now()->toIso8601String(), 'latitude' => -1.71, 'longitude' => 101.27,
        ])->assertCreated()->assertJsonPath('data.pendaki_id', $otherHiker->id);
        $this->postJson('/api/tracking/checkpoint/gps', [
            'pendaki_id' => $otherHiker->id, 'latitude' => -1.70, 'longitude' => 101.26,
        ])->assertOk()->assertJsonPath('data.nearest_post.id', $this->post1->id);
        $this->postJson('/api/tracking/checkpoint/gps', [
            'pendaki_id' => $this->memberHiker->id, 'latitude' => -1.70, 'longitude' => 101.26,
        ])->assertForbidden();
        $otherLog = GkCheckpointLog::where('id_pendaki', $otherHiker->id)->where('id_post', $this->post1->id)->firstOrFail();
        $this->deleteJson('/api/tracking/v2/checkpoint/' . $otherLog->id)->assertOk();
        $this->assertSame(2, GkCheckpointLog::where('id_pendaki', $this->leaderHiker->id)->count());
    }

    public function test_summit_checkin_atomically_transitions_to_descent(): void
    {
        $this->actingAs($this->leader, 'sanctum');
        $this->scan('naik', $this->post1)->assertCreated();
        $this->scan('naik', $this->summit)->assertCreated()
            ->assertJsonPath('data.tracking_phase', 'turun')
            ->assertJsonPath('data.visit_key', 'naik:' . $this->summit->id)
            ->assertJsonPath('data.next_visit_key', 'turun:' . $this->post2->id);

        $this->assertDatabaseHas('gk_pendakis', [
            'id' => $this->leaderHiker->id, 'tracking_phase' => 'turun',
        ]);
    }

    public function test_descent_requires_summit(): void
    {
        $this->actingAs($this->leader, 'sanctum');
        $this->scan('turun', $this->post2)->assertStatus(409)
            ->assertJsonPath('errors.code', 'DESCENT_REQUIRES_SUMMIT');
        $this->assertSame(0, GkCheckpointLog::count());
    }

    public function test_same_physical_post_accepts_one_naik_and_one_turun(): void
    {
        $this->actingAs($this->leader, 'sanctum');
        $this->scan('naik', $this->post1)->assertCreated();
        $this->scan('naik', $this->post2)->assertCreated();
        $this->scan('naik', $this->summit)->assertCreated();
        $this->scan('turun', $this->post2)->assertCreated()
            ->assertJsonPath('data.visit_key', 'turun:' . $this->post2->id);

        $this->assertSame(4, GkCheckpointLog::where('id_pendaki', $this->leaderHiker->id)->count());
    }

    public function test_summit_replay_after_transition_is_naik_duplicate_not_descent(): void
    {
        $this->actingAs($this->leader, 'sanctum');
        $this->scan('naik', $this->summit)->assertCreated();
        // Replay summit naik -> duplikasi summit naik, bukan kunjungan turun.
        $this->scan('naik', $this->summit)->assertStatus(409)
            ->assertJsonPath('errors.code', 'CHECKPOINT_ALREADY_RECORDED');
        // Summit turun ditolak (tidak ada kunjungan summit kedua).
        $this->scan('turun', $this->summit)->assertStatus(409)
            ->assertJsonPath('errors.code', 'CHECKPOINT_PHASE_CONFLICT');
        $this->assertSame(1, GkCheckpointLog::where('id_pendaki', $this->leaderHiker->id)->count());
    }

    public function test_idempotent_replay_returns_canonical_record(): void
    {
        $this->actingAs($this->leader, 'sanctum');
        $eventId = (string) Str::uuid();
        $first = $this->scan('naik', $this->post1, null, $eventId)->assertCreated();

        $replay = $this->scan('naik', $this->post1, null, $eventId)->assertOk();
        $replay->assertJsonPath('data.idempotent', true)
            ->assertJsonPath('data.checkpoint_log_id', $first->json('data.checkpoint_log_id'));
        $this->assertSame(1, GkCheckpointLog::where('id_pendaki', $this->leaderHiker->id)->count());
    }

    public function test_client_event_id_reused_with_different_identity_is_conflict(): void
    {
        $this->actingAs($this->leader, 'sanctum');
        $eventId = (string) Str::uuid();
        $this->scan('naik', $this->post1, null, $eventId)->assertCreated();

        $this->scan('naik', $this->post2, null, $eventId)->assertStatus(409)
            ->assertJsonPath('errors.code', 'CLIENT_EVENT_CONFLICT');
    }

    public function test_cancel_summit_before_descent_restores_naik(): void
    {
        $this->actingAs($this->leader, 'sanctum');
        $logId = $this->scan('naik', $this->summit)->assertCreated()->json('data.checkpoint_log_id');

        $this->deleteJson('/api/tracking/v2/checkpoint/' . $logId)->assertOk()
            ->assertJsonPath('data.tracking_phase', 'naik');
        $this->assertDatabaseHas('gk_pendakis', ['id' => $this->leaderHiker->id, 'tracking_phase' => 'naik']);
        $this->assertSame(0, GkCheckpointLog::count());
    }

    public function test_cancel_summit_after_descent_is_rejected(): void
    {
        $this->actingAs($this->leader, 'sanctum');
        $summitLog = $this->scan('naik', $this->summit)->assertCreated()->json('data.checkpoint_log_id');
        $this->scan('turun', $this->post2)->assertCreated();

        $this->deleteJson('/api/tracking/v2/checkpoint/' . $summitLog)->assertStatus(409)
            ->assertJsonPath('errors.code', 'SUMMIT_HAS_DESCENT_CHECKINS');
        $this->assertSame(2, GkCheckpointLog::count());
    }

    public function test_cancel_descent_does_not_delete_naik_of_same_post(): void
    {
        $this->actingAs($this->leader, 'sanctum');
        $this->scan('naik', $this->post2)->assertCreated();
        $this->scan('naik', $this->summit)->assertCreated();
        $descentLog = $this->scan('turun', $this->post2)->assertCreated()->json('data.checkpoint_log_id');

        $this->deleteJson('/api/tracking/v2/checkpoint/' . $descentLog)->assertOk();
        $this->assertSame(2, GkCheckpointLog::count());
        $this->assertDatabaseHas('gk_checkpoint_logs', ['id_post' => $this->post2->id, 'phase' => 'naik']);
        $this->assertDatabaseMissing('gk_checkpoint_logs', ['id_post' => $this->post2->id, 'phase' => 'turun']);
    }

    public function test_summit_phase_transition_is_per_hiker(): void
    {
        $this->actingAs($this->leader, 'sanctum');
        $this->scan('naik', $this->summit)->assertCreated();

        $this->assertDatabaseHas('gk_pendakis', ['id' => $this->leaderHiker->id, 'tracking_phase' => 'turun']);
        $this->assertDatabaseHas('gk_pendakis', ['id' => $this->memberHiker->id, 'tracking_phase' => 'naik']);
    }

    public function test_leader_naik_proof_does_not_allow_marking_member_turun(): void
    {
        $this->actingAs($this->leader, 'sanctum');
        $this->scan('naik', $this->post1)->assertCreated();

        // Ketua sudah check-in naik di Pos 1, tapi belum turun -> tidak boleh menandai turun anggota.
        $this->scan('turun', $this->post1, $this->memberHiker->id)->assertForbidden();
    }

    public function test_late_ascent_after_summit_keeps_phase_turun(): void
    {
        $this->actingAs($this->leader, 'sanctum');
        $this->scan('naik', $this->summit, null, null, '2026-10-16T06:00:00+07:00')->assertCreated();
        // Naik pos biasa yang datang setelah summit dengan waktu sebelum summit -> diterima, fase tetap turun.
        $this->scan('naik', $this->post1, null, null, '2026-10-16T05:00:00+07:00')->assertCreated()
            ->assertJsonPath('data.tracking_phase', 'turun');
        $this->assertDatabaseHas('gk_pendakis', ['id' => $this->leaderHiker->id, 'tracking_phase' => 'turun']);
    }

    public function test_late_ascent_with_conflicting_time_is_rejected(): void
    {
        $this->actingAs($this->leader, 'sanctum');
        $this->scan('naik', $this->summit, null, null, '2026-10-16T06:00:00+07:00')->assertCreated();

        $this->scan('naik', $this->post1, null, null, '2026-10-16T07:00:00+07:00')->assertStatus(409)
            ->assertJsonPath('errors.code', 'CHECKPOINT_PHASE_CONFLICT');
    }

    public function test_future_recorded_at_is_rejected(): void
    {
        $this->actingAs($this->leader, 'sanctum');
        $future = now()->addHour()->toISOString();
        $this->scan('naik', $this->post1, null, null, $future)->assertStatus(409)
            ->assertJsonPath('errors.code', 'CHECKPOINT_PHASE_CONFLICT');
        $this->assertSame(0, GkCheckpointLog::count());
    }

    public function test_progress_v2_returns_itinerary_and_phase(): void
    {
        $this->actingAs($this->leader, 'sanctum');
        $this->scan('naik', $this->post1)->assertCreated();
        $this->scan('naik', $this->post2)->assertCreated();
        $this->scan('naik', $this->summit)->assertCreated();
        $this->scan('turun', $this->post2)->assertCreated();

        $response = $this->getJson('/api/tracking/v2/progress/' . $this->booking->id)->assertOk();
        $response->assertJsonPath('data.total_posts', 3)
            ->assertJsonPath('data.total_visits', 5);

        $hikers = collect($response->json('data.hikers'))->keyBy('pendaki_id');
        $leader = $hikers[$this->leaderHiker->id];
        $this->assertSame('turun', $leader['tracking_phase']);
        $this->assertSame(['completed' => 3, 'total' => 3, 'percentage' => 100], $leader['ascent_progress']);
        $this->assertSame(['completed' => 1, 'total' => 2, 'percentage' => 50], $leader['descent_progress']);
        $this->assertSame(5, count($leader['visits']));
        $this->assertSame('turun:' . $this->post1->id, $leader['next_visit_key']);
    }

    public function test_progress_without_summit_configuration_returns_error(): void
    {
        $this->summit->update(['is_summit' => false]);
        $this->actingAs($this->leader, 'sanctum');
        $this->getJson('/api/tracking/v2/progress/' . $this->booking->id)->assertStatus(422)
            ->assertJsonPath('errors.code', 'ROUTE_CONFIGURATION_INVALID');
    }

    public function test_legacy_summit_checkin_sets_descent_and_legacy_cancel_restores_ascent(): void
    {
        $this->actingAs($this->leader, 'sanctum');
        $response = $this->postJson('/api/tracking/checkpoint/qr', [
            'qr_code_value' => $this->summit->qr_code_value,
        ])->assertCreated();
        $this->assertSame('turun', $this->leaderHiker->fresh()->tracking_phase);
        $this->deleteJson('/api/tracking/checkpoint/' . $response->json('data.checkpoint_log_id'))->assertOk();
        $this->assertSame('naik', $this->leaderHiker->fresh()->tracking_phase);
    }

    public function test_legacy_manual_summit_sets_descent(): void
    {
        $this->actingAs($this->leader, 'sanctum')->postJson('/api/tracking/checkpoint/manual', [
            'post_id' => $this->summit->id, 'latitude' => -1.72, 'longitude' => 101.28,
        ])->assertCreated();
        $this->assertSame('turun', $this->leaderHiker->fresh()->tracking_phase);
    }

    public function test_legacy_cancel_of_v2_summit_restores_ascent_and_blocks_after_descent(): void
    {
        $this->actingAs($this->leader, 'sanctum');
        $logId = $this->scan('naik', $this->summit)->assertCreated()->json('data.checkpoint_log_id');
        $this->deleteJson('/api/tracking/checkpoint/' . $logId)->assertOk();
        $this->assertSame('naik', $this->leaderHiker->fresh()->tracking_phase);
        $logId = $this->scan('naik', $this->summit)->assertCreated()->json('data.checkpoint_log_id');
        $this->scan('turun', $this->post2)->assertCreated();
        $this->deleteJson('/api/tracking/checkpoint/' . $logId)->assertStatus(409);
        $this->assertDatabaseHas('gk_checkpoint_logs', ['id' => $logId]);
    }

    public function test_stale_cancellation_cannot_reset_a_new_summit(): void
    {
        $this->actingAs($this->leader, 'sanctum');
        $logId = $this->scan('naik', $this->summit)->assertCreated()->json('data.checkpoint_log_id');
        $staleLog = GkCheckpointLog::findOrFail($logId);
        $this->deleteJson('/api/tracking/v2/checkpoint/' . $logId)->assertOk();
        $newLogId = $this->scan('naik', $this->summit)->assertCreated()->json('data.checkpoint_log_id');
        try {
            app(\App\Services\CheckpointVisitService::class)->cancel($staleLog, $this->leaderHiker);
            $this->fail('A stale cancellation must be rejected.');
        } catch (\App\Exceptions\CheckpointException $e) {
            $this->assertSame(404, $e->httpStatus);
        }
        $this->assertSame('turun', $this->leaderHiker->fresh()->tracking_phase);
        $this->assertDatabaseHas('gk_checkpoint_logs', ['id' => $newLogId]);
    }

    public function test_descent_timestamp_before_summit_is_rejected_but_clock_skew_is_allowed(): void
    {
        $this->actingAs($this->leader, 'sanctum');
        $this->scan('naik', $this->summit, null, null, '2026-10-16T08:00:00+07:00')->assertCreated();
        $this->scan('turun', $this->post2, null, null, '2026-10-16T07:00:00+07:00')
            ->assertStatus(409)->assertJsonPath('errors.code', 'CHECKPOINT_PHASE_CONFLICT');
        $this->scan('turun', $this->post2, null, null, '2026-10-16T07:58:00+07:00')->assertCreated();
    }

    public function test_progress_loads_hiker_names_in_one_query_for_both_api_versions(): void
    {
        $this->actingAs($this->leader, 'sanctum');
        DB::enableQueryLog();
        try {
            foreach (['/api/tracking/progress/', '/api/tracking/v2/progress/'] as $path) {
                DB::flushQueryLog();
                $this->getJson($path . $this->booking->id)->assertOk();
                $nameQueries = collect(DB::getQueryLog())
                    ->filter(fn ($query) => preg_match('/from ["`]?biodatas["`]?/i', $query['query']) === 1);
                $this->assertCount(1, $nameQueries);
            }
        } finally {
            DB::disableQueryLog();
        }
    }

    public function test_legacy_progress_still_returns_ascent_only(): void
    {
        $this->actingAs($this->leader, 'sanctum');
        $this->scan('naik', $this->post1)->assertCreated();

        $response = $this->getJson('/api/tracking/progress/' . $this->booking->id)->assertOk();
        $hikers = collect($response->json('data.hikers'))->keyBy('pendaki_id');
        // Endpoint lama tetap memakai bentuk "completed/total/percentage" naik.
        $this->assertSame(1, $hikers[$this->leaderHiker->id]['completed']);
        $this->assertSame(3, $hikers[$this->leaderHiker->id]['total']);
    }
}
