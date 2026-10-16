<?php

namespace App\Enums;

/**
 * Fase perjalanan per pendaki pada satu booking.
 *
 * Nilai serialized harus persis "naik"/"turun" karena disimpan di kolom string
 * database dan dikirim ke mobile apa adanya. Jangan menyediakan input bebas yang
 * mengubah fase: transisi hanya dilakukan service domain.
 */
enum TrackingPhase: string
{
    case Naik = 'naik';
    case Turun = 'turun';

    /**
     * Parse nilai dari database/mobile menjadi enum. Null/blank/kosong => Naik.
     * Nilai tak dikenal dikembalikan null agar pemanggil bisa menolak secara jelas.
     */
    public static function tryFromString(?string $value): ?self
    {
        if ($value === null || $value === '') {
            return self::Naik;
        }

        return self::tryFrom($value);
    }
}
