<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Controllers\helper\ApiResponse;
use App\Models\destinasi;

class DestinasiController extends Controller
{
    /**
     * Mengambil daftar destinasi gunung yang tersedia untuk booking mobile.
     */
    public function index()
    {
        $destinasi = destinasi::where('status', 2)
            ->where('kategori', 'gunung')
            ->with([
                'gambar_destinasi',
                'gates' => function ($query) {
                    $query->where('status', true);
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
        $destinasi = destinasi::where('status', 2)
            ->where('kategori', 'gunung')
            ->with([
                'gambar_destinasi',
                'gates' => function ($query) {
                    $query->where('status', true);
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
}
