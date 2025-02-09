<?php

namespace App\Http\Controllers\tracking\admin\checkpoint;

use App\Http\Controllers\Controller;
use App\Models\gk_checkpoint;
use Illuminate\Http\Request;

class CheckpointController extends Controller
{
    public function daftar()
    {
        $data = gk_checkpoint::orderBy('urutan', 'asc')->get();
        return view('tracking.admin.checkpoints.index', [
            "checkpoints" => $data
        ]);
    }

    public function tambah()
    {
        $checkpoints = gk_checkpoint::orderBy('urutan', 'asc')->get();
        return view('tracking.admin.master-data.checkpoints.tambah', compact('checkpoints'));
    }

    public function tambahAction(Request $request)
    {

        $request->validate([
            'nama' => 'required|string|max:255',
            'deskripsi_naik' => 'required',
            'deskripsi_turun' => 'required',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'urutan' => 'required|integer',
        ]);

        // return $request;
        gk_checkpoint::where('urutan', '>=', $request->urutan)
            ->orderBy('urutan', direction: 'desc') 
            ->increment('urutan');


        $proceed = gk_checkpoint::create([
            'nama' => $request->nama,
            'deskripsi_naik' => $request->deskripsi_naik,
            'deskripsi_turun' => $request->deskripsi_turun,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'urutan' => $request->urutan,
        ]);
        

        if (!$proceed) {
            return back()->with('error', 'Terjadi kesalahan ketika menambahkan checkpoint');
        }
        return back()->with('success', 'Berhasil menambah Chekpoint');
    }

    public function edit($id)
    {
        $data = gk_checkpoint::where('id', operator: $id)->first();
        $checkpoints = gk_checkpoint::orderBy('urutan', 'asc')->get();
        return view('tracking.admin.master-data.checkpoints.edit', compact('checkpoints', 'data'));
    }

    public function editAction(Request $request, $id)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'deskripsi_naik' => 'required',
            'deskripsi_turun' => 'required',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'urutan' => 'required|integer',
        ]);

        $checkpoint = gk_checkpoint::find($id);

        if (!$checkpoint) {
            return back()->with('error', 'Checkpoint tidak ditemukan');
        }

        // $tempUrutan = 0;

        // $checkpoint->update([
        //     'urutan' => $tempUrutan,
        // ]);


        // Urutan lama sebelum diedit
        $oldUrutan = $checkpoint->urutan;
        // Urutan baru setelah diedit
        $newUrutan = $request->urutan;

        if ($oldUrutan != $newUrutan) {
            // Jika urutan diubah ke urutan yang lebih besar (misalnya 1 ke 4)
            if ($oldUrutan < $newUrutan) {
                // Kurangi urutan checkpoints di antara oldUrutan dan newUrutan (order by ascending untuk menjaga urutan)
                gk_checkpoint::where('urutan', '>', $oldUrutan)
                    ->where('urutan', '<=', $newUrutan)
                    ->orderBy('urutan', 'asc') // Menghindari error pengurutan
                    ->decrement('urutan');
            }
            // Jika urutan diubah ke urutan yang lebih kecil (misalnya 4 ke 1)
            elseif ($oldUrutan > $newUrutan) {
                // Tambah urutan checkpoints di antara newUrutan dan oldUrutan (order by descending untuk menjaga urutan)
                gk_checkpoint::where('urutan', '>=', $newUrutan)
                    ->where('urutan', '<', $oldUrutan)
                    ->orderBy('urutan', 'desc') // Menghindari error pengurutan
                    ->increment('urutan');
            }
        }

        $checkpoint->update([
            'nama' => $request->nama,
            'deskripsi_naik' => $request->deskripsi_naik,
            'deskripsi_turun' => $request->deskripsi_turun,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'urutan' => $newUrutan,
        ]);

        if (!$checkpoint) {
            return back()->with('error', 'Terjadi kesalahan ketika mengedit Checkpoint');
        }

        return back()->with('success', 'Berhasil memperbaharui Checkpoint');
    }

    public function hapus($id)
    {
        $checkPoint = gk_checkpoint::findOrFail($id);
        $checkPoint->delete();
        return back()->with('success', 'Berhasil Menghapus Checkpoint');
    }

}
