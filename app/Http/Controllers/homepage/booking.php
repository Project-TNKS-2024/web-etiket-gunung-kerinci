<?php

namespace App\Http\Controllers\homepage;

use App\Http\Controllers\Controller;
use App\Http\Controllers\helper\BookingHelperController;
use App\Http\Controllers\helper\BookingValidator;
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
use App\Models\pembayaran;
use App\Models\pengajuan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;



use function PHPUnit\Framework\isNull;

class booking extends Controller
{

    private $helper;
    private $upload;

    public function __construct(BookingHelperController $helper, UploadFileControlller $upload)
    {
        $this->helper = $helper;
        $this->upload = $upload;
    }

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

        // ambil data destinasi
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
                'unique_code' => $this->helper->generateCode(10),
                'keterangan' => null,
                'id_booking_master' => null,
            ]);

            return redirect()->route('homepage.booking.snk', ['id' => $newBooking->id])
                ->with('success', 'Create Booking');
        }
    }

    public function bookingId($id)
    {
        $booking = gk_booking::with('gktiket')->find($id);
        if (!$booking) {
            abort(404);
        }


        switch ($booking->status_booking) {
            case '1':
                return redirect()->route('homepage.booking.snk', ['id' => $id]);
                break;

            case '2':
                return redirect()->route('homepage.booking.formulir', ['id' => $id]);
                break;

            case '3':
                return redirect()->route('homepage.booking.payment', ['id' => $id]);
                break;

                // 4, 5, 6, 7

            default:
                return $booking;
                return redirect()->route('homepage.booking.destinasi.paket', ['id' => $id]);
                break;
        }
    }

    public function bookingSnk($id)
    {
        $booking = gk_booking::where('id_user', Auth::user()->id)->where('id', $id)->first();
        // ================================ cek booking sattus ============================================
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
            $booking->update(['status_booking' => 2]);
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
        // ================================ cek booking sattus ============================================
        $booking = gk_booking::with('gktiket')->where("id", $id)->first();

        if (!$booking) {
            abort(404);
        } elseif ($booking->status_booking > 3) {
            return redirect()->route('user.dashboard.reiwayat')->with('error', 'Booking telah dibayar');
        }

        $pendaki = gk_pendaki::where('booking_id', $booking->id)->get();
        $barang = gk_barang_bawaan::where('id_booking', $booking->id)->get();

        return view('homepage.booking.bookingFp', [
            'id' => $id,
            'booking' => $booking,
            'pendaki' => $pendaki,
            'barang' => $barang,
        ]);
    }

    public function bookingFPStore(Request $request)
    {
        // return $request;
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
            'surat_stugas' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
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

        if (strlen($booking->gktiket->penugasan) > 0) {
            if (isset($request->surat_stugas)) {
                $path = null;
                if (strlen($booking->lampiran_stugas) > 0) {
                    $path = $upload->upadate($booking->lampiran_stugas, $request->surat_stugas);
                } else {
                    $path = $upload->create($booking->id, 'booking', $request->surat_stugas);
                }
                $booking->lampiran_stugas = $path;

                $booking->save();
            }
        }

        foreach ($formulirPendakis as $key => $formulir) {
            $pendaki = gk_pendaki::find($formulir['id_pendaki']);

            // return $pendaki;

            if (!$pendaki) {
                $pendaki = new gk_pendaki();
                $pendaki->booking_id = $booking->id;
            }
            $pendaki->first_name = $formulir['first_name'] ?? '';
            $pendaki->last_name = $formulir['last_name'] ?? '';
            $pendaki->kategori_pendaki = $formulir['kewarganegaraan'] ?? '';
            $pendaki->nik = $formulir['identitas'] ?? '';
            $pendaki->jenis_kelamin = $formulir['jenis_kelamin'] ?? '';
            $pendaki->tanggal_lahir = $formulir['tanggal_lahir'] ?? '';
            // $pendaki->tinggi_badan = $formulir['tinggi_badan'];
            // $pendaki->berat_badan = $formulir['berat_badan'];
            $pendaki->no_hp = $formulir['no_hp'] ?? '';
            $pendaki->no_hp_darurat = $formulir['no_hp_darurat'] ?? '';
            $pendaki->provinsi = $formulir['provinsi'] ?? '';
            $pendaki->kabupaten = $formulir['kabupaten_kota'] ?? '';
            $pendaki->kec = $formulir['kecamatan'] ?? '';
            $pendaki->desa = $formulir['desa_kelurahan'] ?? '';


            if (isset($formulir['lampiran_identitas'])) {
                $path = "";
                if (strlen($pendaki->lampiran_identitas) > 0) {
                    $upload->delete($pendaki->lampiran_identitas);
                    $path = $upload->create($booking->id, 'booking', $formulir['lampiran_identitas']);
                } else {
                    $path = $upload->create($booking->id, 'booking', $formulir['lampiran_identitas']);
                }
                $pendaki->lampiran_identitas = $path;
            }

            if (isset($formulir['surat_izin_ortu'])) {
                $path = "";
                if (strlen($pendaki->lampiran_surat_izin_ortu) > 0) {
                    $upload->delete($pendaki->lampiran_surat_izin_ortu);
                    $path = $upload->create($booking->id, 'booking', $formulir['surat_izin_ortu']);
                } else {
                    $path = $upload->create($booking->id, 'booking', $formulir['surat_izin_ortu']);
                }
                $pendaki->lampiran_surat_izin_ortu = $path;
            }

            if (isset($formulir['surat_keterangan_sehat'])) {
                $path = "";
                if (strlen($pendaki->lampiran_surat_kesehatan) > 0) {
                    $upload->delete($pendaki->lampiran_surat_kesehatan);
                    $path = $upload->create($booking->id, 'booking', $formulir['surat_keterangan_sehat']);
                } else {
                    $path = $upload->create($booking->id, 'booking', $formulir['surat_keterangan_sehat']);
                }
                $pendaki->lampiran_surat_kesehatan = $path;
            }

            // return $pendaki;
            $pendaki->save();
        }

        if ($request->action == 'next') {

            $request->validate([
                'barangWajib' => 'required|array',
                'barangWajib.perlengkapan_gunung_standar' => 'required|boolean',
                'barangWajib.trash_bag' => 'required|boolean',
                'barangWajib.p3k_standart' => 'required|boolean',
                'barangWajib.survival_kit_standart' => 'required|boolean',
            ]);

            // // validasi falid daya booking
            // $BookingValidator = new BookingValidator($booking->id);
            // $validasi =  $BookingValidator->validate();

            // if (!$validasi['success']) {
            //     return redirect()->back()->withErrors($validasi['message']);
            // }

            return redirect()->route('homepage.booking.detail', ['id' => $booking->id])->with('Data Booking Berhasil disimpan');

            // pindah laman 
        } else if ($request->action == 'save') {
            return redirect()->back()->with('success', 'Data berhasil disimpan');
        }

        return redirect()->back()->withErrors('error', 'Action tidak falid');
    }
    public function bookingDetail($id)
    {
        // cek booking
        $booking = gk_booking::with(['gateMasuk', 'gateMasuk.destinasi', 'gateKeluar', 'pendakis'])
            ->where('id', $id)
            ->where('id_user', Auth::id())
            ->first();

        // return $booking;
        // cek status booking
        if (!$booking) {
            abort(404);
        } else if ($booking->status_booking !== 3) {
            return redirect()->route("homepage.booking", ["id" => $id]);
        }

        $pendakis = gk_pendaki::where('booking_id', $booking->id)->get();

        // return $booking;
        return view('homepage.booking.bookingDetail', [
            'booking' => $booking,
            'formulirPendakis' => $booking->pendakis,
            'pendakis' => $pendakis,
        ]);
    }

    public function bookingPayment($id)
    {
        // cek booking
        $booking = gk_booking::with(['gateMasuk', 'gateKeluar', 'pendakis'])
            ->where('id', $id)
            ->where('id_user', Auth::id())
            ->first();

        // cek status booking
        if (!$booking) {
            abort(404);
        } else if ($booking->status_booking == 2) {
            // validasi formulir pendaki'
            $BookingValidator = new BookingValidator($booking->id);
            $validasi = $BookingValidator->validate();

            // return $validasi;

            if ($validasi['success']) {
                $booking->status_booking = 3;
                $booking->save();
            } else {
                return redirect()->route('homepage.booking.formulir', ['id' => $id])->withErrors($validasi['message']);
            }
        } else if ($booking->status_booking !== 3) {
            return redirect()->route("homepage.booking", ["id" => $id]);
        }

        // cek id transaksi booking
        $midtrans = new MidtransController;
        $snapToken = '';
        $traksaksi = pembayaran::where('id_booking', $booking->id)->latest()->first();

        if ($traksaksi) {
            $status = $midtrans->checkPaymentStatus($traksaksi->id);
            $statusTranskasi = $status['data']['status_code'];

            // return $status;
            if ($statusTranskasi == 200) {
                return redirect()->route('dashboard.tiket', ['id' => $id])->with('success', 'Pembayaran Berhasil');
            } else if ($statusTranskasi == 404) {
                $params = array(
                    'transaction_details' => array(
                        'order_id' => $traksaksi->id,
                        'gross_amount' => $booking->total_pembayaran,
                    ),
                    'customer_details' => array(
                        'first_name' => $booking->pendakis[0]->first_name,
                        'last_name' =>  $booking->pendakis[0]->last_name,
                        'email' => Auth::user()->email,
                        'phone' => Auth::user()->no_hp
                    ),
                );

                $data = $midtrans->generateSnapToken($params);
                $snapToken = $data['data'];

                $traksaksi->spesial = $snapToken;
                $traksaksi->save();
            } else if ($statusTranskasi == 407) {
                // return "transaksi belum di bayar";
                $traksaksi->status = 'failed';
                $traksaksi->save();
                $traksaksi = pembayaran::create([
                    'id_booking' => $booking->id,
                    'spesial' => '',
                    'amount' => $booking->total_pembayaran,
                    'status' => 'pending',
                    'payment_method' => 'midtrans',
                    'deadline' => date('Y-m-d H:i:s', strtotime('+1 day'))
                ]);

                $params = array(
                    'transaction_details' => array(
                        'order_id' => $traksaksi->id,
                        'gross_amount' => $booking->total_pembayaran,
                    ),
                    'customer_details' => array(
                        'first_name' => $booking->pendakis[0]->first_name,
                        'last_name' =>  $booking->pendakis[0]->last_name,
                        'email' => Auth::user()->email,
                        'phone' => Auth::user()->no_hp
                    ),
                );
                $data = $midtrans->generateSnapToken($params);
                $snapToken = $data['data'];
                $traksaksi->spesial = $snapToken;
                $traksaksi->save();
            } else if ($statusTranskasi == 201) {
                // return "transaksi pennding";
                $snapToken = $traksaksi->spesial;
            }
        } else {
            $traksaksi = pembayaran::create([
                'id_booking' => $booking->id,
                'spesial' => '',
                'amount' => $booking->total_pembayaran,
                'status' => 'pending',
                'payment_method' => 'midtrans',
                'deadline' => date('Y-m-d H:i:s', strtotime('+1 day'))
            ]);

            $params = array(
                'transaction_details' => array(
                    'order_id' => $traksaksi->id,
                    'gross_amount' => $booking->total_pembayaran,
                ),
                'customer_details' => array(
                    'first_name' => $booking->pendakis[0]->first_name,
                    'last_name' =>  $booking->pendakis[0]->last_name,
                    'email' => Auth::user()->email,
                    'phone' => Auth::user()->no_hp
                ),
            );
            // bikin snaop token
            $data = $midtrans->generateSnapToken($params);
            $snapToken = $data['data'];
            $traksaksi->spesial = $snapToken;
            $traksaksi->save();
        }

        // return $booking;

        return view('homepage.booking.bookingPayment', [
            'snaptoken' => $snapToken,
            'booking' => $booking,
            'pendakis' => $booking->pendakis
        ]);
    }

    // =========================================================================================

    public function tiket($id)
    {
        $booking = gk_booking::with(['gateMasuk', 'gateKeluar', 'pendakis'])->where('id', $id)->first();
        // cek status booking

        // return $booking;


        if (!$booking) {
            abort(404);
        } else if ($booking->status_booking < 3) {
            abort(404);
        }


        return view('homepage.booking.bookingTiket', [
            'booking' => $booking,
            'pendakis' => $booking->pendakis
        ]);
    }

    public function addBuktiPembayaran(Request $request, $id)
    {
        $request->validate([
            'bukti_pembayaran' => 'required|file|mimes:jpg,jpeg,png,pdf|max:2048'
        ]);

        $pengajuan = new pengajuan();
        $pengajuan->booking_id = $id;
        $path = $this->upload->create($id, 'booking', $request->bukti_pembayaran);
        $pengajuan->bukti = $path;
        $pengajuan->status = 'pending';
        $pengajuan->keterangan = '';
        $pengajuan->save();

        return redirect()->back()->with('success', 'Bukti pembayaran berhasil dikirim');
    }

    public function deleteBuktiPembayaran($id, $pengajuan_id)
    {
        $pengajuan = pengajuan::find($pengajuan_id);
        $pengajuan->delete();
        return redirect()->back()->with('success', 'Bukti pembayaran berhasil dihapus');
    }
}
