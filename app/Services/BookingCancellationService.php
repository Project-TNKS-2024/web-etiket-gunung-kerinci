<?php

namespace App\Services;

use App\Models\gk_booking;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class BookingCancellationService
{
    public function cancel(gk_booking $booking, User $actor, string $reason): gk_booking
    {
        return DB::transaction(function () use ($booking, $actor, $reason) {
            $booking = gk_booking::lockForUpdate()->findOrFail($booking->id);
            if ((int) $booking->status_booking === gk_booking::STATUS_CANCELLED) {
                return $booking;
            }
            if (!in_array((int) $booking->status_booking, [0, 1, 2, 3], true)) {
                throw ValidationException::withMessages(['booking_id' => 'Hanya pesanan sebelum konfirmasi pembayaran yang dapat dibatalkan.']);
            }
            $booking->update([
                'status_booking' => gk_booking::STATUS_CANCELLED,
                'cancelled_at' => now(),
                'cancelled_by' => $actor->id,
                'cancellation_reason' => trim($reason),
            ]);
            return $booking;
        });
    }
}
