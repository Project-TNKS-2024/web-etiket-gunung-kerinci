<?php

namespace App\Http\Controllers\homepage;

use App\Http\Controllers\Controller;
use App\Http\Controllers\helper\BookingHelperController;
use App\Http\Controllers\helper\uploadFileControlller;
use App\Models\bio_pendaki;
use App\Models\destinasi;
use App\Models\gk_barang_bawaan;
use App\Models\gk_booking;
use App\Models\gk_gates;
use App\Models\gk_pendaki;
use App\Models\gk_tiket_pendaki;
use App\Models\gambar_destinasi;
use App\Models\gk_paket_tiket;
use App\Models\pembayaran;
use App\Models\User;
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
            'destinasi' => $destinasi,
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
            'gunung' => $gunung,
            'paket' => $paket,
            'gambar' => $gambar_destinasi,
        ]);
    }

    public function destinasiTiket($id)
    {
        // id = id paket
        $paket = gk_paket_tiket::where('id', $id)->first();

        // ambil data destinasi
        $id_destinasi = $paket->id_destinasi;
        $destinasi = destinasi::with('gates')->where('id', $id_destinasi)->first();
        $gambar_destinasi = gambar_destinasi::where('id_destinasi', $id_destinasi)->get();

        // ambil data tiket
        $tiket = gk_tiket_pendaki::where('id_paket_tiket', $id)
            ->with(['paket_tiket'])
            ->get();

        return view('homepage.booking.bookingDestinasiTiket', [
            'paket' => $paket,
            'destinasi' => $destinasi,
            'gambar' => $gambar_destinasi,
            'tiket' => $tiket,
        ]);
    }

    public function destinasiTiketStore(Request $request)
    {

        $request->validate([
            'date_start' => 'required|date',
            'date_end' => 'required|date',
            'wni' => 'required|numeric',
            'wna' => 'required|numeric',
            'jenis_tiket' => 'required|string',
            'gerbang_masuk' => 'required',
            'gerbang_keluar' => 'required',
        ]);

        // cek user verified
        $user = User::where('id', Auth::user()->id)
            ->with('biodata')->first();

        // return $user;
        if (!$user->biodata or $user->biodata->verified != 'verified') {
            return redirect()->route('user.dashboard.profile')->with('error', 'Biodata anda belum terverifikasi');
        }


        $dateStart = Carbon::createFromFormat('Y-m-d', $request->date_start);
        $dateEnd = Carbon::createFromFormat('Y-m-d', $request->date_end);
        $totalDays = $dateStart->diffInDays($dateEnd) + 1;

        if ($dateStart > $dateEnd) {
            return back()->with('error', 'Error: Tanggal tidak sesuai');
        }

        if ($request->wni + $request->wna < 2) {
            return back()->with('error', 'Error: Jumlah pendaki tidak mencukupi');
        }

        $booking = gk_booking::where('id_user', Auth::user()->id)
            ->where('status_booking', '<', 3)
            ->orderBy('created_at', 'desc')
            ->first();

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

            // reset pendaki
            $total_pendaki = $request->wni + $request->wna;
            $pendaki = gk_pendaki::where('booking_id', $booking->id)->get();
            if ($pendaki->count() > $total_pendaki) {
                $pendaki->take($total_pendaki)->each(function ($pendaki) {
                    $pendaki->delete();
                });
            }

            return redirect()
                ->route('homepage.booking.snk', ['id' => $booking->id])
                ->with('success', 'Update Booking');
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
                'lampiran_stugas' => null,
                'unique_code' => null,
                'keterangan' => null,
                'id_booking_master' => null,
            ]);

            return redirect()
                ->route('homepage.booking.snk', ['id' => $newBooking->id])
                ->with('success', 'Create Booking');
        }
    }

    public function bookingId($id)
    {
        // cek booking
        $booking = gk_booking::with('gktiket')->find($id);
        if (!$booking) {
            abort(404);
        }

        switch ($booking->status_booking) {
            case '0':
                return redirect()->route('homepage.booking.snk', ['id' => $id]);
                break;

            case '1':
                return redirect()->route('homepage.booking.snk', ['id' => $id]);
                break;

            case '2':
                return redirect()->route('homepage.booking.formulir', ['id' => $id]);
                break;

            case '3':
                return redirect()->route('homepage.booking.payment', ['id' => $id]);
                break;

            default:
                return redirect()->route('dashboard.tiket', ['id' => $id]);
                break;
        }
    }

    public function bookingSnk($id)
    {
        $booking = gk_booking::with('gktiket')->where('id_user', Auth::user()->id)->where('id', $id)->first();
        if (!$booking) {
            abort(404);
        } elseif ($booking->status_booking > 3) {
            return redirect(route('homepage.booking', ['id' => $id]));
        }
        $destinasi = destinasi::where('id', $booking->gktiket->id_destinasi)->first();
        // return $destinasi;
        // return $booking;
        return view('homepage.booking.bookingSnk', [
            'id' => $id,
            'destinasi' => $destinasi,
            'status' => $booking->status_booking,
        ]);
    }

    public function bookingSnkStore(Request $request)
    {
        $request->validate([
            'id' => 'required|string',
            'snk' => 'required|string',
        ]);

        if (!$request->snk) {
            return back()->withErrors(['snk' => 'Silahkan ceklis data diri anda']);
        }

        $booking = gk_booking::where('id_user', Auth::user()->id)->where('id', $request->id)->first();
        if (!$booking) {
            abort(404);
        }
        if ($booking->status_booking > 3) {
            return redirect()->route('homepage.booking', ['id' => $request->id]);
        }

        $booking->update(['status_booking' => 2]);
        return redirect()->route('homepage.booking.formulir', ['id' => $request->id]);
    }

    public function bookingFP($id)
    {
        $booking = gk_booking::with(['gateMasuk', 'gateKeluar', 'pendakis', 'gktiket'])
            ->where('id', $id)
            ->where('id_user', Auth::id())
            ->first();

        if (!$booking) {
            abort(404);
        } elseif ($booking->status_booking != 2) {
            return redirect()->route('homepage.booking', ['id' => $id]);
        }

        // ================================ cek ketua pendaki ============================================
        $pendaki = gk_pendaki::where('booking_id', $booking->id)
            ->with('biodata')
            ->get();

        $userBio = bio_pendaki::find(Auth::user()->id_bio);
        $userUsia = Carbon::parse($userBio->tanggal_lahir)->age;
        if ($pendaki->count() == 0) {
            gk_pendaki::create([
                'booking_id' => $booking->id,
                'tagihan' => 0,
                'id_bio' => $userBio->id,
                'usia' => $userUsia,
                'lampiran_surat_izin_ortu' => null,
            ]);

            $pendaki = gk_pendaki::where('booking_id', $booking->id)
                ->with('biodata')
                ->get();
        }

        $barang = gk_barang_bawaan::where('id_booking', $booking->id)->get();

        // return $pendaki;
        return view('homepage.booking.bookingFp', [
            'id' => $id,
            'booking' => $booking,
            'pendaki' => $pendaki,
            'barang' => $barang,
        ]);
    }

    public function bookingPendakiAdd(Request $request)
    {

        // return $request;
        $request->validate([
            'code' => 'required|string',
            'booking' => 'required|string',
            'id' => 'string|nullable',
        ]);

        $booking = gk_booking::with(['gateMasuk', 'gateKeluar', 'pendakis'])
            ->where('id', $request->booking)
            ->where('id_user', Auth::id())
            ->first();

        // return $booking;
        if (!$booking) {
            abort(404);
        } elseif ($booking->status_booking != 2) {
            return redirect()->route('homepage.booking', ['id' => $request->id]);
        }

        $bioPendaki = bio_pendaki::where('id', $request->code)
            ->where('verified', 'verified')
            ->first();

        if ($bioPendaki == null) {
            return back()->withErrors(['code' => 'Kode tidak ditemukan']);
        }

        $pendaki = gk_pendaki::with('booking')
            ->where('id_bio', $bioPendaki->id)
            ->whereHas('booking', function ($query) {
                $query->where('status_booking', '<', 7);
            })
            ->first();

        if ($pendaki) {
            return back()->withErrors(['code' => 'Kode sudah terdaftar dalam pendakian lain dan belum menyelesaikannya']);
        } else {
            $userUsia = Carbon::parse($bioPendaki->tanggal_lahir)->age;

            if ($request->id != null) {
                $pendaki = gk_pendaki::find($request->id);
                $pendaki->update([
                    'id_bio' => $bioPendaki->id,
                    'usia' => $userUsia,
                    'lampiran_surat_izin_ortu' => null,
                ]);
            } else {
                gk_pendaki::create([
                    'booking_id' => $request->booking,
                    'tagihan' => 0,
                    'id_bio' => $bioPendaki->id,
                    'usia' => $userUsia,
                    'lampiran_surat_izin_ortu' => null,
                ]);
            }
        }

        return back()->with('success', 'Berhasil menambahkan pendaki');
    }

    public function bookingFPStore(Request $request)
    {
        // return $request;
        // dd($this->helper->generateCode(10));
        $request->validate([
            'id_booking' => 'required|string',
            'action' => 'required|string',

            'formulir' => 'required|array',

            'formulir.*.id_pendaki' => 'nullable|string',
            'formulir.*.kode_bio' => 'nullable|string',
            'formulir.*.no_hp_darurat' => 'nullable|string',
            'formulir.*.surat_izin_ortu' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',

            'barangWajib' => 'nullable|array',
            'barangWajib.perlengkapan_gunung_standar' => 'nullable|boolean',
            'barangWajib.trash_bag' => 'nullable|boolean',
            'barangWajib.p3k_standart' => 'nullable|boolean',
            'barangWajib.survival_kit_standart' => 'nullable|boolean',
        ]);

        $booking = gk_booking::with(['gateMasuk', 'gateKeluar', 'pendakis'])
            ->where('id', $request->id_booking)
            ->where('id_user', Auth::id())
            ->first();

        if (!$booking) {
            abort(404);
        } elseif ($booking->status_booking != 2) {
            return redirect()->route('homepage.booking', ['id' => $request->id]);
        }

        $formulirPendakis = $request->formulir;
        $upload = new uploadFileControlller();

        foreach ($formulirPendakis as $key => $formulir) {
            $pendaki = gk_pendaki::find($formulir['id_pendaki']);
            $bioPendaki = bio_pendaki::find($formulir['kode_bio']);

            if (!$bioPendaki) {
                return back()->withErrors(['formulir' => 'Bio pendaki tidak ditemukan']);
            }
            $bioPendaki->no_hp_darurat = $formulir['no_hp_darurat'] ?? '';
            $bioPendaki->save();

            if (!$pendaki) {
                return back()->withErrors(['formulir' => 'Pendaki tidak ditemukan']);
            }

            if (isset($formulir['surat_izin_ortu'])) {
                $path = '';
                if (strlen($pendaki->lampiran_surat_izin_ortu) > 0) {
                    $upload->delete($pendaki->lampiran_surat_izin_ortu);
                    $path = $upload->create($booking->id, 'booking', $formulir['surat_izin_ortu']);
                } else {
                    $path = $upload->create($booking->id, 'booking', $formulir['surat_izin_ortu']);
                }
                $pendaki->lampiran_surat_izin_ortu = $path;
            }

            $pendaki->tagihan = $this->helper->getTagihanPendaki($pendaki);
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

            $booking->status_booking = 3;
            $booking->save();

            return redirect()->route('homepage.booking.detail', ['id' => $booking->id])->with('Data Booking Berhasil disimpan');

            // pindah laman
        } elseif ($request->action == 'save') {
            return redirect()->back()->with('success', 'Data berhasil disimpan');
        }

        return redirect()->back()->withErrors('error', 'Action tidak falid');
    }
    public function bookingDetail($id)
    {

        // cek booking
        $booking = gk_booking::with(['gateMasuk', 'gateKeluar', 'pendakis'])
            ->where('id', $id)
            ->where('id_user', Auth::id())
            ->first();

        if (!$booking) {
            abort(404);
        } else if ($booking->status_booking != 3) {
            return redirect()->route('homepage.booking', ['id' => $id]);
        }

        $booking->total_pembayaran = gk_pendaki::where('booking_id', $booking->id)->sum('tagihan');

        $hitung = function ($param) {
            return $this->helper->getDetailTagihan($param);
        };

        return view('homepage.booking.bookingDetail', [
            // 'snaptoken' => $snapToken,
            'booking' => $booking,
            'formulirPendakis' => $booking->pendakis,
            'hitung' => $hitung
            // 'pendakis' => $booking->pendakis,
        ]);
    }

    public function bookingCancel($id)
    {
        $booking = gk_booking::with(['gateMasuk', 'gateKeluar', 'pendakis'])
            ->where('id', $id)
            ->where('id_user', Auth::id())
            ->first();

        if (!$booking) {
            abort(404);
        } elseif ($booking->status_booking != 3) {
            return redirect()->route('homepage.booking', ['id' => $id]);
        }

        // hapus pembayaran
        $pembayaran = pembayaran::where('id_booking', $id)->get();

        // return $pembayaran;
        if ($pembayaran->isEmpty()) {
            // return redirect()->back()->withErrors(['error' => 'Pembayaran tidak ditemukan']);

            $booking->status_booking = 2;
            // return redirect()->route('homepage.booking.formulir', ['id' => $id]);
        }

        foreach ($pembayaran as $payment) {
            if ($payment->status == 'success') {
                return redirect()->back()->withErrors(['error' => 'Pembayaran sudah dibayar']);
            }
            $payment->delete();
        }

        $booking->status_booking = 2;
        $booking->save();
        return redirect(route('homepage.booking.formulir', ['id' => $id]));
    }

    public function bookingPayment($id)
    {
        $booking = gk_booking::with(['gateMasuk', 'gateKeluar', 'gateMasuk.destinasi', 'pendakis'])
            ->where('id', $id)
            ->where('id_user', Auth::id())
            ->first();

        // return $booking;

        if (!$booking) {
            abort(404);
        }
        if ($booking->status_booking < 3) {
            return redirect()->route('homepage.booking', ['id' => $id]);
        }

        $booking->total_pembayaran = gk_pendaki::where('booking_id', $id)->sum('tagihan');
        $pembayaran = pembayaran::where('id_booking', $id)->get();


        // Check if any 'status' in pembayaran is 'approved'
        $verified = $pembayaran->contains('status', 'approved'); // true if at least one 'status' is 'approved'
        $qris = gk_gates::where('id', $booking->gate_masuk)->first()->qris;


        // dd($verified);
        return view('homepage.booking.bookingPayment', [
            'qris' => $qris,
            'booking' => $booking,
            'pendakis' => $booking->pendakis,
            'pembayaran' => $pembayaran,
            'verified' => $verified, // Pass the verified value to the view

        ]);
    }

    public function addBuktiPembayaran(Request $request)
    {
        $request->validate([
            'id' => 'required|string',
            'metode' => 'required|string',
            'bukti_pembayaran' => 'required|file|mimes:jpg,jpeg,png,pdf|max:2048'
        ]);
        $booking = gk_booking::with(['gateMasuk', 'gateKeluar', 'pendakis'])
            ->where('id', $request->id)
            ->where('id_user', Auth::id())
            ->first();

        if (!$booking) {
            abort(404);
        } elseif ($booking->status_booking != 3) {
            return redirect()->route('homepage.booking', ['id' => $request->id]);
        }

        $path = $this->upload->create($request->id, 'booking', $request->bukti_pembayaran);
        if ($request->metode == 'scan') {
            $metode = 'Scan Qris Gate Mauk';
        } elseif ($request->metode == 'transfer') {
            $metode = 'Transfer Bank';
        }
        pembayaran::create([
            'id_booking' => $request->id,
            'spesial' => null,
            'amount' => $booking->total_pembayaran,
            'status' => 'pending',
            'payment_method' => $metode,
            'bukti_pembayaran' => $path,
            'keterangan' => '',
            'spesial' => '',
            'deadline' => Carbon::now()->addDays(1),
        ]);

        return redirect()->back()->with('success', 'Bukti pembayaran berhasil dikirim');
    }

    public function deleteBuktiPembayaran(Request $request)
    {
        $request->validate([
            'id' => 'required|string',
            'id_pembayaran' => 'required|string',
        ]);

        $booking = gk_booking::with(['gateMasuk', 'gateKeluar', 'pendakis'])
            ->where('id', $request->id)
            ->where('id_user', Auth::id())
            ->first();

        if (!$booking) {
            abort(404);
        } elseif ($booking->status_booking != 3) {
            return redirect()->route('homepage.booking', ['id' => $request->id]);
        }

        $pembayaran = pembayaran::find($request->id_pembayaran);
        if (!$pembayaran) {
            return redirect()->back()->with('error', 'Pembayaran tidak ditemukan');
        }
        // hapus bukti pembayaran
        $pembayaran->delete();
        return redirect()->back()->with('success', 'Bukti pembayaran berhasil dihapus');
    }

    // =========================================================================================

    public function struk($id)
    {
        $booking = gk_booking::where('id', $id)->first();
        if (!$booking) {
            abort(404);
        } else if ($booking->status_booking < 3) {
            abort(404);
        }

        if ($booking->status_pembayaran) {
            $booking = json_decode($booking->dataStruk);
            // return $booking;
        } else {
            $booking = $this->helper->getDataStruk($booking->id);
        }

        // return $booking;

        return view('homepage.booking.bookingStruk', [
            'data' => $booking,
        ]);
    }

    public function tiket($id)
    {
        $booking = gk_booking::with(['gateMasuk', 'gateKeluar', 'pendakis.biodata', 'destinasi'])->where('id', $id)->first();

        if (!$booking) {
            abort(404);
        } else if ($booking->status_booking < 4) {
            abort(404);
        }

        if ($booking->id_user != Auth::id()) {
            $filteredPendakis = $booking->pendakis->where('id_bio', Auth::user()->id_bio)->first();
            $booking->pendakis = [$filteredPendakis];
        }

        // return $booking->pendakis[0];
        return view('homepage.booking.bookingTiket', [
            'booking' => $booking,
            'pendakis' => $booking->pendakis
        ]);
    }
}
