<?php

namespace App\Http\Controllers\homepage;

use App\Http\Controllers\Controller;
use App\Models\destinasi;
use App\Models\gk_barang_bawaan;
use App\Models\gk_booking;
use App\Models\gk_gates;
use App\Models\gk_pendaki;
use App\Models\gk_tiket_pendaki;
use App\Models\gambar_destinasi;
use App\Models\gk_paket_tiket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use function PHPUnit\Framework\isNull;

class booking extends Controller
{
    public function booking($id)
    {

        $paket = gk_paket_tiket::where('id_destinasi', $id)->get();
        $gunung = destinasi::find($id);
        $gambar_destinasi = gambar_destinasi::where('id_destinasi', $id)->get();

        // return $gunung;
        return view('homepage.booking.booking', [
            "gunung" => $gunung,
            "paket" => $paket,
            "gambar" => $gambar_destinasi,
        ]);
        // return $data;
    }

    public function bookingPaket($id)
    {
        $gunung = destinasi::find($id);
        $paket = gk_paket_tiket::find($id);
        $tiket = gk_paket_tiket::where('id_destinasi', $id)->get();
        $gates = gk_gates::where('id_destinasi', $id)->get();
        $gambar_destinasi = gambar_destinasi::where('id_destinasi', $id)->get();
        // return $tiket;
        return view("homepage.booking.booking-paket", [
            "gunung" => $gunung,
            "paket" => $paket,
            "tiket" => $tiket,
            "gates" => $gates,
            "gambar" => $gambar_destinasi,
        ]);
    }

    public function postBooking(Request $request)

    {
        if (!Auth::check()) {
            return redirect()->route('etiket.in.login');
        }

        if (Auth::user()->role == 'user') {
            $request->validate([
                'date_start' => 'required|date',
                'date_end' => 'required|date',
                'wni' => 'required|numeric',
                'wna' => 'required|numeric',
                'jenis_tiket' => 'required|string',
                'gerbang_masuk' => 'required',
                'gerbang_keluar' => 'required',
            ]);


            // $booking = gk_booking::where('id_tiket', $request->id_tiket)->where('status', '<', 3)->first();
            // if ($booking) {

            //     // update data booking dengan yang baru
            //     $booking->update([
            //         'total_pendaki' => $request->wni + $request->wna,
            //         'wni' => $request->wni,
            //         'wna' => $request->wna,
            //         'gate_masuk' => $request['gerbang-masuk'],
            //         'gate_keluar' => $request['gerbang-keluar'],
            //         'tanggal_masuk' => $request['date-start'],
            //         'tanggal_keluar' => $request['date-end'],
            //     ]);

            //     return redirect()->route('homepage.booking-snk', ['id' => $booking->id])->with('success', 'Update Booking');
            // } else {
            //     // buat booking baru
            //     $newBooking = gk_booking::create([
            //         'id_user' => Auth::user()->id,
            //         'id_tiket' => $request->id_tiket,
            //         'status' => 0,
            //         'id_booking_master' => 0,
            //         'total_pendaki' => $request->wni + $request->wna,
            //         'wni' => $request->wni,
            //         'wna' => $request->wna,
            //         'keterangan' => $request->keterangan,
            //         'QR' => null,
            //         'pembayaran' => false,
            //         'gate_masuk' => $request['gerbang-masuk'],
            //         'gate_keluar' => $request['gerbang-keluar'],
            //         'tanggal_masuk' => $request['date-start'],
            //         'tanggal_keluar' => $request['date-end'],
            //     ]);
            //     // return $newBooking;
            //     return redirect()->route('homepage.booking-snk', ['id' => $newBooking->id])->with('success', 'Create Booking');
            // }

            // return redirect()->route('homepage.booking-snk', ['id' => $request->id]);
        } else {
            return redirect()->back();
        }
    }
    // =========================================================================================
    public function bookingSnk($id)
    {
        // id booking
        $booking = gk_booking::find($id);
        // return $booking;
        return view('homepage.booking.booking-snk', ['id' => $id, 'status' => $booking->status]);
    }
    public function bookingSnkStore(Request $request)
    {
        // return $request;
        if ($request->snk) {
            // update booking status = 1
            $booking = gk_booking::find($request->id);
            $booking->update(['status' => 1]);
            return redirect()->route('homepage.booking-fp', ['id' => $request->id]);
        } else {
            return back()->withErrors(['snk' => 'Silahkan ceklis data diri anda']);
        }
    }
    // =========================================================================================

    public function bookingFP($id)
    {
        $booking = gk_booking::find($id);
        $pendaki = gk_pendaki::where('booking_id', $booking->id)->get();
        $barang = gk_barang_bawaan::where('id_booking', $booking->id)->get();

        return view('homepage.booking.booking-fp', [
            'id' => $id,
            'booking' => $booking,
            'pendaki' => $pendaki,
            'barang' => $barang
        ]);
    }
    public function bookingFPStore(Request $request)
    {
        if ($request->action == "save") {

            $validatedData = $request->validate([
                'id_booking' => 'required|integer',
                'formulir' => 'required|array',

                'formulir.*.jenis_identitas' => 'required|string',
                'formulir.*.identitas' => 'nullable|string',
                'formulir.*.nama_depan' => 'nullable|string',
                'formulir.*.nama_belakang' => 'nullable|string',
                'formulir.*.nomor_telepon' => 'nullable|string',
                'formulir.*.nomor_telepon_darurat' => 'nullable|string',
                'formulir.*.tanggal_lahir' => 'nullable|date',
                'formulir.*.provinsi' => 'nullable|string',
                'formulir.*.kabupaten_kota' => 'nullable|string',
                'formulir.*.kecamatan' => 'nullable|string',
                'formulir.*.desa_kelurahan' => 'nullable|string',

                'barangWajib' => 'required|array',
                'barangWajib.perlengkapan_gunung_standar' => 'required|boolean|accepted',
                'barangWajib.trash_bag' => 'required|boolean|accepted',
                'barangWajib.p3k_standart' => 'required|boolean|accepted',
                'barangWajib.survival_kit_standart' => 'required|boolean|accepted',

                'jumlah_barang' => 'required|integer|min:0',

                'barangTambahan' => 'nullable|array',
                'barangTambahan.*.nama' => 'nullable|string',
                'barangTambahan.*.jumlah' => 'nullable|integer|min:0',
                'action' => 'required|string|in:save,next',
            ]);

            // update pendaki
            $pendakis = $request->formulir;
            // return $pendakis[0];

            foreach ($pendakis as $pendaki) {
                // menyimpan/update lampiran
                $lIdentitas = '-';
                $lSuratKesehatan = '-';
                $lSimaksi = '-';

                // return $pendaki;
                // cek jika id pendaki ada
                if ($pendaki['id_pendaki'] != null) {
                    // update data pndaki by id
                    $data = gk_pendaki::where('id', $pendaki['id_pendaki'])
                        ->where('booking_id', $request->id_booking)
                        ->update([
                            'wni' => $pendaki['jenis_identitas'] == 'KTP',
                            'nik' => $pendaki['identitas'],
                            'nama' => $pendaki['nama_depan'] . ' ' . $pendaki['nama_belakang'],
                            'lampiran_identitas' => $lIdentitas,
                            'no_hp' => $pendaki['nomor_telepon'],
                            'no_hp_darurat' => $pendaki['nomor_telepon_darurat'],
                            'tanggal_lahir' => $pendaki['tanggal_lahir'],
                            'usia' => $pendaki['usia'],
                            'provinsi' => $pendaki['provinsi'],
                            'kabupaten' => $pendaki['kabupaten_kota'],
                            'kec' => $pendaki['kecamatan'],
                            'desa' => $pendaki['desa_kelurahan'],
                            'lampiran_surat_kesehatan' => $lSuratKesehatan,
                            'lampiran_simaksi' => $lSimaksi,
                            'ketua' => $pendaki['ketua'] ?? false,
                        ]);
                    return $data;
                } else {
                    $data = gk_pendaki::create([
                        'booking_id' => $request->id_booking,
                        'wni' => $pendaki['jenis_identitas'] == 'KTP',
                        'nik' => $pendaki['identitas'],
                        'nama' => $pendaki['nama_depan'] . ' ' . $pendaki['nama_belakang'],
                        'lampiran_identitas' => $lIdentitas,
                        'no_hp' => $pendaki['nomor_telepon'],
                        'no_hp_darurat' => $pendaki['nomor_telepon_darurat'],
                        'tanggal_lahir' => $pendaki['tanggal_lahir'],
                        'usia' => $pendaki['usia'],
                        'provinsi' => $pendaki['provinsi'],
                        'kabupaten' => $pendaki['kabupaten_kota'],
                        'kec' => $pendaki['kecamatan'],
                        'desa' => $pendaki['desa_kelurahan'],
                        'lampiran_surat_kesehatan' => $lSuratKesehatan,
                        'lampiran_simaksi' => $lSimaksi,
                        'ketua' => $pendaki['ketua'] ?? false,
                    ]);
                }
                return $data;
            }

            // return redirect()->back()->with('success', 'Data berhasil disimpan');
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
