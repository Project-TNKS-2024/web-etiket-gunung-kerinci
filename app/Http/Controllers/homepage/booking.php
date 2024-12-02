<?php

namespace App\Http\Controllers\homepage;

use App\Http\Controllers\Controller;
use App\Http\Controllers\helper\BookingHelperController;
use App\Http\Controllers\helper\MidtransController;
use App\Http\Controllers\helper\uploadFileControlller;
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

    public function destinasiList()
    {
        // ambil destinasis yang open
        $destinasi = destinasi::where('status', 1)->with('gambar_destinasi')->get();
        // return $destinasi;
        return view('homepage.booking.bookingDestinasiList', [
            "destinasi" => $destinasi
        ]);
    }
    // booking : destinasi - paket - tiket - snk - fp -
    public function destinasiPaket($id)
    {
        // id  = id destinasi
        $gunung = destinasi::find($id);
        $gambar_destinasi = gambar_destinasi::where('id_destinasi', $id)->get();
        // paket tiket
        $paket = gk_paket_tiket::where('id_destinasi', $id)->get();

        return view('homepage.booking.bookingDestinasiPaket', [
            "gunung" => $gunung,
            "paket" => $paket,
            "gambar" => $gambar_destinasi,
        ]);
    }

    public function destinasiTiket($id)
    {
        // id = id paket
        $paket = gk_paket_tiket::where("id", $id)->first();

        // // ambil data destinasi
        $id_destinasi = $paket->id_destinasi;
        $destinasi = destinasi::with('gates')->where('id', $id_destinasi)->first();
        $gambar_destinasi = gambar_destinasi::where('id_destinasi', $id_destinasi)->get();

        // ambil data tiket
        $tiket = gk_tiket_pendaki::where('id_paket_tiket', $id)->with(['paket_tiket'])->get();

        return view("homepage.booking.bookingDestinasiTiket", [
            "paket" => $paket,
            "destinasi" => $destinasi,
            "gambar" => $gambar_destinasi,
            "tiket" => $tiket,
        ]);
    }

    public function destinasiTiketStore(Request $request)
    {
        // Pastikan pengguna sudah login dan memiliki peran 'user'
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        if (Auth::user()->role != "user") {
            return redirect()->route("homepage.beranda");
        }

        // Validasi request
        $request->validate([
            'date_start' => 'required|date',
            'date_end' => 'required|date',
            'wni' => 'required|numeric',
            'wna' => 'required|numeric',
            'jenis_tiket' => 'required|string',
            'gerbang_masuk' => 'required',
            'gerbang_keluar' => 'required',
        ]);

        // Parse tanggal mulai dan selesai
        $dateStart = Carbon::createFromFormat('Y-m-d', $request->date_start);
        $dateEnd = Carbon::createFromFormat('Y-m-d', $request->date_end);
        $totalDays = $dateStart->diffInDays($dateEnd);

        // Validasi tanggal mulai tidak boleh lebih besar dari tanggal selesai
        if ($dateStart > $dateEnd) {
            return back()->with('error', 'Error: Tanggal tidak sesuai');
        }

        // Validasi jumlah pendaki minimal 2 orang
        if ($request->wni + $request->wna < 2) {
            return back()->with('error', 'Error: Jumlah pendaki tidak mencukupi');
        }

        // Cari booking yang belum selesai dari user yang sedang login
        $booking = gk_booking::where('id_user', Auth::user()->id)
            ->where('status_booking', '<', 3)
            ->orderBy('created_at', 'desc')
            ->first();

        // Update atau buat booking baru
        if ($booking) {
            // Update booking yang ada
            $booking->update([
                'total_pendaki' => $request->wni + $request->wna,
                'total_pendaki_wni' => $request->wni,
                'total_pendaki_wna' => $request->wna,
                'gate_masuk' => $request->gerbang_masuk,
                'gate_keluar' => $request->gerbang_keluar,
                'tanggal_masuk' => $dateStart,
                'tanggal_keluar' => $dateEnd,
            ]);

            return redirect()->route('homepage.booking.snk', ['id' => $booking->id])
                ->with('success', 'Update Booking');
        } else {
            // Buat booking baru
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
                'lampiran_stugas' => null,
                'unique_code' => null,
                'keterangan' => null,
                'id_booking_master' => null,
            ]);

            return redirect()->route('homepage.booking.snk', ['id' => $newBooking->id])
                ->with('success', 'Create Booking');
        }
    }

    public function bookingSnk($id)
    {
        $booking = gk_booking::where('id_user', Auth::user()->id)->where('id', $id)->first();
        // return $booking;
        if (!$booking) {
            abort(404);
        } elseif ($booking->status_booking > 3) {
            return redirect()->back()->with('error', 'Booking telah dibayar');
        }

        return view('homepage.booking.bookingSnk', ['id' => $id, 'status' => false]);
    }

    public function bookingSnkStore(Request $request)
    {
        if ($request->snk) {
            $booking = gk_booking::find($request->id);
            $booking->update(['status_booking' => 1]);
            return redirect()->route('homepage.booking.formulir', ['id' => $request->id]);
        } else {
            return back()->withErrors(['snk' => 'Silahkan ceklis data diri anda']);
        }
    }

    public function bookingFP($id)
    {
        if (!Auth::check()) {
            abort(403);
        }
        $booking = gk_booking::with('gktiket')->where("id", $id)->first();

        if (!$booking) {
            abort(404);
        } elseif ($booking->status_booking > 3) {
            return redirect()->route('user.dashboard.reiwayat')->with('error', 'Booking telah dibayar');
        }

        $pendaki = gk_pendaki::where('booking_id', $booking->id)->get();
        $barang = gk_barang_bawaan::where('id_booking', $booking->id)->get();

        // return $pendaki;

        // return $booking;

        return view('homepage.booking.bookingFp', [
            'id' => $id,
            'booking' => $booking,
            'pendaki' => $pendaki,
            'barang' => $barang,
        ]);
    }

    public function bookingFPStore(Request $request)
    {
        $request->validate([
            'id_booking' => 'required|string',
            'action' => 'required|string',

            'formulir' => 'required|array',

            'formulir.*.id_pendaki' => 'nullable|string',
            'formulir.*.first_name' => 'nullable|string',
            'formulir.*.last_name' => 'nullable|string',
            'formulir.*.kewarganegaraan' => 'nullable|string',
            'formulir.*.identitas' => 'nullable|string',

            'formulir.*.jenis_kelamin' => 'nullable|string',
            'formulir.*.tanggal_lahir' => 'nullable|date',
            // 'formulir.*.tinggi_badan' => 'nullable|numeric',
            // 'formulir.*.berat_badan' => 'nullable|numeric',

            'formulir.*.no_hp' => 'nullable|string',
            'formulir.*.no_hp_darurat' => 'nullable|string',

            'formulir.*.provinsi' => 'nullable|string',
            'formulir.*.kabupaten_kota' => 'nullable|string',
            'formulir.*.kecamatan' => 'nullable|string',
            'formulir.*.desa_kelurahan' => 'nullable|string',

            'formulir.*.lampiran_identitas' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'formulir.0.surat_stugas' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'formulir.*.surat_izin_ortu' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'formulir.*.surat_keterangan_sehat' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',

            'barangWajib' => 'nullable|array',
            'barangWajib.perlengkapan_gunung_standar' => 'nullable|boolean',
            'barangWajib.trash_bag' => 'nullable|boolean',
            'barangWajib.p3k_standart' => 'nullable|boolean',
            'barangWajib.survival_kit_standart' => 'nullable|boolean',
        ]);

        $booking = gk_booking::with('gktiket')->find($request->id_booking);
        $formulirPendakis = $request->formulir;
        $upload = new uploadFileControlller();

        foreach ($formulirPendakis as $key => $formulir) {
            $pendaki = gk_pendaki::find($formulir['id_pendaki']);

            // return $pendaki;

            if (!$pendaki) {
                $pendaki = new gk_pendaki();
                $pendaki->booking_id = $booking->id;
            }

            $pendaki->first_name = $formulir['first_name'];
            $pendaki->last_name = $formulir['last_name'];
            $pendaki->kategori_pendaki = $formulir['kewarganegaraan'];
            $pendaki->nik = $formulir['identitas'];
            $pendaki->jenis_kelamin = $formulir['jenis_kelamin'];
            $pendaki->tanggal_lahir = $formulir['tanggal_lahir'];
            // $pendaki->tinggi_badan = $formulir['tinggi_badan'];
            // $pendaki->berat_badan = $formulir['berat_badan'];
            $pendaki->no_hp = $formulir['no_hp'];
            $pendaki->no_hp_darurat = $formulir['no_hp_darurat'];
            $pendaki->provinsi = $formulir['provinsi'];
            $pendaki->kabupaten = $formulir['kabupaten_kota'];
            $pendaki->kec = $formulir['kecamatan'];
            $pendaki->desa = $formulir['desa_kelurahan'];

            if (isset($formulir['lampiran_identitas'])) {
                $path = null;
                if (strlen($pendaki->lampiran_identitas) > 0) {
                    $path = $upload->upadate($pendaki->lampiran_identitas, $formulir['lampiran_identitas']);
                } else {
                    $path = $upload->create($booking->id, 'booking', $formulir['lampiran_identitas']);
                }
                $pendaki->lampiran_identitas = $path;
            }

            if (isset($formulir['surat_izin_ortu'])) {
                $path = null;
                if (strlen($pendaki->lampiran_surat_izin_ortu) > 0) {
                    $path = $upload->upadate($pendaki->lampiran_surat_izin_ortu, $formulir['surat_izin_ortu']);
                } else {
                    $path = $upload->create($booking->id, 'booking', $formulir['surat_izin_ortu']);
                }
                $pendaki->lampiran_surat_izin_ortu = $path;
            }

            if (isset($formulir['surat_keterangan_sehat'])) {
                $path = null;
                if (strlen($pendaki->lampiran_surat_kesehatan) > 0) {
                    $path = $upload->upadate($pendaki->lampiran_surat_kesehatan, $formulir['surat_keterangan_sehat']);
                } else {
                    $path = $upload->create($booking->id, 'booking', $formulir['surat_keterangan_sehat']);
                }
                $pendaki->lampiran_surat_kesehatan = $path;
            }
            $pendaki->save();
        }

        if ($request->action == 'next') {

            return "okw";
        } else if ($request->action == 'save') {
            return redirect()->back()->with('success', 'Data berhasil disimpan');
        }

        return redirect()->back()->withErrors('error', 'Action tidak falid');
    }
    // =========================================================================================

    public function bookingDetail($id)
    {
        // id booking
        $booking = gk_booking::with(['gateMasuk', 'gateKeluar', 'pendakis'])->where('id', $id)->first();
        $user = Auth::user();
        if (!$booking) {
            abort(404);
        }

        if ($booking->status_booking == 0) {
            return redirect()->route("homepage.booking.snk", ["id" => $id]);
        }

        // validasi booking
        $ClassHelper = new BookingHelperController();
        $ClassHelper->validasiBooking($booking->id);

        // jika validasi gagal kembali ke halaman booking fp

        $booking = gk_booking::with(['gateMasuk', 'gateKeluar', 'pendakis'])->where('id', $id)->first();
        $booking->update([
            'status_booking' => 3
        ]);
        $ketua = $booking->pendakis[0];

        $params = array(
            'transaction_details' => array(
                'order_id' => $booking->id,
                // 'order_id' => rand(),
                'gross_amount' => $booking->total_pembayaran,
            ),
            'customer_details' => array(
                'first_name' => $ketua->nama,
                'last_name' => $user->fullname,
                'email' => $user->email,
                'phone' => $user->no_hp
            ),
        );

        $midtrans = new MidtransController;
        $snapToken = $midtrans->generateSnapToken($params);

        return view('homepage.booking.booking-detail', [
            'snaptoken' => $snapToken,
            'booking' => $booking,
            'pendakis' => $booking->pendakis
        ]);
    }

    public function bookingPayment($id)
    {
        $booking = gk_booking::with(['gateMasuk', 'gateKeluar', 'pendakis'])->where('id', $id)->first();
        $statusPayment = [
            'status' => 000,
            'message' => 'Data tidak ditemukan',
            'redirect_name' => 'Booking Tiket',
            'redirect_url' => route('homepage.booking.destinasi.paket', ['id' => 1]),
        ];


        if (isset($booking)) {
            $midtrans = new MidtransController;
            $status = $midtrans->checkPaymentStatus($booking->id);

            // return $status;
            if (isset($status->getData()->status_code)) {
                if ($status->getData()->status_code == 200) {
                    $helper = new BookingHelperController();
                    $qr = $helper->generateUniqueCode($booking->id);
                    $booking->update([
                        'status_booking' => 4,
                        'status_pembayaran' => 1,
                        'unique_code' => $qr->getData()->data,
                    ]);
                    $statusPayment = [
                        'status' => 200,
                        'message' => 'Pembayaran berhasil',
                        'redirect_name' => 'Cek Tiket',
                        'redirect_url' => route('homepage.booking.tiket', ['id' => $booking->id])
                    ];
                } else if ($status->getData()->status_code == 201) {
                    $statusPayment = [
                        'status' => 201,
                        'message' => 'Pembayaran Pending',
                        'redirect_name' => 'Cek Pembayaran',
                        'redirect_url' => route('homepage.booking.detail', ['id' => $booking->id])
                    ];
                } else if ($status->getData()->status_code == 407) {
                    $statusPayment = [
                        'status' => 407,
                        'message' => 'Pembayaran Galal',
                        'redirect_name' => 'Cek Pembayaran',
                        'redirect_url' => route('homepage.booking.detail', ['id' => $booking->id])
                    ];
                } else {
                    $statusPayment = [
                        'status' => 404,
                        'message' => 'Pembayaran Tidak Ditemukan',
                        'redirect_name' => 'Cek Pembayaran',
                        'redirect_url' => route('homepage.booking.detail', ['id' => $booking->id])
                    ];
                }
            } else {
                $statusPayment = [
                    'status' => 404,
                    'message' => 'Booking Tidak Ditemukan',
                    'redirect_name' => 'Pesan Tiket',
                    'redirect_url' => route('homepage.booking.destinasi.paket', ['id' => 1]),
                ];
            }
        }
        // return $statusPayment['status'];
        return view('homepage.booking.booking-payment', [
            'booking' => $booking,
            'status' => $status,
            'statusPayment' => $statusPayment
        ]);
    }
    public function tiketBooking($id)
    {
        $booking = gk_booking::with(['gateMasuk', 'gateKeluar', 'pendakis'])->where('id', $id)->first();
        if ($booking->status_pembayaran > 4) {
            abort(404);
        }
        // return [
        //     'booking' => $booking,
        //     'pendakis' => $booking->pendakis
        // ];
        return view('homepage.booking.booking-tiket', [
            'booking' => $booking,
            'pendakis' => $booking->pendakis
        ]);
    }
}
