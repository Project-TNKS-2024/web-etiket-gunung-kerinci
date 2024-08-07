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
use Carbon\Carbon;
use Carbon\CarbonPeriod;


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
        $uid = 1;
        $destinasi = destinasi::with('gates')->where('id',$uid)->first();
        $gambar_destinasi = gambar_destinasi::where('id_destinasi',$uid)->get();
        $tiket = gk_tiket_pendaki::with(['paket_tiket'])
            ->whereHas('paket_tiket', function ($query) use ($uid) {
                $query->where('id_destinasi', $uid);
            })
            ->get();
        $paket = gk_paket_tiket::where("id", $id)->first();
        $tiket_pendaki = gk_tiket_pendaki::where('id_paket_tiket',$id)->get();


        // return $tiket;
        return view("homepage.booking.booking-paket", [
            "destinasi" => $destinasi,
            "tiket" => $tiket,
            "gambar" => $gambar_destinasi,
            "jenis_tiket" => $id,
            "paket" =>$paket,
            "tiket_pendaki" => $tiket_pendaki
        ]);
    }

    public function postBooking(Request $request)

    {
        if (!Auth::check()) {
            return redirect()->route('etiket.in.login');
        }

        if (Auth::user()->role != "user") {
            return redirect()->route("homepage.beranda");
        }

            $request->validate([
                'date_start' => 'required|date',
                'date_end' => 'required|date',
                'wni' => 'required|numeric',
                'wna' => 'required|numeric',
                'jenis_tiket' => 'required|string',
                'gerbang_masuk' => 'required',
                'gerbang_keluar' => 'required',
            ]);


            $tiket = gk_paket_tiket::with('tiket_pendaki')->where('id', $request->jenis_tiket)->first();
            $days = $this->countWeekdaysAndWeekends($request->date_start,$request->date_end);

            // retu
            $hargaWni = $tiket->tiket_pendaki->where('kategori_pendaki','wni')->where('id_paket_tiket', intval($request->jenis_tiket))->first();
            $totalHargaWni = ($days['weekends'] * $hargaWni->harga_masuk_wk + $days['weekdays'] * $hargaWni->harga_masuk_wd + $hargaWni->harga_kemah * ($days['weekdays'] +$days['weekends']-1)) * intval($request->wni) + ($hargaWni->harga_traking*intval($request->wni)) + ($hargaWni->harga_ansuransi*intval($request->wni));

            $hargaWna = $tiket->tiket_pendaki->where('kategori_pendaki','wna')->where('id_paket_tiket', intval($request->jenis_tiket))->first();
            $totalHargaWna = ($days['weekends'] * $hargaWni->harga_masuk_wk + $days['weekdays'] * $hargaWna->harga_masuk_wd + $hargaWna->harga_kemah * ($days['weekdays'] +$days['weekends']-1)) * intval($request->wna) + ($hargaWna->harga_traking*intval($request->wna)) + ($hargaWna->harga_ansuransi*intval($request->wna));
            ;

            $totalHarga = $totalHargaWna+$totalHargaWni;

            $dateStart = Carbon::createFromFormat('d-m-Y', $request->date_start);
            $dateEnd = Carbon::createFromFormat('d-m-Y', $request->date_end);
            $totalDays = $dateStart->diffInDays($dateEnd);


            if ($dateStart > $dateEnd) {
                return back()->with('error', 'Error: tanggal tidak sesuai');
            }
            $booking = gk_booking::where('id_user', Auth::user()->id)->where('id', $request->id_booking)->where('status_booking', '<', 3)->first();
            if ($booking) {

                $booking->update([
                    'total_pendaki' => $request->wni + $request->wna,
                    'wni' => $request->wni,
                    'wna' => $request->wna,
                    'gate_masuk' => $request['gerbang_masuk'],
                    'gate_keluar' => $request['gerbang_keluar'],
                    'tanggal_masuk' => $request['date_start'],
                    'tanggal_keluar' => $request['date_end'],
                ]);

                return redirect()->route('homepage.booking-snk', ['id' => $booking->id])->with('success', 'Update Booking');
            } else {
                $newBooking = gk_booking::create([
                    'id_user' => Auth::user()->id,
                    'id_tiket' => $request->jenis_tiket,
                    'status_booking' => 0,
                    'id_booking_master' => 0,
                    'total_pendaki' => $request->wni + $request->wna,
                    'total_pendaki_wni' => $request->wni,
                    'total_pendaki_wna' => $request->wna,
                    'QR' => null,
                    'pembayaran' => false,
                    'gate_masuk' => $request->gerbang_masuk,
                    'gate_keluar' => $request->gerbang_keluar,
                    'tanggal_masuk' => Carbon::createFromFormat('d-m-Y', $request->date_start),
                    'tanggal_keluar' =>Carbon::createFromFormat('d-m-Y', $request->date_end),
                    'total_hari' => $totalDays,
                    'total_pembayaran' => $totalHarga,
                    'lampiran_simaksi' => "-",
                    'lampiran_stugas' => "-",
                    'unique_code' => $this->generateRandomKey(10),
                ]);
                // return $newBooking;
                return redirect()->route('homepage.booking-snk', ['id' => $newBooking->id])->with('success', 'Create Booking');
            }

            return redirect()->route('homepage.booking-snk', ['id' => $request->id]);
    }
    public function bookingSnk($id)
    {
        // id booking
        if (!Auth::check()) {
            return redirect()->route('homepage.beranda');
        }
        $booking = gk_booking::where('id_user', Auth::user()->id)->where('id', $id)->first();
        if (!$booking){
            abort(404);
        }

        if ($booking->status_booking ==1) {
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
            return redirect()->route("homepage.booking-snk", ["id"=> $id] );
        }
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
            $paket = gk_paket_tiket::all();
            $booking = gk_booking::where('id', $request->id_booking)->first();
            $tiket = gk_paket_tiket::with('tiket_pendaki')->where('id', $booking->id_tiket)->first();
            // return $pendakis[0];

            foreach ($pendakis as $pendaki) {
                $nationality = $pendaki['kewarganegaraan'] == "Warga Negara Indonesia" ? "wni" : "wna";
                $days = $this->countWeekdaysAndWeekends(Carbon::parse($booking->tanggal_masuk)->format('d-m-Y'),Carbon::parse($booking->tanggal_keluar)->format('d-m-Y'));
                $tiket_pendaki = $tiket->tiket_pendaki->where('kategori_pendaki',$nationality)->where('id_paket_tiket', intval($booking->id_tiket))->first();
                $tagihan = $days['weekends'] * $tiket_pendaki->harga_masuk_wk + $days['weekdays'] * $tiket_pendaki->harga_masuk_wd + $tiket_pendaki->harga_kemah * ( $days['weekends'] +  $days['weekdays'] - 1) + $tiket_pendaki->harga_traking + $tiket_pendaki->harga_ansuransi;
                $pendakiUsia = Carbon::parse($pendaki['tanggal_lahir'])->age;

                $lIdentitas = '-';
                $lSuratKesehatan = '-';
                $lSimaksi = '-';
                $lSuratIzin = '-';
                if ($pendaki['id_pendaki'] != null) {
                    $data = gk_pendaki::where('id', $pendaki['id_pendaki'])
                        ->where('booking_id', $request->id_booking)
                        ->update([
                            'nik' => $pendaki['identitas'],
                            'nama' => $pendaki['nama_depan'] . '<----->' . $pendaki['nama_belakang'],
                            'lampiran_identitas' => $lIdentitas,
                            'no_hp' => $pendaki['nomor_telepon'],
                            'no_hp_darurat' => $pendaki['nomor_telepon_darurat'],
                            'tanggal_lahir' => $pendaki['tanggal_lahir'],
                            'usia' => $pendakiUsia,
                            'provinsi' => $pendaki['provinsi'],
                            'kabupaten' => $pendaki['kabupaten_kota'],
                            'kec' => $pendaki['kecamatan'],
                            'desa' => $pendaki['desa_kelurahan'],
                            'lampiran_surat_kesehatan' => $lSuratKesehatan,
                            'tiket_id' => $tiket_pendaki->id,
                            'lampiran_surat_izin_ortu' => $lSuratIzin,
                            'tagihan' => $tagihan,
                            'kategori_pendaki' => $nationality,
                            'jenis_kelamin' => $pendaki['jenis_kelamin'] == "Laki-Laki" ? "l" : "p",
                            'jenis_identitas' => $pendaki['jenis_identitas'],
                        ]);
                    // return $data;
                } else {
                    $data = gk_pendaki::create([
                        'booking_id' => $request->id_booking,
                        'nik' => $pendaki['identitas'],
                        'nama' => $pendaki['nama_depan'] . '<----->' . $pendaki['nama_belakang'],
                        'lampiran_identitas' => $lIdentitas,
                        'no_hp' => $pendaki['nomor_telepon'],
                        'no_hp_darurat' => $pendaki['nomor_telepon_darurat'],
                        'tanggal_lahir' => $pendaki['tanggal_lahir'],
                        'usia' => $pendakiUsia,
                        'provinsi' => $pendaki['provinsi'],
                        'kabupaten' => $pendaki['kabupaten_kota'],
                        'kec' => $pendaki['kecamatan'],
                        'desa' => $pendaki['desa_kelurahan'],
                        'lampiran_surat_kesehatan' => $lSuratKesehatan,
                        'tiket_id' => $tiket_pendaki->id,
                        'lampiran_surat_izin_ortu' => $lSuratIzin,
                        'tagihan' => $tagihan,
                        'kategori_pendaki' => $nationality,
                        'jenis_kelamin' => $pendaki['jenis_kelamin'] == "Laki-Laki" ? "l" : "p",
                        'jenis_identitas' => $pendaki['jenis_identitas'],
                    ]);
                }
                // return $data;
            }

            return redirect()->back()->with('success', 'Data berhasil disimpan');
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

        $booking = gk_booking::with(['gateMasuk', 'gateKeluar'])->where('id',$id)->first();
        if (!$booking) {
            abort(404);
        }

        if ($booking->status_booking == 0) {
            return redirect()->route("homepage.booking-snk", ["id"=> $id] );
        }
        $gates = gk_gates::all();

        // return [
        //     'id' => $id,
        //     'booking' => $booking,
        //     'gates' => $gates
        // ];

        $tiket = gk_paket_tiket::with('tiket_pendaki')->where('id', $booking->id_tiket)->first();
        $days = $this->countWeekdaysAndWeekends(Carbon::parse($booking->tanggal_masuk)->format('d-m-Y'),Carbon::parse($booking->tanggal_keluar)->format('d-m-Y'));

        // retu
        $hargaWni = $tiket->tiket_pendaki->where('kategori_pendaki','wni')->where('id_paket_tiket', intval($booking->id_tiket))->first();
        $totalHargaWni = ($days['weekends'] * $hargaWni->harga_masuk_wk + $days['weekdays'] * $hargaWni->harga_masuk_wd + $hargaWni->harga_kemah * ($days['weekdays'] +$days['weekends']-1)) * intval($booking->total_pendaki_wni) + ($hargaWni->harga_traking*intval($booking->total_pendaki_wni)) + ($hargaWni->harga_ansuransi*intval($booking->total_pendaki_wni));

        $hargaWna = $tiket->tiket_pendaki->where('kategori_pendaki','wna')->where('id_paket_tiket', intval($booking->id_tiket))->first();
        $totalHargaWna = ($days['weekends'] * $hargaWni->harga_masuk_wk + $days['weekdays'] * $hargaWna->harga_masuk_wd + $hargaWna->harga_kemah * ($days['weekdays'] +$days['weekends']-1)) * intval($booking->total_pendaki_wna) + ($hargaWna->harga_traking*intval($booking->total_pendaki_wna)) + ($hargaWna->harga_ansuransi*intval($booking->total_pendaki_wna));
        ;

        $totalHarga = $totalHargaWna+$totalHargaWni;

        return view('homepage.booking.booking-detail', [
            'booking' => $booking,
            'gates' => $gates,
            'hargaWni' => $hargaWni,
            'totalHargaWni' => $totalHargaWni,
            'hargaWna' => $hargaWna,
            'totalHargaWna' => $totalHargaWna,
            'days' => $days,
            'days' => $days

        ]);
    }

    function countWeekdaysAndWeekends($dateStart, $dateEnd) {
        // Convert string dates to Carbon instances
        $start = Carbon::createFromFormat('d-m-Y', $dateStart);
        $end = Carbon::createFromFormat('d-m-Y', $dateEnd);

        // Create a CarbonPeriod instance
        $period = CarbonPeriod::create($start, $end);

        // Initialize counters
        $weekdays = 0;
        $weekends = 0;

        // Iterate through each date in the period
        foreach ($period as $date) {
            if ($date->isWeekend()) {
                $weekends++;
            } else {
                $weekdays++;
            }
        }

        return [
            'weekdays' => $weekdays,
            'weekends' => $weekends
        ];
    }

    function generateRandomKey($length) {
        $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }
}
