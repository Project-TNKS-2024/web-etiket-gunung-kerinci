<?php

namespace App\Http\Controllers\etiket\admin;

use App\Http\Controllers\Controller;
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
        $destinasi = Destinasi::all();
        $gates = gk_gates::all();
        $jenisTiket = ['Weekday', 'Weekend'];

        return view('etiket.admin.master-data.destinasi.tambah', [
            "destinasi" => $destinasi,
            "gate" => $gates,
            "jenisTiket" => $jenisTiket,
        ]);
    }

    public function tambahAction(Request $request)
    {
        $request->validate([
            'nama' => 'required',
            'detail' => 'required',
        ]);

        $proceed = destinasi::create([
            "nama" => $request->nama,
            "detail" => $request->detail,
            "status" => 1,
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
            'detail' => 'required',
            'status' => 'required',
        ]);

        if (!destinasi::where('id', $id)->update([
            "nama" => $request->nama,
            "detail" => $request->detail,
            "status" => $request->status
        ])) {
            return back()->withErrors(['database', 'Terjadi kesalahan saat mengubah destinasi']);
        }

        return back()->with('success', 'Berhasil memperbarui tiket');
    }

    public function hapus(Request $reqeust, $id)
    {
        destinasi::where('id', $id)->delete();
        return back()->with('success', 'Berhasil Menghapus Tiket');
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
                $fileName = time() . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('upload/'), $fileName);
                $fileUrl = 'upload/' . $fileName;

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
