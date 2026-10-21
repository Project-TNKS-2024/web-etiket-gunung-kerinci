<?php

namespace Database\Seeders;

use App\Http\Controllers\helper\BookingHelperController;
use App\Models\gk_booking;
use App\Models\statusPendaki;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CompletedBookingSeeder extends Seeder
{
    private const DESCRIPTION = 'Data demo seeding: tiket selesai dengan anggota dan perjalanan yang sama.';

    public function run(): void
    {
        $source = gk_booking::where('status_booking', 6)
            ->where('keterangan', 'Data demo seeding: pendakian aktif untuk uji fitur pelacakan jejak.')
            ->whereHas('user', fn ($query) => $query->where('email', 'satria@gmail.com'))
            ->first();

        if (! $source) {
            $this->command?->warn('CompletedBookingSeeder: jalankan BookingSeeder terlebih dahulu.');

            return;
        }

        DB::transaction(function () use ($source): void {
            $source = gk_booking::whereKey($source->id)->lockForUpdate()->firstOrFail();
            if (gk_booking::where('id_user', $source->id_user)->where('keterangan', self::DESCRIPTION)->exists()) {
                return;
            }

            $helper = app(BookingHelperController::class);
            do {
                $code = $helper->generateCode(10);
            } while (gk_booking::where('unique_code', $code)->exists());

            $finished = $source->replicate(['cancelled_at', 'cancelled_by', 'cancellation_reason']);
            $start = Carbon::today()->subDays(4);
            $end = $start->copy()->addDays(2);
            $finished->fill([
                'tanggal_masuk' => $start->toDateString(),
                'tanggal_keluar' => $end->toDateString(),
                'total_hari' => 3,
                'dataStruk' => null,
                'status_booking' => 8,
                'status_pembayaran' => 1,
                'unique_code' => $code,
                'keterangan' => self::DESCRIPTION,
            ]);
            $finished->save();

            $checkinAt = $start->copy()->setHour(8);
            $checkoutAt = $end->copy()->setHour(16);
            $total = 0;
            foreach ($source->pendakis as $sourceMember) {
                $member = $sourceMember->replicate();
                $member->fill([
                    'booking_id' => $finished->id,
                    'tracking_phase' => 'turun',
                    'checkin_at' => $checkinAt,
                    'actual_checkout_at' => $checkoutAt,
                    'checkout_recorded_at' => $checkoutAt,
                    'checkout_recorded_by' => null,
                    'checkout_reason' => 'Data demo tiket selesai.',
                ]);
                $member->save();
                $member->update(['tagihan' => $helper->getTagihanPendaki($member)]);
                $total += $member->tagihan;

                foreach ([2 => $checkinAt, 3 => $checkoutAt] as $status => $time) {
                    $history = new statusPendaki([
                        'id_pendaki' => $member->id,
                        'status' => $status,
                        'detail' => 'Riwayat demo tiket selesai.',
                    ]);
                    $history->created_at = $time;
                    $history->updated_at = $time;
                    $history->save();
                }
            }
            $finished->update(['total_pembayaran' => $total]);
        });

        $this->command?->info('CompletedBookingSeeder: tiket selesai tersedia; tiket aktif tetap dipertahankan.');
    }
}
