<?php

namespace Database\Seeders;

use App\Http\Controllers\helper\BookingHelperController;
use App\Models\bio_pendaki;
use App\Models\destinasi;
use App\Models\gk_booking;
use App\Models\gk_gates;
use App\Models\gk_paket_tiket;
use App\Models\gk_pendaki;
use App\Models\GkCheckpointLog;
use App\Models\GkOverstayCharge;
use App\Models\GkOverstayChargeItem;
use App\Models\GkOverstayPayment;
use App\Models\GkPost;
use App\Models\pembayaran;
use App\Models\statusPendaki;
use App\Models\User;
use App\Services\OverstayCalculator;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Tujuh skenario tiket: Satria belum berangkat, Raka mendaki, Bima terlambat.
 *
 * Semua tanggal dihitung dari satu acuan T (tanggal seeder dijalankan, WIB).
 * Identitas skenario memakai unique_code tetap supaya seeding ulang tidak
 * menggandakan booking. Dua skenario selesai menyelaraskan riwayat demo saat seed ulang.
 */
class BookingSeeder extends Seeder
{
    private const PREFIX = 'Pendakian: ';
    private const LEGACY_PREFIX = 'Data demo TNKS: ';

    private const PURCHASE_LEAD_DAYS = [
        'draft' => 0,
        'cancelled' => 1,
        'finished' => 2,
        'late' => 1,
        'active' => 2,
        'checked_in' => 1,
        'overdue' => 0,
    ];

    private const SCENARIOS = [
        'draft' => ['code' => 'KRNPESAN01', 'legacy_code' => 'DEMODRAFT1', 'label' => 'draft pendaftaran'],
        'cancelled' => ['code' => 'KRNBATAL01', 'legacy_code' => 'DEMOBATAL1', 'label' => 'draft dibatalkan'],
        'finished' => ['code' => 'KRNLUNAS01', 'legacy_code' => 'DEMOSELES1', 'label' => 'pendakian selesai'],
        'late' => ['code' => 'KRNTELAT01', 'legacy_code' => 'DEMOTELAT1', 'label' => 'pendakian selesai terlambat'],
        'active' => ['code' => 'KRNAKTIF01', 'legacy_code' => 'DEMOAKTIF1', 'label' => 'tiket aktif'],
        'checked_in' => ['code' => 'KRNCEKIN01', 'legacy_code' => 'DEMOCEKIN1', 'label' => 'sedang mendaki'],
        'overdue' => ['code' => 'KRNLEWAT01', 'legacy_code' => 'DEMOLEWAT1', 'label' => 'belum check-out'],
    ];

    private User $owner;

    private int $destinationId;

    private int $packageId;

    private int $gateId;

    private Carbon $today;

    private BookingHelperController $helper;

    /** @var array<string, bio_pendaki> */
    private array $mainBios = [];

    /** @var array<string, bio_pendaki> */
    private array $extraBios = [];

    /** @var array<string, User> */
    private array $scenarioOwners = [];

    public function run(): void
    {
        DB::transaction(fn () => $this->seedScenarios());
    }

    private function seedScenarios(): void
    {
        $this->owner = User::where('email', 'satria@gmail.com')->first();
        if (! $this->owner || ! $this->owner->id_bio || ! $this->owner->biodata) {
            $this->command?->warn('BookingSeeder: akun Satria beserta biodatanya harus tersedia, jalankan UserSampelSeeder dahulu.');

            return;
        }

        $this->destinationId = destinasi::where('nama', 'Gunung Kerinci')->firstOrFail()->id;
        $this->packageId = gk_paket_tiket::where('id_destinasi', $this->destinationId)
            ->where('nama', 'Umum')->firstOrFail()->id;
        $this->gateId = gk_gates::where('id_destinasi', $this->destinationId)
            ->where('nama', 'Desa Kersik Tuo')->firstOrFail()->id;

        $this->today = Carbon::now(config('app.timezone'))->startOfDay();
        $this->helper = app(BookingHelperController::class);

        $this->mainBios = $this->mainBios();
        if (count($this->mainBios) !== 3) {
            $this->command?->warn('BookingSeeder: biodata Satria, Mukhtada, dan Opi belum lengkap.');

            return;
        }
        $this->extraBios = $this->extraBios();
        $raka = User::where('email', 'raka@gmail.com')->firstOrFail();
        if (! $raka->biodata || $raka->biodata->nik !== '1234567834567352') {
            throw new \RuntimeException('Identitas akun Raka tidak sesuai.');
        }
        $bima = User::firstOrCreate(['email' => 'bima@gmail.com'], [
            'password' => 'password',
            'role' => 'user',
            'gauth_type' => 'manual',
            'id_bio' => $this->extraBios['bima']->id,
            'email_verified_at' => now(),
        ]);
        if ($bima->id_bio !== $this->extraBios['bima']->id) {
            throw new \RuntimeException('Email Bima dipakai identitas lain; perubahan dibatalkan.');
        }
        $this->scenarioOwners = ['checked_in' => $raka, 'overdue' => $bima];
        $this->assignScenarioOwner('checked_in', $raka, $this->mainBios['satria']->id);
        $this->assignScenarioOwner('overdue', $bima);

        $legacyCount = gk_booking::where('keterangan', 'like', 'Data demo seeding:%')->count();
        if ($legacyCount > 0) {
            $this->command?->warn(
                "BookingSeeder: {$legacyCount} contoh demo lama dipertahankan; tidak ada data lama yang dihapus."
            );
        }

        $this->seedDraft();
        $this->seedCancelled();
        $this->seedFinished();
        $this->seedLate();
        $this->seedActive();
        $satria = $this->owner;
        try {
            $this->owner = $raka;
            $this->seedCheckedIn();
            $this->owner = $bima;
            $this->seedOverdue();
        } finally {
            $this->owner = $satria;
        }
        $this->syncDemoPurchaseTimes();

        $codes = collect(self::SCENARIOS)->pluck('code')->all();
        $created = gk_booking::whereIn('unique_code', $codes)->count();
        if ($created < count(self::SCENARIOS)) {
            $this->command?->warn(
                "BookingSeeder: hanya {$created} dari ".count(self::SCENARIOS).
                ' skenario yang tersedia. Periksa konflik pendakian aktif pada database lama.'
            );
        } else {
            $this->command?->info('BookingSeeder: Satria dua draft/tiga tiket; Raka mendaki; Bima terlambat belum checkout.');
        }
    }

    private function seedDraft(): void
    {
        if ($this->existing('draft')) {
            return;
        }

        $start = $this->today->copy()->addDays(7);
        $this->createBooking(
            'draft',
            [$this->mainBios['satria']],
            $start,
            $start->copy()->addDays(2),
            2,
            false,
            $this->today->copy()->subDay()->setTime(9, 0)
        );

        $this->command?->info('BookingSeeder: draft pendaftaran (status 2) tersedia.');
    }

    private function seedCancelled(): void
    {
        if ($this->existing('cancelled')) {
            return;
        }

        $start = $this->today->copy()->addDays(7);
        $createdAt = $this->today->copy()->subDays(3)->setTime(10, 0);
        $cancelledAt = $this->today->copy()->subDay()->setTime(14, 30);
        $booking = $this->createBooking(
            'cancelled',
            [$this->mainBios['satria']],
            $start,
            $start->copy()->addDays(2),
            9,
            false,
            $createdAt,
            [
                'cancelled_at' => $cancelledAt,
                'cancelled_by' => $this->owner->id,
                'cancellation_reason' => 'Dibatalkan sebelum pendakian karena rencana berubah.',
            ]
        );

        $this->addHistory($booking->pendakis->first(), 1, $cancelledAt, 'Pembatalan sebelum pendakian.');
        $this->command?->info('BookingSeeder: draft dibatalkan (status 9) tersedia.');
    }

    private function seedFinished(): void
    {
        $this->seedCompletedScenario('finished', false);
    }

    private function seedLate(): void
    {
        $this->seedCompletedScenario('late', true);
    }

    private function seedCompletedScenario(string $scenario, bool $late): void
    {
        DB::transaction(function () use ($scenario, $late): void {
            $start = $this->today->copy()->subDays(8);
            $end = $this->today->copy()->subDays(6);
            $booking = $this->existing($scenario) ?? $this->createBooking(
                $scenario, array_values($this->mainBios), $start, $end, 8, true,
                $this->today->copy()->subDays(12)->setTime(9, 0)
            );
            $booking = gk_booking::whereKey($booking->id)->lockForUpdate()->with('pendakis')->firstOrFail();
            $expectedBios = collect($this->mainBios)->pluck('id')->sort()->values()->all();
            if ($booking->id_user !== $this->owner->id
                || $booking->keterangan !== self::PREFIX.self::SCENARIOS[$scenario]['label']
                || $booking->pendakis->pluck('id_bio')->sort()->values()->all() !== $expectedBios) {
                throw new \RuntimeException('Skenario selesai hanya boleh memperbarui booking yang sesuai dan anggota yang sesuai.');
            }
            if ($booking->pembayaran()->whereNotIn('payment_method', ['demo', 'tunai'])->exists()
                || GkOverstayPayment::where('id_booking', $booking->id)->whereNotIn('method', ['demo', 'tunai'])->exists()) {
                throw new \RuntimeException('Booking memiliki pembayaran non-demo; pembaruan skenario dibatalkan.');
            }

            // Tanggal booking lama tetap dipakai; seed ulang tidak menggeser riwayat.
            $start = Carbon::parse($booking->tanggal_masuk, config('app.timezone'))->startOfDay();
            $end = Carbon::parse($booking->tanggal_keluar, config('app.timezone'))->startOfDay();
            $checkinAt = $start->copy()->setTime(8, 0);
            $checkoutAt = $late ? $end->copy()->addDay()->setTime(10, 0) : $end->copy()->setTime(16, 0);
            $this->addPayment($booking, $start->copy()->subDays(3)->setTime(10, 15), $booking->total_pembayaran);

            foreach ($booking->pendakis as $member) {
                $member->update([
                    'tracking_phase' => 'turun',
                    'checkin_at' => $checkinAt,
                    'actual_checkout_at' => $checkoutAt,
                    'checkout_recorded_at' => $checkoutAt,
                    'checkout_reason' => $late
                        ? 'Seluruh anggota terlambat satu hari.'
                        : 'Seluruh anggota selesai sesuai jadwal.',
                ]);
                foreach ([2 => $checkinAt, 3 => $checkoutAt] as $status => $at) {
                    $history = statusPendaki::firstOrNew(['id_pendaki' => $member->id, 'status' => $status]);
                    $history->detail = $status === 2 ? 'Check-in tiket.' : $member->checkout_reason;
                    $history->created_at = $at;
                    $history->updated_at = $at;
                    $history->save();
                }
                if ($late) {
                    $this->addPaidOverstayCharge($booking, $member, $checkoutAt);
                }
            }
            $this->seedCompletedRoute(
                $booking, $checkinAt, $checkoutAt,
                $late ? ['Batu Lumut', 'Shelter 1'] : [],
                $late ? ['Shelter 2'] : []
            );
            $booking->update(['status_booking' => 8]);
        });
        $this->command?->info($late
            ? 'BookingSeeder: semua anggota terlambat satu hari; 2 pos naik dan 1 pos turun tanpa rekaman; biaya tambahan lunas.'
            : 'BookingSeeder: riwayat naik-turun lengkap untuk semua anggota tiket selesai.');
    }

    private function seedCompletedRoute(
        gk_booking $booking,
        Carbon $checkinAt,
        Carbon $checkoutAt,
        array $skipAscent,
        array $skipDescent
    ): void {
        $posts = app(\App\Services\CheckpointItineraryService::class)->route((int) $booking->gate_masuk);
        $allowedSkips = $posts->slice(1, -1)->pluck('nama')->all();
        foreach (array_merge($skipAscent, $skipDescent) as $name) {
            if (!in_array($name, $allowedSkips, true)) {
                throw new \RuntimeException("Pos demo yang dilewati tidak tersedia atau merupakan gerbang/puncak: {$name}");
            }
        }
        $summitAt = $checkoutAt->copy()->startOfDay()->setTime(5, 0);
        if ($summitAt->lessThanOrEqualTo($checkinAt) || $checkoutAt->lessThanOrEqualTo($summitAt)) {
            throw new \RuntimeException('Jadwal demo tidak cukup untuk membentuk riwayat naik-turun.');
        }
        $descent = $posts->slice(0, -1)->reverse()->values();
        foreach ($booking->pendakis as $member) {
            foreach (['naik' => [$posts, $skipAscent], 'turun' => [$descent, $skipDescent]] as $phase => [$route, $skips]) {
                foreach ($route->values() as $index => $post) {
                    $identity = [
                        'id_booking' => $booking->id, 'id_pendaki' => $member->id,
                        'id_post' => $post->id, 'phase' => $phase,
                    ];
                    if (in_array($post->nama, $skips, true)) {
                        GkCheckpointLog::where($identity)->delete();
                        continue;
                    }
                    // Waktu interpolasi adalah simulasi; bukan pengukuran durasi jalur.
                    $at = $phase === 'naik'
                        ? $checkinAt->copy()->addSeconds((int) round(
                            $checkinAt->diffInSeconds($summitAt) * $index / max(1, $posts->count() - 1)
                        ))
                        : $summitAt->copy()->addSeconds((int) round(
                            $summitAt->diffInSeconds($checkoutAt) * ($index + 1) / max(1, $descent->count())
                        ));
                    $log = GkCheckpointLog::updateOrCreate($identity, [
                        'method' => 'qr', 'latitude' => $post->latitude, 'longitude' => $post->longitude,
                        'is_manual_override' => false, 'checked_at' => $at,
                    ]);
                    $log->timestamps = false;
                    $log->created_at = $at;
                    $log->updated_at = $at;
                    $log->save();
                }
            }
        }
    }

    private function seedActive(): void
    {
        $start = $this->today->copy();
        $end = $start->copy()->addDays(2);

        if ($booking = $this->existing('active')) {
            if ((int) $booking->status_booking !== 5
                || GkCheckpointLog::where('id_booking', $booking->id)->exists()
                || $booking->pendakis()->where(function ($query) {
                    $query->whereNotNull('checkin_at')->orWhereNotNull('actual_checkout_at');
                })->exists()) {
                throw new \RuntimeException('Tiket siap berangkat sudah mempunyai aktivitas; seeder tidak menghapus perjalanan yang sudah berjalan.');
            }
            $booking->update([
                'tanggal_masuk' => $start->toDateString(),
                'tanggal_keluar' => $end->toDateString(),
            ]);
            return;
        }

        $booking = $this->createBooking(
            'active',
            array_values($this->mainBios),
            $start,
            $end,
            5,
            true,
            $this->today->copy()->subDays(3)->setTime(9, 0)
        );

        $this->addPayment($booking, $this->today->copy()->subDays(2)->setTime(11, 0), $booking->total_pembayaran);
        $this->command?->info('BookingSeeder: tiket aktif (belum check-in) tersedia.');
    }

    private function seedCheckedIn(): void
    {
        if ($this->existing('checked_in')) {
            return;
        }

        $members = [$this->owner->biodata, $this->mainBios['mukhtada'], $this->mainBios['opi']];
        if ($conflicts = $this->activeConflicts($members)) {
            $this->command?->warn(
                'BookingSeeder: skenario sedang mendaki dilewati karena pendaki sudah aktif di booking lain: '.
                implode(', ', $conflicts).'.'
            );

            return;
        }

        $start = $this->today->copy()->subDay();
        $end = $this->today->copy()->addDay();
        $booking = $this->createBooking(
            'checked_in',
            $members,
            $start,
            $end,
            6,
            true,
            $this->today->copy()->subDays(4)->setTime(9, 0)
        );

        $this->addPayment($booking, $this->today->copy()->subDays(3)->setTime(10, 30), $booking->total_pembayaran);
        $checkinAt = $start->copy()->setTime(8, 0);

        foreach ($booking->pendakis as $member) {
            $member->update([
                'tracking_phase' => 'naik',
                'checkin_at' => $checkinAt,
            ]);
            $this->addHistory($member, 2, $checkinAt, 'Check-in tiket.');
            $this->addCheckpoint($booking, $member, $start->copy()->setTime(8, 30));
        }

        $this->command?->info('BookingSeeder: tiket sedang mendaki tersedia.');
    }

    private function seedOverdue(): void
    {
        if ($this->existing('overdue')) {
            return;
        }

        $members = array_values($this->extraBios);
        if ($conflicts = $this->activeConflicts($members)) {
            $this->command?->warn(
                'BookingSeeder: skenario belum check-out dilewati karena pendaki sudah aktif di booking lain: '.
                implode(', ', $conflicts).'.'
            );

            return;
        }

        $start = $this->today->copy()->subDays(4);
        $end = $this->today->copy()->subDays(2);
        $booking = $this->createBooking(
            'overdue',
            $members,
            $start,
            $end,
            6,
            true,
            $this->today->copy()->subDays(7)->setTime(9, 0)
        );

        $this->addPayment($booking, $this->today->copy()->subDays(6)->setTime(10, 0), $booking->total_pembayaran);
        $checkinAt = $start->copy()->setTime(8, 0);

        foreach ($booking->pendakis as $member) {
            $member->update([
                'tracking_phase' => 'naik',
                'checkin_at' => $checkinAt,
            ]);
            $this->addHistory($member, 2, $checkinAt, 'Check-in tiket.');
            $this->addCheckpoint($booking, $member, $start->copy()->setTime(8, 15));
        }

        $this->command?->info('BookingSeeder: tiket belum check-out tersedia dengan tim demo terpisah.');
    }

    private function syncDemoPurchaseTimes(): void
    {
        DB::transaction(function (): void {
            foreach (self::PURCHASE_LEAD_DAYS as $scenario => $daysBeforeEntry) {
                $definition = self::SCENARIOS[$scenario];
                $booking = gk_booking::where('unique_code', $definition['code'])
                    ->lockForUpdate()->with(['pembayaran', 'pendakis'])->first();
                if (! $booking) {
                    continue;
                }
                $expectedOwner = $this->scenarioOwners[$scenario] ?? $this->owner;
                if ($booking->id_user !== $expectedOwner->id
                    || $booking->keterangan !== self::PREFIX.$definition['label']
                    || $booking->pembayaran->contains(fn ($payment) => !in_array($payment->payment_method, ['demo', 'tunai'], true))) {
                    throw new \RuntimeException('Waktu pembelian hanya boleh disesuaikan pada pemesanan yang sesuai.');
                }

                $orderedAt = Carbon::parse($booking->tanggal_masuk, config('app.timezone'))
                    ->startOfDay()->subDays($daysBeforeEntry)->setTime(6, 0);
                $paidAt = $orderedAt->copy()->addMinutes(30);
                $booking->timestamps = false;
                $booking->created_at = $orderedAt;
                $latestEvent = $orderedAt;
                if ($scenario === 'cancelled') {
                    $cancelledAt = $orderedAt->copy()->addHour();
                    $booking->cancelled_at = $cancelledAt;
                    $latestEvent = $cancelledAt;
                    statusPendaki::whereIn('id_pendaki', $booking->pendakis->modelKeys())
                        ->where('status', 1)->update(['created_at' => $cancelledAt, 'updated_at' => $cancelledAt]);
                }
                if (Carbon::parse($booking->updated_at, config('app.timezone'))->lessThan($latestEvent)) {
                    $booking->updated_at = $latestEvent;
                }
                $booking->save();

                foreach ($booking->pembayaran as $payment) {
                    $payment->timestamps = false;
                    $payment->payment_method = 'tunai';
                    $payment->keterangan = 'Pembayaran tiket pendakian.';
                    $payment->created_at = $paidAt;
                    if (Carbon::parse($payment->updated_at, config('app.timezone'))->lessThan($paidAt)) {
                        $payment->updated_at = $paidAt;
                    }
                    $payment->save();
                }
                foreach ($booking->pendakis as $member) {
                    if ($member->checkout_reason) {
                        $member->update(['checkout_reason' => str_ireplace(['DATA DEMO: ', ' demo', 'demo '], '', $member->checkout_reason)]);
                    }
                    foreach ($member->getStatus as $history) {
                        $history->update(['detail' => str_ireplace(['DATA DEMO: ', ' demo', 'demo '], '', $history->detail ?? '')]);
                    }
                }
            }
        });
        $this->command?->info('BookingSeeder: pemesanan/pembayaran diseragamkan H-0/H-1/H-2 (06.00/06.30 WIB).');
    }

    private function assignScenarioOwner(string $scenario, User $target, ?string $oldBio = null): void
    {
        $definition = self::SCENARIOS[$scenario];
        $booking = gk_booking::whereIn('unique_code', [$definition['code'], $definition['legacy_code']])
            ->lockForUpdate()->first();
        if (! $booking) return;
        if (! in_array($booking->id_user, [$this->owner->id, $target->id], true)
            || ! in_array($booking->keterangan, [self::PREFIX.$definition['label'], self::LEGACY_PREFIX.$definition['label']], true)
            || $booking->pembayaran()->whereNotIn('payment_method', ['demo', 'tunai'])->exists()) {
            throw new \RuntimeException('Pemisahan akun hanya berlaku pada pemesanan seeder yang sesuai.');
        }
        if ($oldBio && $booking->id_user !== $target->id) {
            $member = $booking->pendakis()->where('id_bio', $oldBio)->lockForUpdate()->firstOrFail();
            if (DB::table('gk_sos')->where('id_pendaki', $member->id)->exists()) {
                throw new \RuntimeException('Pendaki memiliki SOS; identitas dan percakapannya tidak boleh dipindahkan.');
            }
            $member->update(['id_bio' => $target->id_bio]);
        }
        $booking->update(['id_user' => $target->id]);
    }

    private function existing(string $scenario): ?gk_booking
    {
        $definition = self::SCENARIOS[$scenario];
        $description = self::PREFIX.$definition['label'];

        $booking = gk_booking::where(function ($query) use ($definition, $description): void {
            $query->whereIn('unique_code', [$definition['code'], $definition['legacy_code']])
                ->orWhere(function ($query) use ($description): void {
                    $query->where('id_user', $this->owner->id)->where('keterangan', $description);
                });
        })->first();

        if ($booking) {
            if ($booking->id_user !== $this->owner->id || !in_array($booking->keterangan, [
                $description, self::LEGACY_PREFIX.$definition['label'],
            ], true)) {
                throw new \RuntimeException('Kode pemesanan dipakai oleh booking lain; pembaruan dibatalkan.');
            }
            $booking->fill(['unique_code' => $definition['code'], 'keterangan' => $description]);
            if ($booking->isDirty()) $booking->save();
        }

        return $booking;
    }

    /**
     * @param  bio_pendaki[]  $members
     * @param  array<string, mixed>  $extra
     */
    private function createBooking(
        string $scenario,
        array $members,
        Carbon $start,
        Carbon $end,
        int $status,
        bool $paid,
        Carbon $createdAt,
        array $extra = []
    ): gk_booking {
        $definition = self::SCENARIOS[$scenario];
        $description = self::PREFIX.$definition['label'];

        return DB::transaction(function () use (
            $definition, $description, $members, $start, $end, $status, $paid, $createdAt, $extra
        ): gk_booking {
            $wni = collect($members)->filter(fn (bio_pendaki $bio) => $bio->kenegaraan === 'ID')->count();

            $booking = new gk_booking(array_merge([
                'id_user' => $this->owner->id,
                'id_tiket' => $this->packageId,
                'tanggal_masuk' => $start->toDateString(),
                'tanggal_keluar' => $end->toDateString(),
                'total_hari' => (int) $start->diffInDays($end) + 1,
                'total_pendaki_wni' => $wni,
                'total_pendaki_wna' => count($members) - $wni,
                'gate_masuk' => $this->gateId,
                'gate_keluar' => $this->gateId,
                'status_booking' => $status,
                'status_pembayaran' => $paid,
                'total_pembayaran' => 0,
                'unique_code' => $definition['code'],
                'keterangan' => $description,
                'dataStruk' => null,
            ], $extra));
            $booking->created_at = $createdAt;
            $booking->updated_at = $createdAt;
            $booking->save();

            $total = 0;
            foreach ($members as $bio) {
                $member = gk_pendaki::create([
                    'booking_id' => $booking->id,
                    'id_bio' => $bio->id,
                    'usia' => $bio->tanggal_lahir ? Carbon::parse($bio->tanggal_lahir)->age : 25,
                    'tagihan' => 0,
                ]);
                $member->update(['tagihan' => $this->helper->getTagihanPendaki($member)]);
                $total += $member->tagihan;
            }

            $booking->update(['total_pembayaran' => $total]);

            return $booking->fresh('pendakis');
        });
    }

    private function addPayment(gk_booking $booking, Carbon $paidAt, int $amount): void
    {
        if ($booking->pembayaran()->exists()) {
            return;
        }

        $payment = new pembayaran([
            'id_booking' => $booking->id,
            'amount' => $amount,
            'status' => 'success',
            'payment_method' => 'tunai',
            'bukti_pembayaran' => null,
            'deadline' => null,
            'keterangan' => 'Pembayaran tiket pendakian.',
            'spesial' => null,
        ]);
        $payment->created_at = $paidAt;
        $payment->updated_at = $paidAt;
        $payment->save();
    }

    private function addHistory(gk_pendaki $member, int $status, Carbon $at, string $detail): void
    {
        $history = new statusPendaki([
            'id_pendaki' => $member->id,
            'status' => $status,
            'detail' => $detail,
        ]);
        $history->created_at = $at;
        $history->updated_at = $at;
        $history->save();
    }

    private function addCheckpoint(gk_booking $booking, gk_pendaki $member, Carbon $at): void
    {
        $post = GkPost::where('id_gate', $booking->gate_masuk)
            ->where('status', true)
            ->orderBy('urutan')
            ->first();
        if (! $post) {
            return;
        }

        $log = GkCheckpointLog::firstOrCreate([
            'id_pendaki' => $member->id,
            'id_post' => $post->id,
            'id_booking' => $booking->id,
            'phase' => 'naik',
        ], [
            'method' => 'qr',
            'latitude' => $post->latitude,
            'longitude' => $post->longitude,
            'is_manual_override' => false,
            'checked_at' => $at,
        ]);
        if ($log->wasRecentlyCreated) {
            $log->timestamps = false;
            $log->created_at = $at;
            $log->updated_at = $at;
            $log->save();
        }
    }

    private function addPaidOverstayCharge(gk_booking $booking, gk_pendaki $member, Carbon $checkoutAt): void
    {
        $final = app(OverstayCalculator::class)->finalize($member, $checkoutAt);
        if ($final['needs_review'] || ($final['amount'] ?? 0) <= 0) {
            throw new \RuntimeException('Tarif demo keterlambatan belum dapat dihitung; skenario dibatalkan.');
        }

        $charge = GkOverstayCharge::updateOrCreate([
            'id_booking' => $booking->id,
            'id_pendaki' => $member->id,
        ], [
            'status' => 'final',
            'total' => $final['amount'],
            'snapshot' => null,
            'finalized_at' => $checkoutAt,
            'finalized_by' => null,
        ]);
        foreach ($final['items'] as $item) {
            GkOverstayChargeItem::updateOrCreate([
                'id_overstay_charge' => $charge->id,
                'tanggal' => $item['tanggal'],
            ], [
                'day_type' => $item['day_type'],
                'kategori' => $item['kategori'],
                'harga' => $item['harga'],
                'source' => $final['source'] ?? 'master',
            ]);
        }
        $charge->items()->whereNotIn('tanggal', array_column($final['items'], 'tanggal'))->delete();

        $paidAt = $checkoutAt->copy()->addHours(1);
        $payment = GkOverstayPayment::firstOrNew([
            'id_booking' => $booking->id,
            'id_overstay_charge' => $charge->id,
        ]);
        $payment->fill([
            'amount' => $final['amount'],
            'method' => 'tunai',
            'paid_at' => $paidAt,
            'recorded_by' => null,
            'reference' => 'TNKS-OVERSTAY-'.$member->id,
            'note' => 'Biaya tambahan sudah dibayar.',
        ]);
        $payment->created_at = $paidAt;
        $payment->updated_at = $paidAt;
        $payment->save();
    }

    /** @return array<string, bio_pendaki> */
    private function mainBios(): array
    {
        $bios = [];
        foreach (['satria@gmail.com', 'mukhtada@gmail.com', 'opi@gmail.com'] as $key => $email) {
            $user = User::where('email', $email)->first();
            if (! $user?->id_bio) {
                continue;
            }
            $bio = bio_pendaki::find($user->id_bio);
            if ($bio) {
                $bios[['satria', 'mukhtada', 'opi'][$key]] = $bio;
            }
        }

        return $bios;
    }

    /** @return array<string, bio_pendaki> */
    private function extraBios(): array
    {
        $people = [
            'bima' => [
                'nik' => '1234567834567350',
                'kenegaraan' => 'ID',
                'first_name' => 'Bima',
                'last_name' => 'Saputra',
                'lampiran_identitas' => 'lampiran-demo-4.jpg',
                'no_hp' => '081234567893',
                'no_hp_darurat' => '081298765433',
                'jenis_kelamin' => 'l',
                'tanggal_lahir' => '1997-03-12',
                'provinsi' => '14',
                'kabupaten' => '276',
                'kec' => '4730',
                'desa' => '26624',
                'keterangan' => 'Anggota pendakian',
                'verified' => 'verified',
            ],
            'sari' => [
                'nik' => '1234567834567351',
                'kenegaraan' => 'ID',
                'first_name' => 'Sari',
                'last_name' => 'Lestari',
                'lampiran_identitas' => 'lampiran-demo-5.jpg',
                'no_hp' => '081234567894',
                'no_hp_darurat' => '081298765434',
                'jenis_kelamin' => 'p',
                'tanggal_lahir' => '1999-07-08',
                'provinsi' => '14',
                'kabupaten' => '276',
                'kec' => '4730',
                'desa' => '26624',
                'keterangan' => 'Anggota pendakian',
                'verified' => 'verified',
            ],
        ];

        return collect($people)->mapWithKeys(function (array $bio, string $key): array {
            $person = bio_pendaki::firstOrCreate(['nik' => $bio['nik']], $bio);
            if ($person->keterangan === 'Anggota demo tambahan') {
                $person->update(['keterangan' => $bio['keterangan']]);
            }
            return [$key => $person];
        })->all();
    }

    /**
     * @param  bio_pendaki[]  $bios
     * @return string[]
     */
    private function activeConflicts(array $bios): array
    {
        $ids = collect($bios)->pluck('id')->all();

        return gk_pendaki::whereIn('id_bio', $ids)
            ->whereHas('booking', fn ($query) => $query->where('status_booking', 6))
            ->with('biodata')
            ->get()
            ->map(fn (gk_pendaki $member) => $member->fullName)
            ->filter()
            ->values()
            ->all();
    }
}
