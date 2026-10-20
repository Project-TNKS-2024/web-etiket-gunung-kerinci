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

    public function test_full_seed_covers_every_model_status_with_consistent_relations_and_can_repeat(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-10-20 12:00:00'));
        $this->seed(DatabaseSeeder::class);
        $expected = [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 40, 50, 51, 61];
        $bookings = gk_booking::with('pendakis.getStatus')->get();
        $actual = $bookings->map(fn ($booking) => (int) $booking->getStatusBooking()->code)->sort()->values()->all();
        $this->assertSame($expected, $actual);
        $this->assertSame(14, $bookings->pluck('unique_code')->unique()->count());
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

        $this->seed(DatabaseSeeder::class);
        $this->assertSame(14, gk_booking::count());
        $this->assertSame(2, GkEmergencyMessage::count());
        $this->assertSame($counts, [gk_pendaki::count(), statusPendaki::count(), pembayaran::count()]);
        foreach ($snapshot as $id => $attributes) {
            $this->assertSame($attributes, gk_booking::findOrFail($id)->getAttributes());
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
