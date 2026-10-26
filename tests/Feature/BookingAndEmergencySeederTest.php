<?php

namespace Tests\Feature;

use App\Models\GkEmergencyMessage;
use App\Models\gk_booking;
use App\Models\gk_pendaki;
use App\Models\GkCheckpointLog;
use App\Models\GkOverstayCharge;
use App\Models\GkOverstayPayment;
use App\Models\pembayaran;
use App\Models\statusPendaki;
use Carbon\Carbon;
use Database\Seeders\BookingSeeder;
use Database\Seeders\DatabaseSeeder;
use Database\Seeders\EmergencyMessageSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class BookingAndEmergencySeederTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
    }

    public function test_full_seed_provides_seven_scenarios_without_duplicate_active_hikes_and_can_repeat(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-10-20 12:00:00'));
        $this->seed(DatabaseSeeder::class);

        $bookings = gk_booking::with('pendakis.getStatus')->get();
        $this->assertCount(7, $bookings);
        $this->assertSame(
            ['DEMOAKTIF1', 'DEMOBATAL1', 'DEMOCEKIN1', 'DEMODRAFT1', 'DEMOLEWAT1', 'DEMOSELES1', 'DEMOTELAT1'],
            $bookings->pluck('unique_code')->sort()->values()->all()
        );
        $this->assertSame(
            [2, 5, 6, 8, 8, 9, 61],
            $bookings->map(fn ($booking) => (int) $booking->getStatusBooking()->code)->sort()->values()->all()
        );

        $draft = $bookings->firstWhere('unique_code', 'DEMODRAFT1');
        $this->assertSame(2, (int) $draft->status_booking);
        $this->assertSame('2026-10-27', $draft->tanggal_masuk);
        $this->assertSame('2026-10-29', $draft->tanggal_keluar);
        $this->assertFalse((bool) $draft->status_pembayaran);
        $this->assertSame(0, $draft->pembayaran()->count());

        $cancelled = $bookings->firstWhere('unique_code', 'DEMOBATAL1');
        $this->assertSame(9, (int) $cancelled->status_booking);
        $this->assertNotNull($cancelled->cancelled_at);
        $this->assertSame((int) $cancelled->id_user, (int) $cancelled->cancelled_by);
        $this->assertNotNull($cancelled->cancellation_reason);

        $finished = $bookings->firstWhere('unique_code', 'DEMOSELES1');
        $this->assertSame('2026-10-12', $finished->tanggal_masuk);
        $this->assertSame('2026-10-14', $finished->tanggal_keluar);
        foreach ($finished->pendakis as $member) {
            $this->assertSame('2026-10-12 08:00', $member->checkin_at->format('Y-m-d H:i'));
            $this->assertSame('2026-10-14 16:00', $member->actual_checkout_at->format('Y-m-d H:i'));
            $this->assertSame(0, (int) $member->overstayCharge?->total);
        }

        $late = $bookings->firstWhere('unique_code', 'DEMOTELAT1');
        $lateMember = $late->pendakis->first(
            fn ($member) => $member->actual_checkout_at->format('Y-m-d') > $late->tanggal_keluar
        );
        $this->assertNotNull($lateMember);
        $this->assertSame('2026-10-15 10:00', $lateMember->actual_checkout_at->format('Y-m-d H:i'));
        $charge = GkOverstayCharge::where('id_pendaki', $lateMember->id)->firstOrFail();
        $this->assertSame('final', $charge->status);
        $this->assertTrue(GkOverstayPayment::where('id_overstay_charge', $charge->id)->exists());
        $this->assertSame(8, (int) $late->status_booking);

        $active = $bookings->firstWhere('unique_code', 'DEMOAKTIF1');
        $this->assertSame('2026-10-22', $active->tanggal_masuk);
        $this->assertSame('2026-10-24', $active->tanggal_keluar);
        $this->assertTrue((bool) $active->status_pembayaran);
        $this->assertTrue($active->pembayaran()->exists());
        foreach ($active->pendakis as $member) {
            $this->assertNull($member->checkin_at);
        }

        $checkedIn = $bookings->firstWhere('unique_code', 'DEMOCEKIN1');
        $this->assertSame('2026-10-19', $checkedIn->tanggal_masuk);
        $this->assertSame('2026-10-21', $checkedIn->tanggal_keluar);
        $this->assertSame(3, GkCheckpointLog::where('id_booking', $checkedIn->id)->count());

        $overdue = $bookings->firstWhere('unique_code', 'DEMOLEWAT1');
        $this->assertSame('2026-10-16', $overdue->tanggal_masuk);
        $this->assertSame('2026-10-18', $overdue->tanggal_keluar);
        $this->assertSame($overdue->pendakis->count(), GkCheckpointLog::where('id_booking', $overdue->id)->count());

        $activeBios = gk_pendaki::whereHas('booking', fn ($query) => $query->where('status_booking', 6))
            ->pluck('id_bio');
        $this->assertSame($activeBios->count(), $activeBios->unique()->count());
        $this->assertEmpty(
            $checkedIn->pendakis->pluck('id_bio')->intersect($overdue->pendakis->pluck('id_bio'))
        );

        foreach ($bookings as $booking) {
            $this->assertNotNull($booking->gktiket);
            $this->assertSame((int) $booking->gktiket->id_destinasi, (int) $booking->gateMasuk->id_destinasi);
            $this->assertGreaterThanOrEqual(1, $booking->pendakis->count());
            $this->assertSame(
                (int) $booking->total_pendaki_wni + (int) $booking->total_pendaki_wna,
                $booking->pendakis->count()
            );
            $this->assertEquals($booking->pendakis->sum('tagihan'), $booking->total_pembayaran);
            foreach ($booking->pendakis as $member) {
                $this->assertNotNull($member->biodata);
                if ($member->actual_checkout_at) {
                    $this->assertTrue($member->actual_checkout_at->greaterThanOrEqualTo($member->checkin_at));
                }
            }
        }

        $snapshot = $bookings->mapWithKeys(fn ($booking) => [$booking->id => $booking->getAttributes()])->all();
        $counts = [gk_pendaki::count(), statusPendaki::count(), pembayaran::count(), GkCheckpointLog::count()];
        $postSnapshot = \App\Models\GkPost::orderBy('id')->get(['id', 'id_gate', 'nama', 'urutan', 'qr_code_value', 'status'])->toArray();
        $gateSnapshot = \App\Models\gk_gates::orderBy('id')->get(['id', 'nama', 'id_destinasi'])->toArray();
        $checkpointSnapshot = GkCheckpointLog::orderBy('id')->get()->toArray();

        $this->seed(DatabaseSeeder::class);
        $this->assertSame(7, gk_booking::count());
        $this->assertSame(2, GkEmergencyMessage::count());
        $this->assertSame($counts, [gk_pendaki::count(), statusPendaki::count(), pembayaran::count(), GkCheckpointLog::count()]);
        $this->assertSame($postSnapshot, \App\Models\GkPost::orderBy('id')->get(['id', 'id_gate', 'nama', 'urutan', 'qr_code_value', 'status'])->toArray());
        $this->assertSame($gateSnapshot, \App\Models\gk_gates::orderBy('id')->get(['id', 'nama', 'id_destinasi'])->toArray());
        $this->assertSame($checkpointSnapshot, GkCheckpointLog::orderBy('id')->get()->toArray());
        foreach ($snapshot as $id => $attributes) {
            $this->assertSame($attributes, gk_booking::findOrFail($id)->getAttributes());
        }

        // Seeding ulang setelah tanggal skenario lewat tidak menggeser jadwal.
        $before = $checkedIn->getAttributes();
        $members = $checkedIn->pendakis()->pluck('id_bio')->all();
        Carbon::setTestNow(Carbon::parse('2026-10-25 12:00:00'));

        $this->seed(DatabaseSeeder::class);

        $this->assertSame($before, $checkedIn->fresh()->getAttributes());
        $this->assertSame(61, (int) $checkedIn->fresh()->getStatusBooking()->code);
        $this->assertSame(2, gk_booking::where('status_booking', 6)->count());
        $this->assertSame(7, gk_booking::count());
        foreach ($members as $bioId) {
            $this->assertSame(1, gk_pendaki::where('id_bio', $bioId)
                ->whereHas('booking', fn ($query) => $query->where('status_booking', 6))->count());
        }
    }

    public function test_existing_alerts_and_processed_tickets_are_preserved_without_broadcasting(): void
    {
        $this->seed(DatabaseSeeder::class);
        $alert = GkEmergencyMessage::firstOrFail();
        $alert->update(['status' => 'resolved', 'resolved_at' => now(), 'description' => 'Diubah petugas.']);
        $snapshot = $alert->fresh()->getAttributes();
        $ticket = gk_booking::where('unique_code', 'DEMOBATAL1')->firstOrFail();
        $ticket->update(['cancellation_reason' => 'Alasan pengguna.']);
        $ticketSnapshot = $ticket->fresh()->getAttributes();
        Event::fake([\App\Events\EmergencyBroadcast::class]);

        $this->seed([EmergencyMessageSeeder::class, BookingSeeder::class]);
        $this->assertSame($snapshot, $alert->fresh()->getAttributes());
        $this->assertSame($ticketSnapshot, $ticket->fresh()->getAttributes());
        $this->assertSame(2, GkEmergencyMessage::count());
        Event::assertNotDispatched(\App\Events\EmergencyBroadcast::class);

        $active = gk_booking::where('unique_code', 'DEMOCEKIN1')->firstOrFail();
        $active->update(['keterangan' => 'Booking operasional pengguna']);
        $before = $active->fresh()->getAttributes();
        $counts = [gk_booking::count(), gk_pendaki::count(), statusPendaki::count(), GkCheckpointLog::count()];
        $this->seed([BookingSeeder::class]);
        $this->assertSame(7, gk_booking::count());
        $this->assertSame($before, $active->fresh()->getAttributes());
        $this->assertSame($counts, [gk_booking::count(), gk_pendaki::count(), statusPendaki::count(), GkCheckpointLog::count()]);
    }

    public function test_non_demo_alert_prevents_adding_any_demo_alerts(): void
    {
        $this->seed(DatabaseSeeder::class);
        GkEmergencyMessage::query()->delete();
        $admin = \App\Models\User::where('email', 'superadmin@tnks.com')->firstOrFail();
        $destination = \App\Models\destinasi::where('nama', 'Gunung Kerinci')->firstOrFail();
        $real = GkEmergencyMessage::create([
            'id_user' => $admin->id, 'id_destinasi' => $destination->id,
            'type' => 'admin_broadcast', 'title' => 'Pesan petugas',
            'description' => 'Data operasional yang sudah tersedia.',
            'severity' => 'critical', 'status' => 'active',
        ]);
        $snapshot = $real->fresh()->getAttributes();
        $this->seed(EmergencyMessageSeeder::class);
        $this->assertSame(1, GkEmergencyMessage::count());
        $this->assertSame($snapshot, $real->fresh()->getAttributes());
    }
}
