<?php

namespace App\Http\Controllers\homepage;

use App\Http\Controllers\Controller;
use App\Models\gk_booking;
use App\Models\gk_gates;
use App\Models\tiket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class booking extends Controller
{
    public function booking($id)
    {

        $tiket = tiket::find($id);
        if ($tiket->spesial == 'gunungKerinci') {
            $gates = gk_gates::all();
            return view("homepage.booking.booking", ["gates" => $gates, "tiket" => $tiket]);
        }
        return view("homepage.booking.booking", ["tiket" => $tiket]);
    }

    public function postBooking(Request $request)
    {
        if (!Auth::check()) {
            return redirect()->route('etiket.in.login');
        }

        if (Auth::user()->role == 'user') {

            // jika user
            // cek booking sebelumnya

            // jika booking sebekumnya sudah ada yang status==0
            // update booking sebelumnya

            // jika tidak ada booking sebekumnya yang status==0
            // buat booking baru

            // return redirect()->route('homepage.booking-snk', ['id' => $booking->id]);

            return redirect()->route('homepage.booking-snk', ['id' => $request->id]);
        } else {

            return 0;
        }
    }
    // =========================================================================================
    public function bookingSnk($id)
    {
        // id booking
        return view('homepage.booking.booking-snk', ['id' => $id]);
    }
    public function bookingSnkStore(Request $request)
    {
        if ($request->snk) {
            return redirect()->route('homepage.booking-fp', ['id' => $request->id]);
        } else {
            return back()->withErrors(['snk' => 'Silahkan ceklis data diri anda']);
        }
    }
    // =========================================================================================

    public function bookingFP($id)
    {
        // id booking
        return view('homepage.booking.booking-fp', ['id' => $id]);
    }
    public function bookingFPStore(Request $request)
    {
        if ($request->action == "save") {
            // return $request;
            return redirect()->back()->with('successa', 'Data berhasil disimpan');
            // $validatedData = $request->validate([
            //     'id_booking' => 'required|integer',
            //     'formulir' => 'required|array',
            //     'formulir.*.jenis_identitas' => 'required|string',
            //     'formulir.*.identitas' => 'nullable|string',
            //     'formulir.*.nama_depan' => 'nullable|string',
            //     'formulir.*.nama_belakang' => 'nullable|string',
            //     'formulir.*.nomor_telepon' => 'nullable|string',
            //     'formulir.*.nomor_telepon_darurat' => 'nullable|string',
            //     'formulir.*.tanggal_lahir' => 'nullable|date',
            //     'formulir.*.usia' => 'nullable|integer',
            //     'formulir.*.provinsi' => 'nullable|string',
            //     'formulir.*.kabupaten_kota' => 'nullable|string',
            //     'formulir.*.kecamatan' => 'nullable|string',
            //     'formulir.*.desa_kelurahan' => 'nullable|string',
            //     'barangWajib' => 'required|array',
            //     'barangWajib.perlengkapan_gunung_standar' => 'required|boolean|accepted',
            //     'barangWajib.trash_bag' => 'required|boolean|accepted',
            //     'barangWajib.p3k_standart' => 'required|boolean|accepted',
            //     'barangWajib.survival_kit_standart' => 'required|boolean|accepted',
            //     'jumlah_barang' => 'required|integer|min:0',
            //     'barangTambahan' => 'nullable|array',
            //     'barangTambahan.*.nama' => 'nullable|string',
            //     'barangTambahan.*.jumlah' => 'nullable|integer|min:0',
            //     'action' => 'required|string|in:save,next',
            // ]);
            // update booking
            // return redirect()->route('homepage.booking-fp', ['id' => $request->id])->with('success', 'Data berhasil disimpan');
        } else if ($request->action == "next") {
            return redirect()->route('homepage.booking-detail', ['id' => $request->id_booking])->with('success', 'Data berhasil disimpan');
            // $validatedData = $request->validate([
            //     'id_booking' => 'required|integer',
            //     'formulir' => 'required|array',
            //     'formulir.*.jenis_identitas' => 'required|string',
            //     'formulir.*.identitas' => 'required|string',
            //     'formulir.*.nama_depan' => 'required|string',
            //     'formulir.*.nama_belakang' => 'required|string',
            //     'formulir.*.nomor_telepon' => 'required|string',
            //     'formulir.*.nomor_telepon_darurat' => 'required|string',
            //     'formulir.*.tanggal_lahir' => 'required|date',
            //     'formulir.*.usia' => 'required|integer',
            //     'formulir.*.provinsi' => 'required|string',
            //     'formulir.*.kabupaten_kota' => 'required|string',
            //     'formulir.*.kecamatan' => 'required|string',
            //     'formulir.*.desa_kelurahan' => 'required|string',
            //     'barangWajib' => 'required|array',
            //     'barangWajib.perlengkapan_gunung_standar' => 'required|boolean|accepted',
            //     'barangWajib.trash_bag' => 'required|boolean|accepted',
            //     'barangWajib.p3k_standart' => 'required|boolean|accepted',
            //     'barangWajib.survival_kit_standart' => 'required|boolean|accepted',
            //     'jumlah_barang' => 'required|integer|min:0',
            //     'barangTambahan' => 'required|array',
            //     'barangTambahan.*.nama' => 'required|string',
            //     'barangTambahan.*.jumlah' => 'required|integer|min:0',
            //     'action' => 'required|string|in:save,next',
            // ]);
        }
    }
    // =========================================================================================

    public function bookingDetail($id)
    {
        // id booking

        $booking = gk_booking::find($id);
        $gates = gk_gates::all();

        // return [
        //     'id' => $id,
        //     'booking' => $booking,
        //     'gates' => $gates
        // ];

        return view('homepage.booking.booking-detail', [
            'booking' => $booking,
            'gates' => $gates
        ]);
    }
}
