<?php

namespace App\Services;

use App\Models\gk_booking;
use App\Models\GkOverstayAudit;
use App\Models\GkOverstayCharge;
use App\Models\GkOverstayPayment;
use App\Models\User;
use Carbon\CarbonInterface;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

/**
 * Mencatat pembayaran biaya tambahan di pos. Nominal selalu berasal dari
 * tagihan server (tidak ditulis ulang client); pengiriman ulang tidak membuat
 * pembayaran ganda (unique id_overstay_charge + reference).
 */
class AdditionalChargePaymentService
{
    public function record(
        gk_booking $booking,
        GkOverstayCharge $charge,
        string $method,
        User $admin,
        ?string $reference = null,
        ?string $note = null,
        ?CarbonInterface $paidAt = null
    ): GkOverstayPayment {
        return DB::transaction(function () use ($booking, $charge, $method, $admin, $reference, $note, $paidAt) {
            $charge = GkOverstayCharge::lockForUpdate()->findOrFail($charge->id);

            if ($charge->id_booking !== $booking->id) {
                throw new InvalidArgumentException('Tagihan tidak sesuai dengan booking');
            }

            if ($charge->status === 'needs_review') {
                throw new InvalidArgumentException('Biaya belum dapat dihitung, pembayaran belum bisa dicatat');
            }

            if ($charge->payment) {
                throw new InvalidArgumentException('Tagihan ini sudah dibayar');
            }

            $paidAt = $paidAt ?? now();

            $payment = GkOverstayPayment::create([
                'id_booking' => $booking->id,
                'id_overstay_charge' => $charge->id,
                'amount' => $charge->total,
                'method' => $method,
                'paid_at' => $paidAt,
                'recorded_by' => $admin->id,
                'reference' => $reference,
                'note' => $note,
            ]);

            GkOverstayAudit::create([
                'id_booking' => $booking->id,
                'id_pendaki' => $charge->id_pendaki,
                'action' => 'payment',
                'detail' => [
                    'charge_id' => $charge->id,
                    'amount' => $charge->total,
                    'method' => $method,
                    'reference' => $reference,
                ],
                'performed_by' => $admin->id,
            ]);

            return $payment;
        });
    }
}
