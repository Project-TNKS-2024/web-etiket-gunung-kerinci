<?php

namespace App\Http\Controllers\etiket\admin;

use App\Http\Controllers\AdminController;
use App\Models\destinasi;
use App\Models\gk_booking;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class dashboard extends AdminController
{
    public function index()
    {
        $listDestinasi = destinasi::with('gambar_destinasi')->get();
        $dataBookingOverview = $this->dataBookingOverview($listDestinasi);
        $dataYearlyBreakup = $this->dataYearlyBreakup();
        $dataMonthlyEarnings = $this->dataMonthlyEarnings();

        // return $dataMonthlyEarnings;

        return view('etiket.admin.dashboard', [
            'listDestinasi' => $listDestinasi,
            'dataGravik' => $dataBookingOverview,
            'dataBreakup' => $dataYearlyBreakup,
            'dataEarning' => $dataMonthlyEarnings
        ]);
    }
    // Booking Overview Data
    private function dataBookingOverview($listDestinasi)
    {
        // Cari bulan terkecil dan terbesar berdasarkan data booking
        $minMonth = gk_booking::min('tanggal_masuk');
        $maxMonth = now()->format('Y-m-01'); // Bulan saat ini (format YYYY-MM-DD)

        // Pastikan minMonth tidak null
        $startDate = $minMonth ? Carbon::parse($minMonth)->startOfMonth() : now()->startOfYear();
        $endDate = Carbon::parse($maxMonth)->startOfMonth();

        // Buat daftar bulan antara minMonth sampai bulan saat ini
        $months = collect();
        while ($startDate <= $endDate) {
            $months[$startDate->format('n')] = $startDate->format('F/Y');
            $startDate->addMonth();
        }

        // Ambil data booking dan kelompokkan berdasarkan tiket dan bulan
        $rawData = gk_booking::selectRaw('id_tiket, MONTH(tanggal_masuk) as bulan, COUNT(*) as jumlah_pendaki')
            ->whereBetween('tanggal_masuk', [$minMonth, $maxMonth])
            ->where('status_booking', '>', 3)
            ->groupBy('id_tiket', 'bulan')
            ->get()
            ->groupBy('id_tiket');

        // Format data untuk chart
        $dataGravik = [
            'series' => [],
            'categories' => $months->values()->toArray()
        ];

        foreach ($listDestinasi as $destinasi) {
            $dataDestinasi = isset($rawData[$destinasi->id]) ? $rawData[$destinasi->id]->keyBy('bulan') : collect();

            $data = $months->map(function ($label, $bulan) use ($dataDestinasi) {
                return $dataDestinasi->has($bulan)
                    ? $dataDestinasi[$bulan]->jumlah_pendaki
                    : 0;
            })->values()->toArray();

            $dataGravik['series'][] = [
                'name' => $destinasi->nama,
                'data' => $data
            ];
        }

        return $dataGravik;
    }

    // Yearly Breakup Data
    private function dataYearlyBreakup()
    {
        $currentYear = now()->year;
        $years = [$currentYear, $currentYear - 1, $currentYear - 2];

        $earnings = collect($years)->mapWithKeys(function ($year) {
            return [$year => (float) gk_booking::where('status_booking', '>', 3)
                ->whereYear('tanggal_masuk', $year)->sum('total_pembayaran')];
        });

        // return $earnings;

        $currentEarnings = $earnings[$currentYear];
        $lastYearEarnings = $earnings[$currentYear - 1];

        $growthPercentage = $lastYearEarnings > 0
            ? round((($currentEarnings - $lastYearEarnings) / $lastYearEarnings) * 100, 2)
            : 0;

        return [
            'total' => number_format($currentEarnings, 0, ',', '.'), // Format ke mata uang
            'growth' => $growthPercentage,
            'year' => $currentYear,
            'lastYear' => $currentYear - 1,
            'series' => $earnings->values()->toArray(),  // Data untuk donut chart
            'labels' => $years // Label untuk tahun
        ];
    }


    //  Monthly Earnings Data
    private function dataMonthlyEarnings()
    {
        $currentYear = now()->year;
        $currentMonth = now()->month;
        $lastMonth = now()->subMonth()->month;

        // Ambil pendapatan bulan ini
        $monthlyEarnings = gk_booking::whereYear('tanggal_masuk', $currentYear)
            ->whereMonth('tanggal_masuk', $currentMonth)
            ->where('status_booking', '>', 3)
            ->sum('total_pembayaran');

        // Ambil pendapatan bulan lalu
        $lastMonthEarnings = gk_booking::whereYear('tanggal_masuk', $currentYear)
            ->whereMonth('tanggal_masuk', $lastMonth)
            ->where('status_booking', '>', 3)
            ->sum('total_pembayaran');

        // Hitung pertumbuhan
        $monthlyGrowth = $lastMonthEarnings > 0
            ? round((($monthlyEarnings - $lastMonthEarnings) / $lastMonthEarnings) * 100, 2)
            : 0;

        // Simpan history earnings untuk grafik
        $rawData  = gk_booking::selectRaw('MONTH(tanggal_masuk) as bulan, SUM(total_pembayaran) as total')
            ->whereYear('tanggal_masuk', $currentYear)
            ->whereIn(DB::raw('MONTH(tanggal_masuk)'), [$lastMonth, $currentMonth])
            ->groupBy('bulan')
            ->orderBy('bulan')
            ->get()
            ->pluck('total', 'bulan') // ambil total dengan key = bulan
            ->toArray();

        $monthlyData = [
            $currentMonth => $rawData[$currentMonth] ?? 0,
            $lastMonth => $rawData[$lastMonth] ?? 0,
        ];

        // return $monthlyData;

        return [
            'total' => number_format($monthlyEarnings, 0, ',', '.'),
            'growth' => $monthlyGrowth,
            'month' => now()->format('F Y'),
            'lastMonth' => now()->subMonth()->format('F Y'),
            'series' => $monthlyData ?: [0], // Jika kosong, beri data default
        ];
    }
}
