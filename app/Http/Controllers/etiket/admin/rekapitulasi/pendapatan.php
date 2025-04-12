<?php

namespace App\Http\Controllers\etiket\admin\rekapitulasi;

use App\Http\Controllers\Controller;
use App\Models\destinasi;
use App\Models\gk_booking;
use Carbon\Carbon;
use Illuminate\Http\Request;

class pendapatan extends Controller
{
    public function index(Request $request)
    {
        // Ambil filter destinasi dari request
        $filter_destinasi = $request->d;

        // Tentukan rentang tanggal awal
        $rentang_awal = $request->rs
            ? Carbon::parse($request->rs)->startOfDay()
            : Carbon::now()->subYear()->startOfDay();

        // Tentukan rentang tanggal akhir
        $rentang_akhir = $request->ra
            ? Carbon::parse($request->ra)->endOfDay()
            : Carbon::now()->endOfDay();

        // Query utama
        $bigData = gk_booking::with(['destinasi', 'pembayaran'])
            ->withMax('pembayaran', 'created_at')
            ->where('status_booking', '>=', 4)
            ->when($filter_destinasi, function ($query) use ($filter_destinasi) {
                $query->whereHas('destinasi', function ($q) use ($filter_destinasi) {
                    $q->where('destinasis.id', $filter_destinasi);
                });
            })
            ->whereHas('pembayaran', function ($query) use ($rentang_awal, $rentang_akhir) {
                $query->whereBetween('updated_at', [$rentang_awal, $rentang_akhir]);
            })
            ->orderByDesc('pembayaran_max_created_at')
            ->get();

        $total = $bigData->sum('total_pembayaran');
        $daftar_destinasi = destinasi::all();

        // return response()->json($bigData);

        return view('etiket.admin.rekapitulasi.pendapatan', [
            'data' => $bigData,
            'total' => $total,
            'daftar_destinasi' => $daftar_destinasi,
            'filter_destinasi' => $filter_destinasi,
            'rentang_awal' => $rentang_awal,
            'rentang_akhir' => $rentang_akhir,
        ]);
    }
    public function download(Request $request)
    {
        $filter_destinasi = $request->d;
        $rentang_awal = $request->rs
            ? Carbon::parse($request->rs)->startOfDay()
            : Carbon::now()->subYear()->startOfDay();
        $rentang_akhir = $request->ra
            ? Carbon::parse($request->ra)->endOfDay()
            : Carbon::now()->endOfDay();

        $data = gk_booking::with(['destinasi', 'pembayaran'])
            ->where('status_booking', '>=', 4)
            ->when($filter_destinasi, function ($query) use ($filter_destinasi) {
                $query->whereHas('destinasi', function ($q) use ($filter_destinasi) {
                    $q->where('destinasis.id', $filter_destinasi);
                });
            })
            ->whereHas('pembayaran', function ($query) use ($rentang_awal, $rentang_akhir) {
                $query->whereBetween('updated_at', [$rentang_awal, $rentang_akhir]);
            })
            ->get();

        $filename = 'Rekap_Pendapatan_' . now()->format('Ymd_His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function () use ($data) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['No', 'Id Booking', 'Destinasi', 'Tanggal Pembayaran', 'Tanggal Pendakian', 'Nominal', 'Validator']);

            foreach ($data as $index => $d) {
                fputcsv($file, [
                    $index + 1,
                    $d->id,
                    $d->destinasi->nama ?? '-',
                    optional($d->pembayaran->last())->created_at,
                    $d->tanggal_masuk,
                    $d->total_pembayaran,
                    optional($d->pembayaran->last())->validator,
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
