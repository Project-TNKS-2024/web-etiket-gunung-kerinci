<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Controllers\helper\ApiResponse;
use App\Models\destinasi;
use App\Models\Event;
use App\Models\gk_booking;
use App\Models\gk_paket_tiket;
use App\Models\gk_tiket_pendaki;
use Carbon\Carbon;

class DestinasiController extends Controller
{
    private function gateCapacity($idDestinasi, $startDate, $endDate = null, $idGate = null): array
    {
        $endDate = $endDate ?? Carbon::parse($startDate)->addMonths(2)->format('Y-m-d');

        $bookings = gk_booking::with([
            'gktiket:id,id_destinasi,nama,min_pendaki,penugasan,keterangan',
            'gateMasuk:id,nama,status,id_destinasi,max_pendaki_hari,min_pendaki_booking,lokasi,lokasi_maps,detail',
        ])
            ->whereHas('gktiket', function ($query) use ($idDestinasi) {
                $query->where('id_destinasi', $idDestinasi);
            })
            ->where('status_booking', '>=', 4)
            ->where(function ($query) use ($startDate, $endDate) {
                $query->whereBetween('tanggal_masuk', [$startDate, $endDate])
                    ->orWhere(function ($query) use ($startDate, $endDate) {
                        $query->where('tanggal_masuk', '<=', $startDate)
                            ->where('tanggal_keluar', '>=', $startDate);
                    });
            });

        if (!is_null($idGate)) {
            $bookings->where('gate_masuk', $idGate);
        }

        $result = [];

        foreach ($bookings->get() as $booking) {
            $currentDate = Carbon::parse($booking->tanggal_masuk);
            $exitDate = Carbon::parse($booking->tanggal_keluar);
            $gateMasuk = $booking->gateMasuk;
            $gateId = $gateMasuk->id ?? null;

            if ($gateId === null) {
                continue;
            }

            while ($currentDate <= $exitDate) {
                $dateStr = $currentDate->format('Y-m-d');

                if (!isset($result[$dateStr][$gateId])) {
                    $result[$dateStr][$gateId] = [
                        'tanggal' => $dateStr,
                        'id_gate' => $gateId,
                        'gate_masuk' => $gateMasuk,
                        'jumlah_pendaki' => 0,
                    ];
                }

                $result[$dateStr][$gateId]['jumlah_pendaki'] += ($booking->total_pendaki_wni + $booking->total_pendaki_wna);
                $currentDate->addDay();
            }
        }

        return collect($result)->flatMap(fn ($dates) => array_values($dates))->values()->all();
    }

    /**
     * Mengambil daftar destinasi gunung yang tersedia untuk booking mobile.
     */
    public function index()
    {
        $destinasi = destinasi::select('id', 'nama', 'status', 'statusGunung', 'kategori', 'lokasi', 'detail', 'sop')
            ->where('status', 2)
            ->where('kategori', 'gunung')
            ->with([
                'gambar_destinasi:id,id_destinasi,src,nama,detail',
                'gates' => function ($query) {
                    $query->select('id', 'nama', 'status', 'id_destinasi', 'max_pendaki_hari', 'min_pendaki_booking', 'lokasi', 'lokasi_maps', 'detail')
                        ->where('status', true);
                },
            ])
            ->get()
            ->map(function ($item) {
                $item->status_label = $item->getStatus();
                $item->status_gunung_label = $item->getStatusGunung();

                return $item;
            });

        return ApiResponse::success($destinasi, 'Berhasil mengambil daftar destinasi', 200);
    }

    /**
     * Mengambil detail destinasi untuk kebutuhan mobile.
     */
    public function show($id)
    {
        $destinasi = destinasi::select('id', 'nama', 'status', 'statusGunung', 'kategori', 'lokasi', 'detail', 'sop')
            ->where('status', 2)
            ->where('kategori', 'gunung')
            ->with([
                'gambar_destinasi:id,id_destinasi,src,nama,detail',
                'gates' => function ($query) {
                    $query->select('id', 'nama', 'status', 'id_destinasi', 'max_pendaki_hari', 'min_pendaki_booking', 'lokasi', 'lokasi_maps', 'detail')
                        ->where('status', true);
                },
            ])
            ->where('id', $id)
            ->first();

        if (!$destinasi) {
            return ApiResponse::error('Destinasi tidak ditemukan', null, 404);
        }

        $destinasi->status_label = $destinasi->getStatus();
        $destinasi->status_gunung_label = $destinasi->getStatusGunung();

        return ApiResponse::success($destinasi, 'Berhasil mengambil detail destinasi', 200);
    }

    /**
     * Mengambil paket tiket berdasarkan destinasi untuk tahap awal booking mobile.
     */
    public function paket($id)
    {
        $destinasi = destinasi::select('id', 'nama', 'status', 'statusGunung', 'kategori', 'lokasi', 'detail', 'sop')
            ->where('status', 2)
            ->where('kategori', 'gunung')
            ->with('gambar_destinasi:id,id_destinasi,src,nama,detail')
            ->where('id', $id)
            ->first();

        if (!$destinasi) {
            return ApiResponse::error('Destinasi tidak ditemukan', null, 404);
        }

        $paket = gk_paket_tiket::select('id', 'id_destinasi', 'nama', 'min_pendaki', 'penugasan', 'keterangan')
            ->where('id_destinasi', $id)
            ->with('tiket_pendaki:id,id_paket_tiket,kategori_pendaki,harga_masuk_wk,harga_masuk_wd,harga_kemah,harga_traking,harga_ansuransi,masa_ansuransi')
            ->get();

        return ApiResponse::success([
            'destinasi' => $destinasi,
            'paket' => $paket,
        ], 'Berhasil mengambil paket tiket destinasi', 200);
    }

    /**
     * Mengambil detail tiket paket, gate, event, dan kapasitas untuk pemilihan tiket mobile.
     */
    public function tiket($id)
    {
        $paket = gk_paket_tiket::select('id', 'id_destinasi', 'nama', 'min_pendaki', 'penugasan', 'keterangan')
            ->with([
                'destinasi:id,nama,status,statusGunung,kategori,lokasi,detail,sop',
                'destinasi.gambar_destinasi:id,id_destinasi,src,nama,detail',
                'destinasi.gates' => function ($query) {
                    $query->select('id', 'nama', 'status', 'id_destinasi', 'max_pendaki_hari', 'min_pendaki_booking', 'lokasi', 'lokasi_maps', 'detail')
                        ->where('status', true);
                },
                'tiket_pendaki:id,id_paket_tiket,kategori_pendaki,harga_masuk_wk,harga_masuk_wd,harga_kemah,harga_traking,harga_ansuransi,masa_ansuransi',
            ])->where('id', $id)->first();

        if (!$paket || !$paket->destinasi || $paket->destinasi->status != 2 || $paket->destinasi->kategori !== 'gunung') {
            return ApiResponse::error('Paket tiket tidak ditemukan', null, 404);
        }

        $now = Carbon::now();
        $next = $now->copy()->addMonth();
        $events = Event::whereBetween('tanggal', [
            $now->format('Y-m-01'),
            $next->format('Y-m-t'),
        ])->select('id', 'judul', 'tanggal', 'libur')->get();

        $tiket = gk_tiket_pendaki::select('id', 'id_paket_tiket', 'kategori_pendaki', 'harga_masuk_wk', 'harga_masuk_wd', 'harga_kemah', 'harga_traking', 'harga_ansuransi', 'masa_ansuransi')
            ->where('id_paket_tiket', $id)
            ->get();

        return ApiResponse::success([
            'paket' => $paket,
            'destinasi' => $paket->destinasi,
            'tiket' => $tiket,
            'booking_bulanan' => $this->gateCapacity($paket->id_destinasi, now()->format('Y-m-d')),
            'events' => $events,
        ], 'Berhasil mengambil detail tiket paket destinasi', 200);
    }
}
