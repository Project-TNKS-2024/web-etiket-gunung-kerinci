<?php

namespace Database\Seeders;

use App\Http\Controllers\helper\BookingHelperController;
use App\Models\gk_booking;
use App\Models\pembayaran;
use App\Models\statusPendaki;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BookingStatusSeeder extends Seeder
{
    public function run(): void
    {
        $source = gk_booking::where('keterangan', 'Data demo seeding: pendakian aktif untuk uji fitur pelacakan jejak.')
            ->where('status_booking', 6)
            ->whereHas('user', fn ($query) => $query->where('email', 'satria@gmail.com'))
            ->whereHas('pendakis')->first();

        if (! $source) {
            $this->command?->info('BookingStatusSeeder: sumber demo aktif tidak tersedia, dilewati.');

            return;
        }

        DB::transaction(function () use ($source): void {
            $source = gk_booking::whereKey($source->id)->lockForUpdate()->firstOrFail();
            $covered = gk_booking::with('pendakis.getStatus')->get()
                ->map(fn ($booking) => (int) $booking->getStatusBooking()->code)->all();

            // Status 6/61 berasal dari satu tiket aktif utama; jangan kloning pendakian aktif.
            foreach ([0, 1, 2, 3, 4, 5, 7, 8, 9, 40, 50, 51] as $code) {
                if (in_array($code, $covered, true)) {
                    continue;
                }

                $description = 'Data demo seeding: cakupan status tiket '.$code.'.';
                // Identitas contoh stabil; jangan menimpa tiket yang sudah diproses.
                $booking = gk_booking::where('id_user', $source->id_user)
                    ->where('keterangan', $description)->first();
                if ($booking) {
                    // Jangan mengembalikan tiket demo yang sudah diproses pengguna.
                    $this->command?->warn('BookingStatusSeeder: contoh status '.$code.' sudah berubah, dipertahankan.');

                    continue;
                }
                $this->createExample($source, $code, $description);
            }
        });
    }

    private function createExample(gk_booking $source, int $code, string $description): void
    {
        $base = match ($code) {
            40, 51 => 4,
            50 => 5,
            default => $code,
        };
        $start = match ($code) {
            40, 7, 8 => Carbon::today()->subDays(4),
            51 => Carbon::today(),
            default => Carbon::today()->addDays(7),
        };
        $end = $start->copy()->addDays(2);
        $helper = app(BookingHelperController::class);
        do {
            $ticketCode = $helper->generateCode(10);
        } while (gk_booking::where('unique_code', $ticketCode)->exists());

        // Replikasi atribut saja: tidak menyalin pembayaran, checkpoint, atau tracking.
        $booking = $source->replicate();
        $booking->fill([
            'tanggal_masuk' => $start->toDateString(),
            'tanggal_keluar' => $end->toDateString(),
            'total_hari' => 3,
            'status_booking' => $base,
            'status_pembayaran' => in_array($base, [4, 5, 6, 7, 8], true),
            'unique_code' => $ticketCode,
            'keterangan' => $description,
            'dataStruk' => null,
            'id_booking_master' => null,
            'validator' => null,
            'cancelled_at' => $code === 9 ? now() : null,
            'cancelled_by' => $code === 9 ? $source->id_user : null,
            'cancellation_reason' => $code === 9 ? 'Pembatalan pesanan demo sebelum pembayaran.' : null,
        ]);
        $booking->save();

        $total = 0;
        foreach ($source->pendakis()->with('biodata')->get() as $sourceMember) {
            $checkedIn = in_array($base, [6, 7, 8], true);
            $checkedOut = in_array($base, [7, 8], true);
            $member = $sourceMember->replicate();
            $member->fill([
                'booking_id' => $booking->id,
                'usia' => Carbon::parse($sourceMember->biodata->tanggal_lahir)->age,
                'tracking_phase' => $checkedOut ? 'turun' : 'naik',
                'checkin_at' => $checkedIn ? $start->copy()->setHour(8) : null,
                'actual_checkout_at' => $checkedOut ? $end->copy()->setHour(16) : null,
                'checkout_recorded_at' => $checkedOut ? $end->copy()->setHour(16) : null,
                'checkout_recorded_by' => null,
                'checkout_reason' => $checkedOut ? 'Check-out demo.' : null,
            ]);
            $member->save();
            $member->update(['tagihan' => $helper->getTagihanPendaki($member)]);
            $total += $member->tagihan;

            $history = match (true) {
                $code === 50 => [1 => now()],
                $checkedOut => [2 => $member->checkin_at, 3 => $member->actual_checkout_at],
                $checkedIn => [2 => $member->checkin_at],
                default => [],
            };
            foreach ($history as $status => $time) {
                $record = new statusPendaki([
                    'id_pendaki' => $member->id,
                    'status' => $status,
                    'detail' => 'Riwayat demo cakupan status tiket.',
                ]);
                $record->created_at = $time;
                $record->updated_at = $time;
                $record->save();
            }
        }
        $booking->update(['total_pembayaran' => $total]);
        if ($booking->status_pembayaran) {
            pembayaran::create([
                'id_booking' => $booking->id,
                'amount' => $total,
                'status' => 'success',
                'payment_method' => 'demo',
                'keterangan' => 'DATA DEMO: pembayaran simulasi tanpa transaksi gateway.',
            ]);
        }
    }
}
