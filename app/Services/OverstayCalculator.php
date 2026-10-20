<?php

namespace App\Services;

use App\Models\Event;
use App\Models\gk_booking;
use App\Models\gk_pendaki;
use App\Models\gk_tiket_pendaki;
use Carbon\Carbon;
use Carbon\CarbonInterface;

/**
 * Sumber kebenaran perhitungan biaya tambahan (overstay) tiket masuk.
 *
 * Aturan yang dipakai (lihat rencana implementasi):
 * - Tiket berlaku sampai tanggal keluar pukul 23:59:59 WIB.
 * - Mulai 00:00 WIB keesokan harinya, pendaki yang sudah check-in dan belum
 *   memiliki kepulangan sah dianggap lewat jadwal, satu hari tambahan per
 *   tanggal kalender lokal.
 * - Hanya tarif masuk (weekday/weekend/libur) per kategori WNI/WNA.
 * - Estimasi memakai `now` server; final memakai `actual_checkout_at`.
 * - Tarif dibekukan lewat snapshot saat check-in; fallback ke tarif master
 *   ditandai sumbernya bila snapshot tidak ada.
 */
class OverstayCalculator
{

    /**
     * Fungsi murni: menghitung hari tambahan + rincian tarif per tanggal.
     * Dapat diuji tanpa database.
     *
     * @param string $scheduledExitDate tanggal keluar booking (Y-m-d)
     * @param int $hargaMasukWk tarif masuk weekend/libur
     * @param int $hargaMasukWd tarif masuk weekday
     * @param string $kategori wni|wna
     * @param string[] $holidayDates daftar tanggal libur (Y-m-d)
     */
    public function compute(
        string $scheduledExitDate,
        CarbonInterface $effectiveEnd,
        int $hargaMasukWk,
        int $hargaMasukWd,
        string $kategori,
        array $holidayDates = []
    ): array {
        $scheduledExit = Carbon::createFromFormat('Y-m-d', $scheduledExitDate, config('app.timezone'))->startOfDay();
        $endDay = $effectiveEnd->copy()->setTimezone(config('app.timezone'))->startOfDay();

        $extraDays = $endDay->greaterThan($scheduledExit)
            ? (int) $scheduledExit->diffInDays($endDay)
            : 0;

        $items = [];
        $cursor = $scheduledExit->copy()->addDay();
        while (!$cursor->greaterThan($endDay)) {
            $iso = $cursor->toDateString();
            $isHoliday = in_array($iso, $holidayDates, true);
            $isWeekend = $cursor->isWeekend();

            if ($isHoliday) {
                $dayType = 'holiday';
            } elseif ($isWeekend) {
                $dayType = 'weekend';
            } else {
                $dayType = 'weekday';
            }

            $rate = $dayType === 'weekday' ? $hargaMasukWd : $hargaMasukWk;

            $items[] = [
                'tanggal' => $iso,
                'day_type' => $dayType,
                'kategori' => $kategori,
                'harga' => $rate,
            ];

            $cursor->addDay();
        }

        return [
            'extra_days' => $extraDays,
            'amount' => array_sum(array_column($items, 'harga')),
            'items' => $items,
        ];
    }

    /**
     * Klasifikasi keadaan pendakian seorang pendaki.
     */
    public function hikeState(gk_pendaki $pendaki): string
    {
        $statuses = $pendaki->getStatus;
        $last = $statuses->last();

        if (!$last) {
            return 'belum_masuk';
        }

        return match ((int) $last->status) {
            1 => 'batal',
            2 => 'mendaki',
            3 => 'turun',
            default => 'belum_masuk',
        };
    }

    /**
     * Tarif masuk beku (snapshot) atau fallback master untuk seorang pendaki.
     *
     * @return array{harga_wk:int|null,harga_wd:int|null,source:string|null,kategori:string}
     */
    public function ratesForPendaki(gk_pendaki $pendaki): array
    {
        $kategori = ($pendaki->biodata->kenegaraan ?? null) === 'ID' ? 'wni' : 'wna';

        $snapshot = $pendaki->tarifSnapshot;
        if ($snapshot && $snapshot->kategori === $kategori) {
            return [
                'harga_wk' => $snapshot->harga_masuk_wk,
                'harga_wd' => $snapshot->harga_masuk_wd,
                'source' => $snapshot->source,
                'kategori' => $kategori,
            ];
        }

        $tiket = gk_tiket_pendaki::where('id_paket_tiket', $pendaki->booking->id_tiket)
            ->where('kategori_pendaki', $kategori)
            ->first();

        if ($tiket) {
            return [
                'harga_wk' => $tiket->harga_masuk_wk,
                'harga_wd' => $tiket->harga_masuk_wd,
                'source' => 'master',
                'kategori' => $kategori,
            ];
        }

        return [
            'harga_wk' => null,
            'harga_wd' => null,
            'source' => null,
            'kategori' => $kategori,
        ];
    }

    /**
     * Daftar tanggal libur (Event libur=1) pada rentang [startDate, endDate].
     *
     * @return string[]
     */
    public function holidayDates(string $startDate, string $endDate): array
    {
        if ($startDate > $endDate) {
            return [];
        }

        return Event::where('libur', 1)
            ->whereBetween('tanggal', [$startDate, $endDate])
            ->pluck('tanggal')
            ->map(fn ($d) => Carbon::parse($d)->toDateString())
            ->all();
    }

    /**
     * Ringkasan overstay untuk satu pendaki. `now` dipakai sebagai akhir efektif
     * bila pendaki masih mendaki (estimasi). `$holidays` boleh diisi untuk
     * menghindari query berulang.
     */
    public function summary(gk_pendaki $pendaki, ?CarbonInterface $now = null, ?array $holidays = null): array
    {
        $now = ($now ?? Carbon::now())->copy()->setTimezone(config('app.timezone'));

        $pendaki->loadMissing(['booking', 'biodata', 'getStatus', 'tarifSnapshot', 'overstayCharge.items', 'overstayCharge.payment']);

        $booking = $pendaki->booking;
        $scheduledExitDate = $booking->tanggal_keluar;
        $state = $this->hikeState($pendaki);
        $rates = $this->ratesForPendaki($pendaki);

        $deadlineExclusive = Carbon::createFromFormat('Y-m-d', $scheduledExitDate, config('app.timezone'))
            ->startOfDay()
            ->addDay();

        $isOverstayEligible = in_array($state, ['mendaki', 'turun'], true);

        $result = [
            'id' => $pendaki->id,
            'full_name' => trim(($pendaki->biodata->first_name ?? '') . ' ' . ($pendaki->biodata->last_name ?? '')),
            'kategori' => $rates['kategori'],
            'hike_state' => $state,
            'scheduled_exit_date' => $scheduledExitDate,
            'deadline_exclusive' => $deadlineExclusive->toIso8601String(),
            'actual_checkout_at' => $pendaki->actual_checkout_at?->copy()->setTimezone(config('app.timezone'))->toIso8601String(),
            'overdue_days' => null,
            'is_estimate' => false,
            'amount' => null,
            'charge_status' => 'none',
            'charge_id' => null,
            'payment_state' => 'none',
            'rate_source' => null,
            'paid_at' => null,
            'items' => [],
        ];

        if (!$isOverstayEligible) {
            return $result;
        }

        // Final: sudah turun, pakai waktu turun aktual.
        if ($state === 'turun' && $pendaki->actual_checkout_at) {
            $effectiveEnd = $pendaki->actual_checkout_at->copy()->setTimezone(config('app.timezone'));
            $result['is_estimate'] = false;

            // Tagihan final sudah ada? Pakai data tersimpan (tidak berubah diam-diam).
            if ($pendaki->overstayCharge) {
                return $this->attachFinalCharge($result, $pendaki);
            }

            // Belum ada tagihan final: hitung dari waktu turun aktual.
            return $this->calculate($result, $effectiveEnd, $rates, $scheduledExitDate, $holidays);
        }

        // Estimasi: masih mendaki.
        if ($state === 'mendaki') {
            $effectiveEnd = $now;
            $result['is_estimate'] = true;

            return $this->calculate($result, $effectiveEnd, $rates, $scheduledExitDate, $holidays);
        }

        return $result;
    }

    private function calculate(array $base, CarbonInterface $effectiveEnd, array $rates, string $scheduledExitDate, ?array $holidays = null): array
    {
        $result = $base;
        $result['rate_source'] = $rates['source'];

        if ($rates['harga_wk'] === null || $rates['harga_wd'] === null) {
            $result['overdue_days'] = null;
            $result['amount'] = null;
            $result['charge_status'] = 'needs_review';
            return $result;
        }

        $holidays ??= $this->holidayDates(
            Carbon::createFromFormat('Y-m-d', $scheduledExitDate, config('app.timezone'))->startOfDay()->addDay()->toDateString(),
            $effectiveEnd->copy()->setTimezone(config('app.timezone'))->startOfDay()->toDateString()
        );

        $computed = $this->compute(
            $scheduledExitDate,
            $effectiveEnd,
            (int) $rates['harga_wk'],
            (int) $rates['harga_wd'],
            $rates['kategori'],
            $holidays
        );

        $result['overdue_days'] = $computed['extra_days'];
        $result['amount'] = $computed['amount'];
        $result['items'] = $computed['items'];

        if ($computed['amount'] <= 0) {
            $result['charge_status'] = 'none';
            $result['amount'] = 0;
            $result['overdue_days'] = 0;
            return $result;
        }

        $result['charge_status'] = $result['is_estimate'] ? 'estimate' : 'final';
        $result['payment_state'] = $result['is_estimate'] ? 'none' : 'unpaid';

        return $result;
    }

    private function attachFinalCharge(array $base, gk_pendaki $pendaki): array
    {
        $charge = $pendaki->overstayCharge;
        $result = $base;
        $result['charge_status'] = $charge->status; // final|needs_review
        $result['charge_id'] = $charge->id;
        $result['rate_source'] = $charge->items->first()?->source;

        if ($charge->status === 'needs_review') {
            $result['overdue_days'] = null;
            $result['amount'] = null;
            $result['items'] = [];
            return $result;
        }

        $result['amount'] = (int) $charge->total;
        $result['overdue_days'] = $charge->items->count();
        $result['items'] = $charge->items->map(fn ($i) => [
            'tanggal' => $i->tanggal->toDateString(),
            'day_type' => $i->day_type,
            'kategori' => $i->kategori,
            'harga' => (int) $i->harga,
        ])->all();

        $result['payment_state'] = $charge->payment ? 'paid' : 'unpaid';
        $result['paid_at'] = $charge->payment?->paid_at?->copy()->setTimezone(config('app.timezone'))->toIso8601String();

        return $result;
    }

    /**
     * Payload final untuk satu pendaki berdasarkan waktu turun aktual.
     * Tidak menyimpan apa pun; hanya mengembalikan rincian yang siap disimpan.
     */
    public function finalize(gk_pendaki $pendaki, CarbonInterface $actualCheckoutAt): array
    {
        $pendaki->loadMissing(['booking', 'biodata', 'tarifSnapshot']);

        $rates = $this->ratesForPendaki($pendaki);
        $scheduledExitDate = $pendaki->booking->tanggal_keluar;

        if ($rates['harga_wk'] === null || $rates['harga_wd'] === null) {
            return [
                'needs_review' => true,
                'status' => 'needs_review',
                'amount' => 0,
                'source' => $rates['source'],
                'kategori' => $rates['kategori'],
                'items' => [],
            ];
        }

        $holidays = $this->holidayDates(
            Carbon::createFromFormat('Y-m-d', $scheduledExitDate, config('app.timezone'))->startOfDay()->addDay()->toDateString(),
            $actualCheckoutAt->copy()->setTimezone(config('app.timezone'))->startOfDay()->toDateString()
        );

        $computed = $this->compute(
            $scheduledExitDate,
            $actualCheckoutAt,
            (int) $rates['harga_wk'],
            (int) $rates['harga_wd'],
            $rates['kategori'],
            $holidays
        );

        return [
            'needs_review' => false,
            'status' => 'final',
            'amount' => $computed['amount'],
            'source' => $rates['source'],
            'kategori' => $rates['kategori'],
            'items' => $computed['items'],
        ];
    }

    /**
     * Ringkasan satu booking (semua pendaki + rekap tim).
     */
    public function bookingSummary(gk_booking $booking, ?CarbonInterface $now = null): array
    {
        $now = ($now ?? Carbon::now())->copy()->setTimezone(config('app.timezone'));

        $booking->loadMissing([
            'pendakis.biodata',
            'pendakis.getStatus',
            'pendakis.tarifSnapshot',
            'pendakis.overstayCharge.items',
            'pendakis.overstayCharge.payment',
            'pendakis.booking',
        ]);

        $holidayStart = Carbon::createFromFormat('Y-m-d', $booking->tanggal_keluar, config('app.timezone'))->startOfDay()->addDay()->toDateString();
        $holidayEnd = $now->copy()->startOfDay()->toDateString();
        $holidays = $holidayStart <= $holidayEnd ? $this->holidayDates($holidayStart, $holidayEnd) : [];

        $pendakis = $booking->pendakis->map(fn (gk_pendaki $p) => $this->summary($p, $now, $holidays))->values();

        $overdueActive = $pendakis->filter(fn ($p) => $p['hike_state'] === 'mendaki' && ($p['overdue_days'] ?? 0) > 0);
        $unpaidFinal = $pendakis->filter(fn ($p) => $p['charge_status'] === 'final' && $p['payment_state'] === 'unpaid');
        $estimateTotal = $pendakis->where('is_estimate', true)->sum(fn ($p) => $p['amount'] ?? 0);
        $finalTotal = $pendakis->where('is_estimate', false)->whereIn('charge_status', ['final', 'needs_review'])->sum(fn ($p) => $p['amount'] ?? 0);

        return [
            'booking_id' => $booking->id,
            'unique_code' => $booking->unique_code,
            'scheduled_exit_date' => $booking->tanggal_keluar,
            'timezone' => config('app.timezone'),
            'server_now' => $now->toIso8601String(),
            'pendakis' => $pendakis->all(),
            'team' => [
                'overdue_active_count' => $overdueActive->count(),
                'unpaid_final_count' => $unpaidFinal->count(),
                'estimate_total' => $estimateTotal,
                'final_total' => $finalTotal,
            ],
        ];
    }
}
