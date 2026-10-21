<?php

namespace Tests\Feature;

use App\Models\GkEmergencyMessage;
use App\Models\gk_booking;
use App\Models\gk_pendaki;
use App\Models\pembayaran;
use App\Models\statusPendaki;
use Carbon\Carbon;
use Database\Seeders\BookingStatusSeeder;
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

    public function test_full_seed_covers_demo_statuses_without_duplicate_active_hikes_and_can_repeat(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-10-20 12:00:00'));
        $this->seed(DatabaseSeeder::class);
        $expected = [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 40, 50, 51];
        $bookings = gk_booking::with('pendakis.getStatus')->get();
        $actual = $bookings->map(fn ($booking) => (int) $booking->getStatusBooking()->code)->sort()->values()->all();
        $this->assertSame($expected, $actual);
        $this->assertSame(13, $bookings->pluck('unique_code')->unique()->count());
        $this->assertSame(1, gk_booking::where('status_booking', 6)->count());
        $activeDemo = $bookings->firstWhere('status_booking', 6);
        $this->assertSame('2026-10-20', $activeDemo->tanggal_masuk);
        $this->assertSame('2026-10-22', $activeDemo->tanggal_keluar);
        $finishedDemo = $bookings->firstWhere('status_booking', 8);
        $this->assertSame('2026-10-16', $finishedDemo->tanggal_masuk);
        $this->assertSame('2026-10-18', $finishedDemo->tanggal_keluar);
        foreach ($finishedDemo->pendakis as $member) {
            $this->assertSame('2026-10-16 08:00', $member->checkin_at->format('Y-m-d H:i'));
            $this->assertSame('2026-10-18 16:00', $member->actual_checkout_at->format('Y-m-d H:i'));
        }
        foreach ($bookings as $booking) {
            $this->assertNotNull($booking->gktiket);
            $this->assertSame((int) $booking->gktiket->id_destinasi, (int) $booking->gateMasuk->id_destinasi);
            $this->assertSame(3, $booking->pendakis->count());
            $this->assertSame(3, (int) $booking->total_pendaki_wni + (int) $booking->total_pendaki_wna);
            $this->assertEquals($booking->pendakis->sum('tagihan'), $booking->total_pembayaran);
            $this->assertContains((int) $booking->status_booking, range(0, 9));
            foreach ($booking->pendakis as $member) {
                $this->assertNotNull($member->biodata);
                if ($member->actual_checkout_at) {
                    $this->assertTrue($member->actual_checkout_at->greaterThanOrEqualTo($member->checkin_at));
                }
            }
        }
        $cancelled = $bookings->firstWhere('status_booking', 9);
        $this->assertNotNull($cancelled->cancelled_at);
        $this->assertSame((int) $cancelled->id_user, (int) $cancelled->cancelled_by);
        $batal = $bookings->first(fn ($booking) => $booking->getStatusBooking()->code === 50);
        $this->assertSame(3, $batal->riwayatPendakian()->where('status', 1)->count());
        $this->assertSame(['high', 'medium'], GkEmergencyMessage::orderBy('severity')->pluck('severity')->sort()->values()->all());
        $snapshot = $bookings->mapWithKeys(fn ($booking) => [$booking->id => $booking->getAttributes()])->all();
        $counts = [gk_pendaki::count(), statusPendaki::count(), pembayaran::count()];
        $postSnapshot = \App\Models\GkPost::orderBy('id')->get(['id', 'id_gate', 'nama', 'urutan', 'qr_code_value', 'status'])->toArray();
        $gateSnapshot = \App\Models\gk_gates::orderBy('id')->get(['id', 'nama', 'id_destinasi'])->toArray();
        $active = $bookings->firstWhere('status_booking', 6);
        $post = \App\Models\GkPost::where('id_gate', $active->gate_masuk)->orderBy('urutan')->firstOrFail();
        $log = \App\Models\GkCheckpointLog::firstOrCreate([
            'id_booking' => $active->id, 'id_pendaki' => $active->pendakis->first()->id,
            'id_post' => $post->id, 'phase' => 'naik',
        ], ['method' => 'qr', 'checked_at' => now()]);
        $attendanceSnapshot = $log->fresh()->getAttributes();

        $this->seed(DatabaseSeeder::class);
        $this->assertSame(13, gk_booking::count());
        $this->assertSame(2, GkEmergencyMessage::count());
        $this->assertSame($counts, [gk_pendaki::count(), statusPendaki::count(), pembayaran::count()]);
        $this->assertSame($postSnapshot, \App\Models\GkPost::orderBy('id')->get(['id', 'id_gate', 'nama', 'urutan', 'qr_code_value', 'status'])->toArray());
        $this->assertSame($gateSnapshot, \App\Models\gk_gates::orderBy('id')->get(['id', 'nama', 'id_destinasi'])->toArray());
        $this->assertSame($attendanceSnapshot, $log->fresh()->getAttributes());
        foreach ($snapshot as $id => $attributes) {
            $this->assertSame($attributes, gk_booking::findOrFail($id)->getAttributes());
        }
        // Repeat the same scenario after the original ticket becomes overdue.
        $before = $active->getAttributes();
        $members = $active->pendakis()->pluck('id_bio')->all();
        Carbon::setTestNow(Carbon::parse('2026-10-25 12:00:00'));

        $this->seed(DatabaseSeeder::class);

        $this->assertSame($before, $active->fresh()->getAttributes());
        $this->assertSame(61, (int) $active->fresh()->getStatusBooking()->code);
        $this->assertSame(1, gk_booking::where('status_booking', 6)->count());
        $this->assertSame(13, gk_booking::count());
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
        $ticket = gk_booking::where('keterangan', 'Data demo seeding: cakupan status tiket 9.')->firstOrFail();
        $ticket->update(['cancellation_reason' => 'Alasan pengguna.']);
        $ticketSnapshot = $ticket->fresh()->getAttributes();
        Event::fake([\App\Events\EmergencyBroadcast::class]);

        $this->seed([EmergencyMessageSeeder::class, BookingStatusSeeder::class]);
        $this->assertSame($snapshot, $alert->fresh()->getAttributes());
        $this->assertSame($ticketSnapshot, $ticket->fresh()->getAttributes());
        $this->assertSame(2, GkEmergencyMessage::count());
        Event::assertNotDispatched(\App\Events\EmergencyBroadcast::class);

        $active = gk_booking::where('status_booking', 6)->firstOrFail();
        $active->update(['keterangan' => 'Booking operasional pengguna']);
        $before = $active->fresh()->getAttributes();
        $counts = [gk_booking::count(), gk_pendaki::count(), statusPendaki::count()];
        $this->seed([
            \Database\Seeders\BookingSeeder::class,
            \Database\Seeders\CompletedBookingSeeder::class,
            BookingStatusSeeder::class,
        ]);
        $this->assertSame($before, $active->fresh()->getAttributes());
        $this->assertSame($counts, [gk_booking::count(), gk_pendaki::count(), statusPendaki::count()]);
        // A database without any eligible source is also a valid no-op.
        $active->update(['keterangan' => 'Data demo seeding: pendakian aktif untuk uji fitur pelacakan jejak.', 'status_booking' => 8]);
        $this->seed(BookingStatusSeeder::class);
        $this->assertSame($counts, [gk_booking::count(), gk_pendaki::count(), statusPendaki::count()]);
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
