<?php

namespace App\Http\Controllers\etiket\admin\master;

use App\Http\Controllers\AdminController;
use App\Models\destinasi as ModelDestinasi;
use App\Models\gambar_destinasi as ModelGambarDestinasi;
use App\Models\gk_booking as ModelBooking;

use App\Http\Controllers\helper\uploadFileControlller;
use Illuminate\Http\Request;

class destinasisController extends AdminController
{
    public function index()
    {
        $destinasi = ModelDestinasi::all();
        $gambar = ModelGambarDestinasi::all();
        // return $destinasi;
        return view('etiket.admin.master.destinasi.index', [
            "destinasi" =>  $destinasi,
            "gambar" => $gambar
        ]);
    }
    public function add()
    {
        return view('etiket.admin.master.destinasi.add');
    }
    public function addAction(Request $request)
    {
        // Validasi data input
        $validatedData = $request->validate([
            'nama' => 'required|string|max:255',
            'status' => 'required|in:0,1', // Hanya menerima 0 atau 1
            'kategori' => 'required|string|max:100',
            'lokasi' => 'required|string|max:255',
            'detail' => 'required|string'
        ]);

        // Simpan data ke database dengan mass assignment
        ModelDestinasi::create([
            'nama' => $validatedData['nama'],
            'status' => $validatedData['status'],
            'kategori' => $validatedData['kategori'],
            'lokasi' => $validatedData['lokasi'],
            'detail' => $validatedData['detail'],
        ]);
        // Redirect kembali ke halaman daftar destinasi dengan pesan sukses
        return redirect()->route('admin.master.destinasi')->with('success', 'Destinasi berhasil ditambahkan!');
    }
    public function deleteAction(Request $request)
    {
        // return $request;
        $validasi = $request->validate([
            'id_destinasi' => 'required|exists:destinasis,id',
        ]);
        // ambil data destinasi
        $destinasi = ModelDestinasi::find($validasi['id_destinasi']);

        // Cek booking dari destinasi dengan menggunakan relasi
        $booking = ModelBooking::whereHas('gktiket', function ($query) use ($validasi) {
            $query->where('id_destinasi', $validasi['id_destinasi']); // pastikan nama kolom benar
        })->get();

        if ($booking->count() > 0) {
            return back()->with('error', 'Destinasi Tidak Dapat Dihapus Karena Sudah Terdapat Booking');
        }
        // hapus gambar dari destinasi
        $gambar = ModelGambarDestinasi::where('id_destinasi', $destinasi->id)->get();
        foreach ($gambar as $item) {
            $uploadController = new uploadFileControlller();
            $uploadController->delete($item->src);
            $item->delete();
        }

        // hpus data destinasi
        $destinasi->delete();

        return back()->with('success', 'Berhasil Menghapus Destinasi');
    }
}
