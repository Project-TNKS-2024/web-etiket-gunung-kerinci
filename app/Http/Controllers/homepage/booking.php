<?php

namespace App\Http\Controllers\homepage;

use App\Http\Controllers\Controller;
use App\Http\Controllers\helper\BookingHelperController;
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
use Carbon\Carbon;



use function PHPUnit\Framework\isNull;

class booking extends Controller
{


    public function booking($id)
    {
        // id  = id destinasi
        $gunung = destinasi::find($id);
        $gambar_destinasi = gambar_destinasi::where('id_destinasi', $id)->get();
        // paket tiket
        $paket = gk_paket_tiket::where('id_destinasi', $id)->get();

        return view('homepage.booking.booking', [
            "gunung" => $gunung,
            "paket" => $paket,
            "gambar" => $gambar_destinasi,
        ]);
    }

    public function bookingPaket($id)
    {
        // id = id paket
        $paket = gk_paket_tiket::where("id", $id)->first();
        // // ambil data destinasi
        $id_destinasi = $paket->id_destinasi;
        $destinasi = destinasi::with('gates')->where('id', $id_destinasi)->first();
        $gambar_destinasi = gambar_destinasi::where('id_destinasi', $id_destinasi)->get();

        // ambil data tiket
        $tiket = gk_tiket_pendaki::where('id_paket_tiket', $id)->with(['paket_tiket'])->get();

        return view("homepage.booking.booking-paket", [
            "paket" => $paket,
            "destinasi" => $destinasi,
            "gambar" => $gambar_destinasi,
            "tiket" => $tiket,
        ]);
    }

    // ====================================================================== oke
    public function postBooking(Request $request)
    {
        if (!Auth::check()) {
            return redirect()->route('etiket.in.login');
        }

        if (Auth::user()->role != "user") {
            return redirect()->route("homepage.beranda");
        }

        // validasi request
        $request->validate([
            'date_start' => 'required|date',
            'date_end' => 'required|date',
            'wni' => 'required|numeric',
            'wna' => 'required|numeric',
            'jenis_tiket' => 'required|string',
            'gerbang_masuk' => 'required',
            'gerbang_keluar' => 'required',
        ]);

        // cek tanggal mendaki dan tanggal kembali
        $dateStart = Carbon::createFromFormat('d-m-Y', $request->date_start);
        $dateEnd = Carbon::createFromFormat('d-m-Y', $request->date_end);
        $totalDays = $dateStart->diffInDays($dateEnd);

        if ($dateStart > $dateEnd) {
            return back()->with('error', 'Error: tanggal tidak sesuai');
        }


        $booking = gk_booking::where('id_user', Auth::user()->id)
            ->where('status_booking', '<', 3)
            ->orderBy('created_at', 'desc')
            ->first();

        // return $request;

        if ($booking) {
            $booking->update([
                'total_pendaki' => $request->wni + $request->wna,
                'total_pendaki_wni' => $request->wni,
                'total_pendaki_wna' => $request->wna,
                'gate_masuk' => $request->gerbang_masuk,
                'gate_keluar' => $request->gerbang_keluar,
                'tanggal_masuk' => $dateStart,
                'tanggal_keluar' => $dateEnd,
            ]);
            return redirect()->route('homepage.booking-snk', ['id' => $booking->id])->with('success', 'Update Booking');
        } else {
            $newBooking = gk_booking::create([
                'id_user' => Auth::user()->id,
                'id_tiket' => $request->jenis_tiket,
                'tanggal_masuk' => $dateStart,
                'tanggal_keluar' => $dateEnd,
                'kategori_hari' => 'wd',
                'total_hari' => $totalDays,
                'total_pendaki_wni' => $request->wni,
                'total_pendaki_wna' => $request->wna,
                'gate_masuk' => $request->gerbang_masuk,
                'gate_keluar' => $request->gerbang_keluar,

                'status_booking' => 0,
                'total_pembayaran' => 0,
                'status_pembayaran' => false,

                'lampiran_simaksi' => null,
                'lampiran_stugas' => null,
                'unique_code' => null,
                'keterangan' => null,
                'id_booking_master' => null,
            ]);
            // return $newBooking;
            return redirect()->route('homepage.booking-snk', ['id' => $newBooking->id])->with('success', 'Create Booking');
        }
    }
    public function bookingSnk($id)
    {
        $booking = gk_booking::where('id_user', Auth::user()->id)->where('id', $id)->first();
        // return $booking;
        if (!$booking) {
            abort(404);
        }

        if ($booking->status_booking == 1) {
            return redirect()->route('homepage.booking-fp', ['id' => $id]);
        }

        return view('homepage.booking.booking-snk', ['id' => $id, 'status' => false]);
    }

    public function bookingSnkStore(Request $request)
    {
        if ($request->snk) {
            $booking = gk_booking::find($request->id);
            $booking->update(['status_booking' => 1]);
            return redirect()->route('homepage.booking-fp', ['id' => $request->id]);
        } else {
            return back()->withErrors(['snk' => 'Silahkan ceklis data diri anda']);
        }
    }
    // =========================================================================================

    public function bookingFP($id)
    {
        if (!Auth::check()) {
            abort(403);
        }
        $booking = gk_booking::with('gktiket')->where("id", $id)->first();
        if (!$booking) {
            abort(404);
        }
        if ($booking->status_booking == 0) {
            return redirect()->route("homepage.booking-snk", ["id" => $id]);
        }
        $pendaki = gk_pendaki::where('booking_id', $booking->id)->get();
        $barang = gk_barang_bawaan::where('id_booking', $booking->id)->get();

        // return $booking;

        return view('homepage.booking.booking-fp', [
            'id' => $id,
            'booking' => $booking,
            'pendaki' => $pendaki,
            'barang' => $barang,
        ]);
    }
    public function updatePendaki($pendaki, $idbooking, $idtiket)
    {
        // cek id_pendaki
        if (isset($pendaki['id_pendaki'])) {
            $getpendaki = gk_pendaki::find($pendaki['id_pendaki']);
            $getpendaki->update([
                'kategori_pendaki' => isset($pendaki['kewarganegaraan']) ? $pendaki['kewarganegaraan'] : 'wni',
                'nama' => isset($pendaki['nama']) ? $pendaki['nama'] : '-',
                'nik' => isset($pendaki['identitas']) ? $pendaki['identitas'] : '-',
                // 'lampiran_identitas' => isset($pendaki['lampiran_identitas']),

                'no_hp' => isset($pendaki['no_hp']) ? $pendaki['no_hp'] : '-',
                'no_hp_darurat' => isset($pendaki['no_hp_darurat']) ? $pendaki['no_hp_darurat'] : '-',
                'jenis_kelamin' => isset($pendaki['jenis_kelamin']) ? $pendaki['jenis_kelamin'] : 'l',
                'tanggal_lahir' => isset($pendaki['tanggal_lahir']) ? $pendaki['tanggal_lahir'] : '',
                'usia' => isset($pendaki['usia']) ? $pendaki['usia'] : 0,

                'provinsi' => isset($pendaki['provinsi']) ? $pendaki['provinsi'] : '-',
                'kabupaten' => isset($pendaki['kabupaten_kota']) ? $pendaki['kabupaten_kota'] : '-',
                'kec' => isset($pendaki['kecamatan']) ? $pendaki['kecamatan'] : '-',
                'desa' => isset($pendaki['desa_kelurahan']) ? $pendaki['desa_kelurahan'] : '-',

                // 'lampiran_surat_kesehatan' => $pendaki['lampiran_surat_kesehatan'],
                // 'lampiran_surat_izin_ortu' => $pendaki['lampiran_surat_izin_ortu'],
            ]);
            // return $pendaki;
        } else {
            gk_pendaki::create([
                'booking_id' => $idbooking,
                'tiket_id' => $idtiket,

                'kategori_pendaki' => isset($pendaki['kewarganegaraan']) ? $pendaki['kewarganegaraan'] : 'wni',
                'nama' => isset($pendaki['nama']) ? $pendaki['nama'] : '-',
                'nik' => isset($pendaki['identitas']) ? $pendaki['identitas'] : '-',
                // 'lampiran_identitas' => isset($pendaki['lampiran_identitas']) ? $pendaki['lampiran_identitas'] : '-',

                'no_hp' => isset($pendaki['no_hp']) ? $pendaki['no_hp'] : '-',
                'no_hp_darurat' => isset($pendaki['no_hp_darurat']) ? $pendaki['no_hp_darurat'] : '-',
                'jenis_kelamin' => isset($pendaki['jenis_kelamin']) ? $pendaki['jenis_kelamin'] : 'l',
                'tanggal_lahir' => isset($pendaki['tanggal_lahir']) ? $pendaki['tanggal_lahir'] : time(),
                'usia' => isset($pendaki['usia']) ? $pendaki['usia'] : 0,

                'provinsi' => isset($pendaki['provinsi']) ? $pendaki['provinsi'] : '-',
                'kabupaten' => isset($pendaki['kabupaten_kota']) ? $pendaki['kabupaten_kota'] : '-',
                'kec' => isset($pendaki['kecamatan']) ? $pendaki['kecamatan'] : '-',
                'desa' => isset($pendaki['desa_kelurahan']) ? $pendaki['desa_kelurahan'] : '-',

                // 'lampiran_surat_kesehatan' => isset($pendaki['lampiran_surat_kesehatan']) ? $pendaki['lampiran_surat_kesehatan'] : '-',
                // 'lampiran_surat_izin_ortu' => isset($pendaki['lampiran_surat_izin_ortu']) ? $pendaki['lampiran_surat_izin_ortu'] : '-',
                'tagihan' => 0,
            ]);
        }
    }
    public function bookingFPStore(Request $request)
    {
        $booking = gk_booking::with('gktiket')->find($request->id_booking);

        if ($request->action == "save") {
            $validatedData = $request->validate([
                'id_booking' => 'required|integer',
                'formulir' => 'required|array',

                'formulir.*.id_pendaki' => 'nullable|string',
                'formulir.*.kewarganegaraan' => 'nullable|string',
                'formulir.*.jenis_kelamin' => 'nullable|string',
                // 'formulir.*.jenis_identitas' => 'nullable|string',
                'formulir.*.identitas' => 'nullable|string',
                // 'formulir.*.lampiran_identitas' => 'nullable|file|mimes:jpg,jpeg,png|max:2048',
                'formulir.*.nama' => 'nullable|string',
                'formulir.*.no_hp' => 'nullable|string',
                'formulir.*.no_hp_darurat' => 'nullable|string',
                'formulir.*.tanggal_lahir' => 'nullable|date',
                'formulir.*.usia' => 'nullable|integer',

                'formulir.*.provinsi' => 'nullable|string',
                'formulir.*.kabupaten_kota' => 'nullable|string',
                'formulir.*.kecamatan' => 'nullable|string',
                'formulir.*.desa_kelurahan' => 'nullable|string',

                // 'formulir.0.surat_simaksi' => 'nullable|string',
                // 'formulir.*.surat_izin_ortu' => 'nullable|string',
                // 'formulir.*.surat_keterangan_sehat' => 'nullable|string',

                // 'barangWajib' => 'required|array',
                // 'barangWajib.perlengkapan_gunung_standar' => 'required|boolean|accepted',
                // 'barangWajib.trash_bag' => 'required|boolean|accepted',
                // 'barangWajib.p3k_standart' => 'required|boolean|accepted',
                // 'barangWajib.survival_kit_standart' => 'required|boolean|accepted',

                'jumlah_barang' => 'integer|min:0',

                'barangTambahan' => 'nullable|array',
                'barangTambahan.*.nama' => 'nullable|string',
                'barangTambahan.*.jumlah' => 'nullable|integer|min:0',
            ]);

            // update pendaki
            $pendakis = $request->formulir;
            foreach ($pendakis as $pendaki) {
                $this->updatePendaki($pendaki, $request->id_booking, $booking->id_tiket);
                // return $pendaki;
            }

            return redirect()->back()->with('success', 'Data berhasil disimpan');
        } else if ($request->action == "next") {
            $validatedData = $request->validate([
                'id_booking' => 'required|integer',
                'formulir' => 'required|array',

                'formulir.*.id_pendaki' => 'required|string',
                'formulir.*.kewarganegaraan' => 'required|string',
                'formulir.*.jenis_kelamin' => 'required|string',
                // 'formulir.*.jenis_identitas' => 'required|string',
                // 'formulir.*.lampiran_identitas' => 'nullable|file|mimes:jpg,jpeg,png|max:2048',
                'formulir.*.identitas' => 'required|string',
                'formulir.*.nama' => 'required|string',

                'formulir.*.no_hp' => 'required|string',
                'formulir.*.no_hp_darurat' => 'required|string',
                'formulir.*.tanggal_lahir' => 'required|date',
                'formulir.*.usia' => 'required|integer',

                'formulir.*.provinsi' => 'required|string',
                'formulir.*.kabupaten_kota' => 'required|string',
                'formulir.*.kecamatan' => 'required|string',
                'formulir.*.desa_kelurahan' => 'required|string',

                // surat tipe file
                // 'formulir.0.surat_simaksi' => 'required|file|mimes:pdf|max:2048',
                // 'formulir.*.surat_izin_ortu' => 'required|file|mimes:pdf|max:2048',
                // 'formulir.*.surat_keterangan_sehat' => 'required|file|mimes:pdf|max:2048',


                'barangWajib' => 'required|array',
                'barangWajib.perlengkapan_gunung_standar' => 'required|boolean|accepted',
                'barangWajib.trash_bag' => 'required|boolean|accepted',
                'barangWajib.p3k_standart' => 'required|boolean|accepted',
                'barangWajib.survival_kit_standart' => 'required|boolean|accepted',

                'jumlah_barang' => 'nullable|integer|min:0',
                'barangTambahan' => 'nullable|array',
                'barangTambahan.*.nama' => 'nullable|string',
                'barangTambahan.*.jumlah' => 'nullable|integer|min:0',
                'action' => 'nullable|string|in:save,next',
            ]);

            // update pendaki
            $pendakis = $request->formulir;
            foreach ($pendakis as $pendaki) {
                $this->updatePendaki($pendaki, $request->id_booking, $booking->id_tiket);
                // return $pendaki;
            }

            // update barang
            $barangs = $request->barangTambahan;
            foreach ($barangs as $barang) {
                gk_barang_bawaan::create([
                    'id_booking' => $request->id_booking,
                    'nama_barang' => $barang['nama'],
                    'jumlah' => $barang['jumlah'],
                ]);
            }
            $booking->update([
                'status_booking' => 2,
            ]);

            // return $request;
            return redirect()->route('homepage.booking-detail', ['id' => $request->id_booking])->with('success', 'Data berhasil disimpan');
        }
    }
    // =========================================================================================

    public function bookingDetail($id)
    {
        // id booking

        $booking = gk_booking::with(['gateMasuk', 'gateKeluar', 'pendakis'])->where('id', $id)->first();
        // return $booking;

        if (!$booking) {
            abort(404);
        }

        if ($booking->status_booking == 0) {
            return redirect()->route("homepage.booking-snk", ["id" => $id]);
        }

        $ClassHelper = new BookingHelperController();

        // main
        // $tes =  $ClassHelper->countWeekdaysAndWeekends($booking->tanggal_masuk, $booking->tanggal_keluar);
        // return $tes->getData()->weekdays + $tes->getData()->weekends;

        // return $booking->pendakis[1];
        // return $booking->pendakis[1]->booking_id;






        // return $ClassHelper->getTagihanPendaki($booking->pendakis[1]);


        // end main
        $ClassHelper->validasiBooking($booking->id);
        $dataBooking = gk_booking::with(['gateMasuk', 'gateKeluar', 'pendakis'])->where('id', $id)->first();

        // return $dataBooking->gateMasuk;

        return view('homepage.booking.booking-detail', [
            'booking' => $dataBooking,
            'pendakis' => $dataBooking->pendakis
        ]);
    }
}
