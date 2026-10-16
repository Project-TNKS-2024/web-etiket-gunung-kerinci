<?php

namespace Tests\Feature;

use App\Models\GkOverstayCharge;
use App\Models\GkOverstayPayment;
use App\Models\gk_booking;
use App\Models\gk_pendaki;
use App\Models\statusPendaki;
use App\Models\User;
use App\Services\AdditionalChargePaymentService;
use App\Services\CheckoutService;
use App\Services\OverstayCalculator;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use InvalidArgumentException;
use Tests\TestCase;

class OverstayCheckoutTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private gk_booking $booking;
    private gk_pendaki $pendaki;

    protected function setUp(): void
    {
        parent::setUp();
        config(['app.timezone' => 'UTC']);
        date_default_timezone_set('UTC');
        $now = now();

        $this->admin = User::create(['email' => 'admin@test.id', 'password' => 'password', 'role' => 'admin']);

        $bioId = (string) Str::uuid();
        DB::table('biodatas')->insert([
            'id' => $bioId, 'nik' => $bioId, 'kenegaraan' => 'ID', 'first_name' => 'Pendaki',
            'last_name' => 'Satu', 'lampiran_identitas' => 't.jpg', 'no_hp' => '0812',
            'jenis_kelamin' => 'l', 'tanggal_lahir' => '1990-01-01',
            'created_at' => $now, 'updated_at' => $now,
        ]);

        $dest = DB::table('destinasis')->insertGetId(['nama' => 'Kerinci', 'kategori' => 'gunung', 'lokasi' => 'Jambi', 'created_at' => $now, 'updated_at' => $now]);
        $gate = DB::table('gk_gates')->insertGetId(['nama' => 'Pintu', 'id_destinasi' => $dest, 'created_at' => $now, 'updated_at' => $now]);
        $pkg = DB::table('gk_paket_tikets')->insertGetId(['id_destinasi' => $dest, 'nama' => 'Pendakian', 'created_at' => $now, 'updated_at' => $now]);
        $tiket = DB::table('gk_tiket_pendakis')->insertGetId([
            'id_paket_tiket' => $pkg, 'kategori_pendaki' => 'wni', 'harga_masuk_wk' => 50000, 'harga_masuk_wd' => 40000,
            'harga_kemah' => 0, 'harga_traking' => 0, 'harga_ansuransi' => 0, 'created_at' => $now, 'updated_at' => $now,
        ]);

        $this->booking = gk_booking::create([
            'id_user' => $this->admin->id, 'id_tiket' => $pkg,
            'tanggal_masuk' => '2026-10-17', 'tanggal_keluar' => '2026-10-19',
            'total_hari' => 3, 'total_pendaki_wni' => 1, 'total_pendaki_wna' => 0,
            'gate_masuk' => $gate, 'gate_keluar' => $gate, 'status_booking' => 6, 'total_pembayaran' => 0,
        ]);
        $this->pendaki = gk_pendaki::create(['booking_id' => $this->booking->id, 'id_bio' => $bioId, 'usia' => 30]);

        // Check-in: status 2 + snapshot tarif beku.
        statusPendaki::create(['id_pendaki' => $this->pendaki->id, 'status' => 2, 'detail' => 'Melakukan pendakian', 'validator' => $this->admin->id]);
        DB::table('gk_pendaki_tarif_snapshots')->insert([
            'id_pendaki' => $this->pendaki->id, 'kategori' => 'wni', 'harga_masuk_wk' => 50000, 'harga_masuk_wd' => 40000,
            'source' => 'snapshot', 'created_at' => $now, 'updated_at' => $now,
        ]);
        $this->pendaki->update(['checkin_at' => Carbon::parse('2026-10-17 08:00:00', 'Asia/Jakarta')->utc()]);
    }

    private function checkoutAt(string $time): void
    {
        $now = Carbon::parse($time, 'Asia/Jakarta');
        app(CheckoutService::class)->checkout($this->booking, [
            ['pendaki_id' => $this->pendaki->id, 'actual_checkout_at' => $now, 'reason' => null],
        ], $this->admin, $now);
    }

    public function test_checkout_on_exit_day_creates_no_charge(): void
    {
        $this->checkoutAt('2026-10-19 23:30:00');
        $this->assertSame(0, GkOverstayCharge::count());
        $this->assertNotNull($this->pendaki->fresh()->actual_checkout_at);
    }

    public function test_on_time_checkout_stays_on_time_after_database_reload(): void
    {
        $this->checkoutAt('2026-10-19 23:30:00');

        $pendaki = $this->pendaki->fresh();
        $this->assertSame('2026-10-19T23:30:00+07:00', $pendaki->actual_checkout_at->setTimezone('Asia/Jakarta')->toIso8601String());
        $summary = app(OverstayCalculator::class)->summary($pendaki);
        $this->assertSame(0, $summary['overdue_days']);
        $this->assertSame(0, $summary['amount']);
        $this->assertSame('none', $summary['payment_state']);
    }

    public function test_checkin_controller_preserves_time_and_allows_same_morning_checkout(): void
    {
        $this->pendaki->getStatus()->delete();
        $this->travelTo(Carbon::parse('2026-10-17 08:00:00', 'Asia/Jakarta')->utc());
        $this->actingAs($this->admin)->withoutMiddleware()
            ->post('/admin/destinasi/booking/updateStatus', [
                'booking_id' => $this->booking->id, 'name' => 2,
                'pendakis' => [$this->pendaki->id => 1],
            ])->assertSessionHasNoErrors();

        $this->assertSame('2026-10-17T08:00:00+07:00', $this->pendaki->fresh()->checkin_at->setTimezone('Asia/Jakarta')->toIso8601String());
        $this->checkoutAt('2026-10-17 10:00:00');
        $this->assertSame(3, (int) $this->pendaki->fresh()->getStatus->last()->status);
    }

    public function test_legacy_checkout_cannot_bypass_finalization(): void
    {
        $this->travelTo(Carbon::parse('2026-10-20 06:00:00', 'Asia/Jakarta')->utc());
        $this->actingAs($this->admin)->withoutMiddleware()
            ->post('/admin/destinasi/booking/updateStatus', [
                'booking_id' => $this->booking->id, 'name' => 3,
                'pendakis' => [$this->pendaki->id => 1],
            ])->assertSessionHasErrors('name');

        $this->assertSame(2, (int) $this->pendaki->fresh()->getStatus->last()->status);
        $this->assertNull($this->pendaki->fresh()->actual_checkout_at);
    }

    public function test_legacy_undo_cannot_remove_paid_checkout(): void
    {
        $this->checkoutAt('2026-10-20 06:00:00');
        $charge = GkOverstayCharge::where('id_pendaki', $this->pendaki->id)->firstOrFail();
        app(AdditionalChargePaymentService::class)->record($this->booking, $charge, 'Tunai', $this->admin);

        $this->actingAs($this->admin)->withoutMiddleware()
            ->post('/admin/destinasi/booking/updateStatus', [
                'booking_id' => $this->booking->id, 'name' => 3,
                'pendakis' => [$this->pendaki->id => 0],
            ])->assertSessionHasErrors('name');

        $this->assertSame(3, (int) $this->pendaki->fresh()->getStatus->last()->status);
        $this->assertNotNull($this->pendaki->fresh()->actual_checkout_at);
        $this->assertSame(40000, $charge->fresh()->payment->amount);
    }

    public function test_booking_summary_preserves_server_time(): void
    {
        $now = Carbon::parse('2026-10-20 14:15:00', 'Asia/Jakarta');
        $summary = app(OverstayCalculator::class)->bookingSummary($this->booking, $now);
        $this->assertSame('2026-10-20T14:15:00+07:00', $summary['server_now']);
        $this->assertSame('14:15', $now->format('H:i'));
    }

    public function test_checkout_form_and_ticket_api_preserve_late_evening_return(): void
    {
        $this->travelTo(Carbon::parse('2026-10-19 23:30:00', 'Asia/Jakarta')->utc());
        $this->actingAs($this->admin)->withoutMiddleware()
            ->post('/admin/destinasi/booking/checkout', [
                'booking_id' => $this->booking->id,
                'checkout' => [$this->pendaki->id => '2026-10-19T23:30'],
            ])->assertSessionHasNoErrors();

        $this->getJson('/api/booking/'.$this->booking->id.'/tiket')
            ->assertOk()
            ->assertJsonPath('data.overstay.pendakis.0.actual_checkout_at', '2026-10-19T23:30:00+07:00')
            ->assertJsonPath('data.overstay.pendakis.0.amount', 0)
            ->assertJsonPath('data.overstay.pendakis.0.payment_state', 'none');
    }

    public function test_payment_timestamp_survives_reload_in_wib(): void
    {
        $this->checkoutAt('2026-10-20 06:00:00');
        $charge = GkOverstayCharge::where('id_pendaki', $this->pendaki->id)->firstOrFail();
        $paidAt = Carbon::parse('2026-10-20 06:30:00', 'Asia/Jakarta');
        $payment = app(AdditionalChargePaymentService::class)->record(
            $this->booking, $charge, 'Tunai', $this->admin, paidAt: $paidAt
        );
        $this->assertSame('2026-10-20T06:30:00+07:00', $payment->fresh()->paid_at->setTimezone('Asia/Jakarta')->toIso8601String());
        $this->assertSame('2026-10-20T06:00:00+07:00', $charge->fresh()->finalized_at->setTimezone('Asia/Jakarta')->toIso8601String());
    }

    public function test_checkout_next_day_creates_weekday_charge(): void
    {
        $this->checkoutAt('2026-10-20 06:00:00'); // Selasa = weekday
        $charge = GkOverstayCharge::where('id_pendaki', $this->pendaki->id)->first();
        $this->assertNotNull($charge);
        $this->assertSame('final', $charge->status);
        $this->assertSame(40000, $charge->total);
        $this->assertSame(1, $charge->items()->count());
        $this->assertSame('weekday', $charge->items->first()->day_type);
    }

    public function test_holiday_uses_weekend_rate(): void
    {
        DB::table('events')->insert([
            'judul' => 'Libur', 'tanggal' => '2026-10-20', 'libur' => 1,
            'created_at' => now(), 'updated_at' => now(),
        ]);
        $this->checkoutAt('2026-10-20 06:00:00');
        $charge = GkOverstayCharge::where('id_pendaki', $this->pendaki->id)->first();
        $this->assertSame(50000, $charge->total);
        $this->assertSame('holiday', $charge->items->first()->day_type);
    }

    public function test_backdated_checkout_requires_reason(): void
    {
        $now = Carbon::parse('2026-10-20 12:00:00', 'Asia/Jakarta');
        $this->expectException(InvalidArgumentException::class);
        app(CheckoutService::class)->checkout($this->booking, [
            ['pendaki_id' => $this->pendaki->id, 'actual_checkout_at' => Carbon::parse('2026-10-19 10:00:00', 'Asia/Jakarta'), 'reason' => null],
        ], $this->admin, $now);
    }

    public function test_payment_recorded_once_and_rejected_when_double(): void
    {
        $this->checkoutAt('2026-10-20 06:00:00');
        $charge = GkOverstayCharge::where('id_pendaki', $this->pendaki->id)->first();

        app(AdditionalChargePaymentService::class)->record($this->booking, $charge, 'Tunai', $this->admin);
        $this->assertSame(1, GkOverstayPayment::count());
        $this->assertSame(40000, GkOverstayPayment::first()->amount);

        $this->expectException(InvalidArgumentException::class);
        app(AdditionalChargePaymentService::class)->record($this->booking, $charge, 'Tunai', $this->admin);
    }
}
