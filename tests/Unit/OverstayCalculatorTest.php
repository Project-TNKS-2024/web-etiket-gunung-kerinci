<?php

namespace Tests\Unit;

use App\Services\OverstayCalculator;
use Carbon\Carbon;
use PHPUnit\Framework\TestCase;

class OverstayCalculatorTest extends TestCase
{
    private const TZ = 'Asia/Jakarta';

    // 2026-10-16 = Jumat, 17 = Sabtu, 18 = Minggu, 19 = Senin, 20 = Selasa, 21 = Rabu.
    private const WK = 50_000;
    private const WD = 40_000;

    private function calc(string $exitDate, string $effectiveEnd, array $holidays = [], string $kategori = 'wni'): array
    {
        return (new OverstayCalculator)->compute(
            $exitDate,
            Carbon::parse($effectiveEnd, self::TZ),
            self::WK,
            self::WD,
            $kategori,
            $holidays
        );
    }

    public function test_ticket_valid_until_exit_date_last_second(): void
    {
        $r = $this->calc('2026-10-19', '2026-10-19 23:59:59');
        $this->assertSame(0, $r['extra_days']);
        $this->assertSame(0, $r['amount']);
        $this->assertEmpty($r['items']);
    }

    public function test_midnight_after_exit_is_one_extra_day(): void
    {
        $r = $this->calc('2026-10-19', '2026-10-20 00:00:00');
        $this->assertSame(1, $r['extra_days']);
        $this->assertSame(self::WD, $r['amount']); // Selasa = weekday
        $this->assertCount(1, $r['items']);
        $this->assertSame('2026-10-20', $r['items'][0]['tanggal']);
        $this->assertSame('weekday', $r['items'][0]['day_type']);
    }

    public function test_descend_morning_after_exit_still_one_day(): void
    {
        $r = $this->calc('2026-10-19', '2026-10-20 06:00:00');
        $this->assertSame(1, $r['extra_days']);
        $this->assertSame(self::WD, $r['amount']);
    }

    public function test_descend_two_days_later_is_two_days(): void
    {
        $r = $this->calc('2026-10-19', '2026-10-21 00:00:00');
        $this->assertSame(2, $r['extra_days']);
        $this->assertSame(self::WD * 2, $r['amount']); // Selasa + Rabu = weekday
        $this->assertCount(2, $r['items']);
    }

    public function test_backdated_checkout_on_exit_day_is_zero(): void
    {
        $r = $this->calc('2026-10-19', '2026-10-19 23:30:00');
        $this->assertSame(0, $r['extra_days']);
        $this->assertSame(0, $r['amount']);
    }

    public function test_holiday_on_weekend_counts_once(): void
    {
        // 17 Okt = Sabtu dan juga libur -> satu baris tarif weekend/libur, bukan dua.
        $r = $this->calc('2026-10-16', '2026-10-17 12:00:00', ['2026-10-17']);
        $this->assertSame(1, $r['extra_days']);
        $this->assertCount(1, $r['items']);
        $this->assertSame('holiday', $r['items'][0]['day_type']);
        $this->assertSame(self::WK, $r['items'][0]['harga']);
        $this->assertSame(self::WK, $r['amount']);
    }

    public function test_mixed_weekday_and_weekend_use_each_rate(): void
    {
        // 17 Sabtu, 18 Minggu, 19 Senin.
        $r = $this->calc('2026-10-16', '2026-10-19 08:00:00');
        $this->assertSame(3, $r['extra_days']);
        $this->assertCount(3, $r['items']);

        $byType = collect($r['items'])->groupBy('day_type');
        $this->assertCount(2, $byType['weekend']);
        $this->assertCount(1, $byType['weekday']);

        $this->assertSame(self::WK * 2 + self::WD, $r['amount']);
    }

    public function test_category_is_carried_to_items(): void
    {
        $r = $this->calc('2026-10-19', '2026-10-20 00:00:00', [], 'wna');
        $this->assertSame('wna', $r['items'][0]['kategori']);
    }

    public function test_effective_end_before_exit_is_zero(): void
    {
        $r = $this->calc('2026-10-19', '2026-10-18 23:59:59');
        $this->assertSame(0, $r['extra_days']);
        $this->assertSame(0, $r['amount']);
    }
}
