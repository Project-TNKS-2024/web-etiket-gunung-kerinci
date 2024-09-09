<?php

namespace App\Http\Controllers\etiket\admin\destinasis;

use App\Http\Controllers\Controller;
use App\Http\Controllers\helper\uploadFileControlller;
use Illuminate\Http\Request;
use App\Models\destinasi;
use App\Models\gk_gates;
use App\Models\gambar_destinasi;
use Illuminate\Support\Facades\DB;
use Exception;
use Illuminate\Support\Facades\Log;


class destinasis extends Controller
{
    //

    public function daftar()
    {

        $data = Destinasi::get();
        // return $data;
        $gambar = gambar_destinasi::with(['destinasi'])->get();

        return view('etiket.admin.master-data.destinasi', [
            "destinasi" => $data,
            "gambar" => $gambar,
        ]);
    }

    public function tambah()
    {
        return view('etiket.admin.master-data.destinasi.tambah');
    }

    public function tambahAction(Request $request)
    {
        $request->validate([
            'nama' => 'required',
            'status' => 'required',
            'kategori' => 'required',
            'lokasi' => 'required',
            'detail' => 'required',
        ]);
        // return $request;

        $proceed = destinasi::create([
            'nama' => $request->nama,
            'status' => $request->status,
            'kategori' => $request->kategori,
            'lokasi' => $request->lokasi,
            'detail' => $request->detail,

        ]);

        if (!$proceed) {
            return back()->with('error', 'Terjadi kesalahan ketika menambahkan destinasi');
        }


        return back()->with('success', 'Berhasil menambah destinasi');
    }

    public function edit($id)
    {
        $data = Destinasi::where('id', $id)->first();
        $gates = gk_gates::with(['destinasi'])->where('gk_gates.id_destinasi', $id)->get();
        $gambar = gambar_destinasi::with(['destinasi'])->where('id_destinasi', $id)->get();

        return view('etiket.admin.master-data.destinasi.edit', [
            'data' => $data,
            'gates' => $gates,
            'gambar' => $gambar,
        ]);
    }

    public function editAction(Request $request, $id)
    {
        $request->validate([
            'nama' => 'required',
            'status' => 'required',
            'kategori' => 'required',
            'lokasi' => 'required',
            'detail' => 'required',
            'status' => 'required',
        ]);

        // return $request;

        if (!destinasi::where('id', $id)->update([
            'nama' => $request->nama,
            'status' => $request->status,
            'kategori' => $request->kategori,
            'lokasi' => $request->lokasi,
            'detail' => $request->detail,
        ])) {
            return back()->withErrors(['database', 'Terjadi kesalahan saat mengubah destinasi']);
        }

        return back()->with('success', 'Berhasil memperbarui tiket');
    }

    public function hapus(Request $reqeust, $id)
    {
        destinasi::where('id', $id)->delete();
        return back()->with('success', 'Berhasil Menghapus Destinasi');
    }

    public function upload(Request $request, $id)
    {
        // Validate the request
        $request->validate([
            'foto' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048', // Added max size constraint
            'foto_nama' => 'required|string|max:255', // Added max length constraint
            'foto_detail' => 'required|string|max:500', // Added max length constraint
        ]);

        try {
            if ($request->hasFile('foto')) {
                $file = $request->file('foto');


                $uploadController = new uploadFileControlller();
                $fileUrl = $uploadController->create('img', 'destinasi', $file);

                gambar_destinasi::create([
                    "src" => $fileUrl,
                    "nama" => $request->input('foto_nama'),
                    "detail" => $request->input('foto_detail'),
                    "id_destinasi" => $id,
                ]);

                return back()->with('success', 'Berhasil mengupload gambar.');
            } else {
                return back()->withErrors(['foto' => 'File tidak ditemukan.']);
            }
        } catch (Exception $e) {
            Log::error('Error uploading file: ' . $e->getMessage());
            return back()->withErrors(['database' => 'Terjadi kesalahan saat mengupload gambar: ' . $e->getMessage()]);
        }
    }
}
