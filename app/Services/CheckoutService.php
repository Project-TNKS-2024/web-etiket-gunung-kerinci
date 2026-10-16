<?php

namespace App\Services;

use App\Models\gk_booking;
use App\Models\gk_pendaki;
use App\Models\GkOverstayAudit;
use App\Models\GkOverstayCharge;
use App\Models\GkOverstayChargeItem;
use App\Models\statusPendaki;
use App\Models\User;
use Carbon\Carbon;
use Carbon\CarbonInterface;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

/**
 * Mencatat kepulangan aktual per pendaki secara atomik dan membekukan
 * tagihan biaya tambahan final. Kepulangan disimpan meski perhitungan tarif
 * bermasalah (status biaya "needs_review"), sesuai aturan keselamatan.
 */
class CheckoutService
{
    public function __construct(private OverstayCalculator $calculator)
    {
    }

    /**
     * @param array<int, array{pendaki_id:string, actual_checkout_at:CarbonInterface|null, reason:string|null}> $items
     * @return array<int, array>
     */
    public function checkout(gk_booking $booking, array $items, User $admin, ?CarbonInterface $now = null): array
    {
        $now = ($now ?? Carbon::now())->setTimezone(OverstayCalculator::TZ);

        return DB::transaction(function () use ($booking, $items, $admin, $now) {
            $booking = gk_booking::lockForUpdate()->findOrFail($booking->id);

            $results = [];
            foreach ($items as $item) {
                $results[] = $this->checkoutOne($booking, $item, $admin, $now);
            }

            $booking->refresh()->load('pendakis.getStatus');
            $allDone = $booking->pendakis->every(function ($p) {
                return in_array($p->getStatus->last()?->status, [1, 3], true);
            });

            // Tetap 6 bila masih ada pendaki aktif; 7 bila semua keluar/batal.
            $booking->update(['status_booking' => $allDone ? 7 : 6]);

            return $results;
        });
    }

    private function checkoutOne(gk_booking $booking, array $item, User $admin, CarbonInterface $now): array
    {
        $pendaki = gk_pendaki::where('booking_id', $booking->id)
            ->lockForUpdate()
            ->find($item['pendaki_id']);

        if (!$pendaki) {
            throw new InvalidArgumentException('Pendaki tidak ditemukan dalam booking ini');
        }

        $state = $this->calculator->hikeState($pendaki);
        if ($state !== 'mendaki') {
            throw new InvalidArgumentException(sprintf(
                'Pendaki %s tidak sedang mendaki',
                trim(($pendaki->biodata->first_name ?? '') . ' ' . ($pendaki->biodata->last_name ?? ''))
            ));
        }

        $actual = ($item['actual_checkout_at'] ?? $now)->copy()->setTimezone(OverstayCalculator::TZ);
        $reason = $item['reason'] ?? null;

        // Waktu turun tidak boleh di masa depan (toleransi kecil).
        if ($actual->greaterThan($now->copy()->addMinutes(5))) {
            throw new InvalidArgumentException('Waktu turun tidak boleh di masa depan');
        }

        // Waktu turun tidak boleh sebelum check-in yang valid.
        if ($pendaki->checkin_at && $actual->lessThan($pendaki->checkin_at->copy()->subMinute())) {
            throw new InvalidArgumentException('Waktu turun tidak boleh sebelum waktu check-in');
        }

        // Alasan wajib bila mundur dari waktu sekarang.
        if ($actual->lessThan($now->copy()->subMinutes(2)) && trim((string) $reason) === '') {
            throw new InvalidArgumentException('Alasan wajib diisi bila waktu turun diubah dari waktu sekarang');
        }

        $pendaki->actual_checkout_at = $actual;
        $pendaki->checkout_recorded_at = $now;
        $pendaki->checkout_recorded_by = $admin->id;
        $pendaki->checkout_reason = $reason ?: null;
        $pendaki->save();

        statusPendaki::create([
            'id_pendaki' => $pendaki->id,
            'status' => 3,
            'detail' => 'Melakukan pendakian',
            'validator' => $admin->id,
        ]);

        $charge = $this->finalizeCharge($booking, $pendaki, $actual, $admin, $now);

        GkOverstayAudit::create([
            'id_booking' => $booking->id,
            'id_pendaki' => $pendaki->id,
            'action' => 'checkout',
            'detail' => [
                'actual_checkout_at' => $actual->toIso8601String(),
                'reason' => $reason ?: null,
                'charge_id' => $charge?->id,
                'charge_status' => $charge?->status,
                'charge_total' => $charge?->total,
            ],
            'performed_by' => $admin->id,
        ]);

        return [
            'pendaki_id' => $pendaki->id,
            'full_name' => trim(($pendaki->biodata->first_name ?? '') . ' ' . ($pendaki->biodata->last_name ?? '')),
            'actual_checkout_at' => $actual->toIso8601String(),
            'charge_status' => $charge?->status,
            'amount' => $charge?->total,
        ];
    }

    private function finalizeCharge(gk_booking $booking, gk_pendaki $pendaki, CarbonInterface $actual, User $admin, CarbonInterface $now): ?GkOverstayCharge
    {
        // Idempoten: tagihan final yang sudah ada tidak dibuat ulang.
        if ($pendaki->overstayCharge) {
            return $pendaki->overstayCharge;
        }

        $payload = $this->calculator->finalize($pendaki, $actual);

        if (!$payload['needs_review'] && $payload['amount'] <= 0) {
            return null; // Tepat waktu, tidak ada tagihan.
        }

        $charge = GkOverstayCharge::create([
            'id_booking' => $booking->id,
            'id_pendaki' => $pendaki->id,
            'status' => $payload['status'],
            'total' => $payload['amount'],
            'snapshot' => [
                'kategori' => $payload['kategori'],
                'source' => $payload['source'],
                'scheduled_exit_date' => $booking->tanggal_keluar,
                'actual_checkout_at' => $actual->toIso8601String(),
            ],
            'finalized_at' => $now,
            'finalized_by' => $admin->id,
            'version' => 1,
        ]);

        foreach ($payload['items'] as $item) {
            GkOverstayChargeItem::create([
                'id_overstay_charge' => $charge->id,
                'tanggal' => $item['tanggal'],
                'day_type' => $item['day_type'],
                'kategori' => $item['kategori'],
                'harga' => $item['harga'],
                'source' => $payload['source'],
            ]);
        }

        return $charge;
    }
}
