<?php

namespace App\Http\Controllers\etiket\admin\rekapitulasi;

use App\Http\Controllers\Controller;
use App\Models\destinasi;
use App\Models\gk_booking;
use Carbon\Carbon;
use Illuminate\Http\Request;

class pengunjung extends Controller
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
      $bigData = gk_booking::with(['destinasi'])
         ->where('status_booking', '>=', 4)
         ->when($filter_destinasi, function ($query) use ($filter_destinasi) {
            $query->whereHas('destinasi', function ($q) use ($filter_destinasi) {
               $q->where('destinasis.id', $filter_destinasi);
            });
         })
         ->whereBetween('tanggal_masuk', [$rentang_awal, $rentang_akhir])
         ->orderByDesc('tanggal_masuk')
         ->get();

         // status booking 4 dan rieayar pedakian cekin
         

      $total_wni = $bigData->sum('total_pendaki_wni');
      $total_wna = $bigData->sum('total_pendaki_wna');
      $total_kunjugan = $total_wni + $total_wna;
      $daftar_destinasi = destinasi::all();

      return view('etiket.admin.rekapitulasi.pengunjung', [
         'data' => $bigData,
         'total_kunjugan' => $total_kunjugan,
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

      $filename = 'Rekap_Pengunjung_' . now()->format('Ymd_His') . '.csv';

      $headers = [
         'Content-Type' => 'text/csv',
         'Content-Disposition' => "attachment; filename=\"$filename\"",
      ];

      $callback = function () use ($data) {
         $file = fopen('php://output', 'w');
         fputcsv($file, ['No', 'ID Booking', 'Destinasi', 'Tanggal Pendakian', 'WNI', 'WNA', 'Validator']);

         foreach ($data as $index => $d) {
            fputcsv($file, [
               $index + 1,
               $d->id,
               $d->destinasi->nama ?? '-',
               $d->tanggal_masuk,
               $d->total_pendaki_wni,
               $d->total_pendaki_wna,
               optional($d->pembayaran->last())->validator ?? '-',
            ]);
         }

         fclose($file);
      };

      return response()->stream($callback, 200, $headers);
   }
}
