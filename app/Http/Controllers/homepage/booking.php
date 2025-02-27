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
use App\Models\setting;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;

use function PHPUnit\Framework\isEmpty;
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

    private function GateCapacity($idTiket, $startDate, $endDate = null, $idGate = null)
    {
        if (!$endDate) {
            $endDate = Carbon::parse($startDate)->addMonths(2)->format('Y-m-d');
        }

        $query = gk_booking::where('id_tiket', $idTiket)
            ->with('gateMasuk')
            ->where('status_booking', '>=', 4)
            ->whereBetween('tanggal_masuk', [$startDate, $endDate])
            ->select(
                'tanggal_masuk',
                'gate_masuk',
                DB::raw('SUM(total_pendaki_wni + total_pendaki_wna) as jumlah_pendaki')
            )
            ->groupBy('tanggal_masuk', 'gate_masuk')
            ->orderBy('tanggal_masuk', 'asc');

        // Filter berdasarkan ID Gate jika diberikan
        if (!is_null($idGate)) {
            $query->where('gate_masuk', $idGate);
        }

        return $query->get();
    }

    public function destinasiPaket($id)
    {
        $gunung = destinasi::find($id);
        $gambar_destinasi = gambar_destinasi::where('id_destinasi', $id)->get();
        $paket = gk_paket_tiket::where('id_destinasi', $id)->get();

        return view('homepage.booking.bookingDestinasiPaket', [
            'gunung' => $gunung,
            'paket' => $paket,
            'gambar' => $gambar_destinasi,
        ]);
    }

    public function destinasiTiket($id)
    {
        // ambil data paket
        $paket = gk_paket_tiket::where('id', $id)->first();

        // ambil data destinasi
        $id_destinasi = $paket->id_destinasi;
        $destinasi = destinasi::with('gates')->where('id', $id_destinasi)->first();
        $gambar_destinasi = gambar_destinasi::where('id_destinasi', $id_destinasi)->get();

        // ambil jumlah pendaki perhari selama 2 bulan kedepan dihitung dari tanggal_masuk, dan booking->verivied==verified
        $bookingBulanan = $this->GateCapacity($id, now());

        // ambil data tiket
        $tiket = gk_tiket_pendaki::where('id_paket_tiket', $id)
            ->with(['paket_tiket'])
            ->get();

        return view('homepage.booking.bookingDestinasiTiket', [
            'paket' => $paket,
            'destinasi' => $destinasi,
            'gambar' => $gambar_destinasi,
            'tiket' => $tiket,
            'bookingBulanan' => $bookingBulanan,
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
        if (!$user->biodata or $user->biodata->verified != 'verified') {
            return redirect()->route('user.dashboard.profile')->with('error', 'Biodata anda belum terverifikasi');
        }

        // cek tanggal masuk < tanggal keluar
        $dateStart = Carbon::createFromFormat('Y-m-d', $request->date_start);
        $dateEnd = Carbon::createFromFormat('Y-m-d', $request->date_end);
        $totalDays = $dateStart->diffInDays($dateEnd) + 1;
        if ($dateStart > $dateEnd) {
            return back()->with('error', 'Error: Tanggal tidak sesuai');
        }

        // cek jarak booking max 2 bulan
        if (Date::now()->addMonths(1) < $dateStart) {
            return back()->with('error', 'Error: Jarak booking tidak boleh lebih dari 1 bulan sekarang');
        }

        // cek jumlah min pendaki
        $tiket = gk_tiket_pendaki::where('id', $request->jenis_tiket)->with('paket_tiket')->first();
        if ($request->wni + $request->wna < $tiket->paket_tiket->min_pendaki) {
            return back()->with('error', 'Error: Jumlah pendaki tidak mencukupi. Minimal ' . $tiket->paket_tiket->min_pendaki . ' orang');
        }

        // cek kapasitas gate
        $gates = gk_gates::where('id', $request->gerbang_masuk)->first();
        $kapasitas = collect($this->GateCapacity($request->jenis_tiket, $request->date_start, $request->date_start, $request->gerbang_masuk));

        if (!$kapasitas->isEmpty()) {
            if ($gates->max_pendaki_hari < $kapasitas->first()->jumlah_pendaki + $request->wni + $request->wna) {
                return back()->with('error', 'Error: Kapasitas penuh');
            }
        }

        // cek umur ketua min 17 tahun
        $userBio = bio_pendaki::find(Auth::user()->id_bio);
        $userUsia = Carbon::parse($userBio->tanggal_lahir)->age;
        if ($userUsia < 17) {
            return back()->with('error', 'Error: Umur tidak mencukupi. Minimal 17 tahun');
        }

        // cari booking terakhir yang blm di verifikasi
        $booking = gk_booking::where('id_user', Auth::user()->id)
            ->where('status_booking', '<', 4)
            ->orderBy('created_at', 'desc')
            ->first();

        if ($booking) {
            // update total pendaki, gate, dan tanggal
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
            // craete booking
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

            // craete ketua pendaki
            gk_pendaki::create([
                'booking_id' => $newBooking->id,
                'tagihan' => 0,
                'id_bio' => $userBio->id,
                'usia' => $userUsia,
                'lampiran_surat_izin_ortu' => null,
            ]);


            return redirect()
                ->route('homepage.booking.snk', ['id' => $newBooking->id])
                ->with('success', 'Create Booking');
        }
    }

    private function getBookingByUser($id, $status = null): gk_booking
    {
        $booking = gk_booking::where('id_user', Auth::user()->id)->where('id', $id)->first();
        if (!$booking) {
            abort(404);
        }

        if (isset($status)) {
            if (is_array($status)) {
                if (!in_array($booking->status_booking, $status)) {
                    return redirect(route('homepage.booking', ['id' => $id]))->send();
                }
            } else {
                if ($booking->status_booking !== $status) {
                    return redirect(route('homepage.booking', ['id' => $id]))->send();
                }
            }
        }

        // cek booking sudah expired atau belum
        if ($booking->status_booking == 3 && $booking->updated_at->diffInHours(Carbon::now()) >= 24) {

            $booking->load('pembayaran');
            $pembayaranTerakhir = $booking->pembayaran()->latest()->first();

            // Jika pembayaran terakhir ada dan statusnya bukan "pending"
            if (($pembayaranTerakhir && $pembayaranTerakhir->status !== 'pending') or isEmpty($pembayaranTerakhir)) {
                // hapus semua pembayaran
                $booking->pembayaran()->delete();

                $booking->status_booking = 2;
                $booking->save();

                return redirect(route('homepage.booking', ['id' => $id]))->send();
            }
        }

        return $booking;
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
        $booking = $this->getBookingByUser($id, [0, 1, 2]);
        $booking->load('gktiket');


        $destinasi = destinasi::where('id', $booking->gktiket->id_destinasi)->first();
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

        $booking = $this->getBookingByUser($request->id, [0, 1, 2]);

        $booking->update(['status_booking' => 2]);
        return redirect()->route('homepage.booking.formulir', ['id' => $request->id]);
    }

    public function bookingFP($id)
    {
        $booking = $this->getBookingByUser($id, [1, 2]);
        $booking->load(['gateMasuk', 'gateKeluar', 'pendakis.biodata', 'gktiket']);

        // ================================ cek ketua pendaki ============================================
        $pendaki = $booking->pendakis;
        $barang = gk_barang_bawaan::where('id_booking', $booking->id)->get();

        return view('homepage.booking.bookingFp', [
            'id' => $id,
            'booking' => $booking,
            'pendaki' => $pendaki,
            'barang' => $barang,
        ]);
    }

    public function bookingPendakiAdd(Request $request)
    {
        $request->validate([
            'code' => 'required|string',
            'booking' => 'required|string',
            'id' => 'string|nullable',
        ]);

        $booking = $this->getBookingByUser($request->booking, 2);
        $booking->load(['gateMasuk', 'gateKeluar', 'pendakis']);

        // cek id biodata
        $bioPendaki = bio_pendaki::where('id', $request->code)
            ->where('verified', 'verified')
            ->first();
        if ($bioPendaki == null) {
            return back()->withErrors(['code' => 'Kode tidak ditemukan']);
        }
        // cek pendaki dalam bookingan
        $pendaki = gk_pendaki::with('booking')
            ->where('id_bio', $bioPendaki->id)
            ->whereHas('booking', function ($query) {
                $query->where('status_booking', '<', 7);
            })
            ->first();
        if ($pendaki) {
            return back()->withErrors(['code' => 'Kode sudah terdaftar dalam pendakian lain dan belum menyelesaikannya']);
        }

        // tambahkan pendaki
        $userUsia = Carbon::parse($bioPendaki->tanggal_lahir)->age;
        if ($request->id != null) {
            // update pendaki
            $pendaki = gk_pendaki::find($request->id);
            $pendaki->update([
                'id_bio' => $bioPendaki->id,
                'usia' => $userUsia,
                'lampiran_surat_izin_ortu' => null,
            ]);
        } else {
            // update pendakki
            gk_pendaki::create([
                'booking_id' => $request->booking,
                'tagihan' => 0,
                'id_bio' => $bioPendaki->id,
                'usia' => $userUsia,
                'lampiran_surat_izin_ortu' => null,
            ]);
        }

        return back()->with('success', 'Berhasil menambahkan pendaki');
    }

    public function bookingFPStore(Request $request)
    {
        $request->validate([
            'id_booking' => 'required|string',
            'action' => 'required|in:next,save',

            'formulir' => 'required|array',

            'formulir.*.id_pendaki' => 'nullable|string',
            'formulir.*.kode_bio' => 'nullable|string',
            'formulir.*.no_hp_darurat' => 'nullable|string|regex:/^(\+?[1-9]\d{0,2})?\s?\d{6,15}$/',
            'formulir.*.surat_izin_ortu' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048'
        ]);

        $booking = $this->getBookingByUser($request->id_booking, 2);
        $booking->load(['gateMasuk', 'gateKeluar', 'pendakis']);

        $totalTagihan = 0;
        $wni = 0;
        $wna = 0;

        foreach ($request->formulir as $formulir) {
            // cek kode bio
            if (isset($formulir['kode_bio'])) {
                $bioPendaki = bio_pendaki::find($formulir['kode_bio']);
                if (!$bioPendaki) {
                    return back()->withErrors(['formulir' => 'Bio pendaki tidak ditemukan']);
                }

                // jumlah ulang pendaki wna/ni
                ($bioPendaki->kenegaraan == 'ID') ? $wni++ : $wna++;

                // update no telp darurat pendaki
                $bioPendaki->no_hp_darurat = $formulir['no_hp_darurat'] ?? '';
                $bioPendaki->save();

                // cek pendaki
                $pendaki = gk_pendaki::find($formulir['id_pendaki']);
                if (!$pendaki) {
                    return back()->withErrors(['formulir' => 'Pendaki tidak ditemukan']);
                }

                // cek pendaki sudah dewasa
                if ($pendaki->usia < 17) {
                    // cek surat izin
                    if (isset($formulir['surat_izin_ortu'])) {
                        $path = '';
                        if (strlen($pendaki->lampiran_surat_izin_ortu) > 0) {
                            $this->upload->delete($pendaki->lampiran_surat_izin_ortu);
                            $path = $this->upload->create($booking->id, 'booking', $formulir['surat_izin_ortu']);
                        } else {
                            $path = $this->upload->create($booking->id, 'booking', $formulir['surat_izin_ortu']);
                        }
                        $pendaki->lampiran_surat_izin_ortu = $path;
                    }
                }

                // hitung tagihan pendaki dan booking
                $pendaki->tagihan = $this->helper->getTagihanPendaki($pendaki);
                $totalTagihan += $pendaki->tagihan;
                $pendaki->save();
            }
        }

        if ($request->action == 'next') {
            // cek kelengkapan bio pendaki
            $booking->load(['pendakis.biodata']);

            foreach ($booking->pendakis as $p) {
                // Cek apakah biodata ada
                if (!$p->biodata) {
                    return redirect()->back()->withErrors('Biodata pendaki tidak ditemukan.');
                }

                // Cek apakah no_hp_darurat ada dan sesuai format nomor telepon
                if (
                    !isset($p->biodata->no_hp_darurat) ||
                    !preg_match('/^(\+?[1-9]\d{0,2})?\s?\d{6,15}$/', $p->biodata->no_hp_darurat)
                ) {
                    return redirect()->back()->withErrors('No telp darurat pendaki ' . $p->biodata->first_name . ' tidak valid.');
                }

                // Cek jika pendaki <17 tahun, pastikan lampiran_surat_izin_ortu ada
                if ($p->usia < 17 && !($this->upload->check($p->lampiran_surat_izin_ortu))) {
                    return redirect()->back()->withErrors('Lampiran surat izin orang tua pendaki ' . $p->biodata->first_name . ' harus ada.');
                }
            }

            // cek min pendaki
            if ($wni + $wna < $booking->gateMasuk->min_pendaki_booking) {
                return redirect()->back()->withErrors('Minimal pendaki wni ' . $booking->gateMasuk->min_pendaki_booking . ' orang.');
            }

            // cek kapasitas pendaki
            $kapasitas = collect($this->GateCapacity($booking->id_tiket, $booking->tanggal_masuk, $booking->tanggal_masuk, $booking->gerbang_masuk));
            if (!$kapasitas->isEmpty()) {
                if ($booking->gateMasuk->max_pendaki_hari < $kapasitas->first()->jumlah_pendaki + $wni + $wna) {
                    return back()->with('error', 'Error: Kapasitas penuh');
                }
            }

            // cek persetujuan barang bawaan
            $request->validate([
                'barangWajib.perlengkapan_gunung_standar' => 'required|boolean',
                'barangWajib.trash_bag' => 'required|boolean',
                'barangWajib.p3k_standart' => 'required|boolean',
                'barangWajib.survival_kit_standart' => 'required|boolean',
            ]);
            // update total pendaki dan total pembayaran booking
            $booking->total_pendaki_wni = $wni;
            $booking->total_pendaki_wna = $wna;
            $booking->status_booking = 3;
            $booking->total_pembayaran = $totalTagihan;
            $booking->save();

            return redirect()->route('homepage.booking.detail', ['id' => $booking->id])->with('Data Booking Berhasil disimpan');
        } elseif ($request->action == 'save') {
            return redirect()->back()->with('success', 'Data berhasil disimpan');
        }

        return redirect()->back()->withErrors('error', 'Action tidak falid');
    }
    public function bookingDetail($id)
    {

        $booking = $this->getBookingByUser($id, 3);
        $booking->load(['gateMasuk', 'gateKeluar', 'pendakis']);

        return view('homepage.booking.bookingDetail', [
            'booking' => $booking,
            'formulirPendakis' => $booking->pendakis,
        ]);
    }

    public function bookingEdit($id)
    {
        $booking = $this->getBookingByUser($id, 3);
        $booking->load(['gateMasuk', 'gateKeluar', 'pendakis']);

        $booking->status_booking = 2;
        $booking->save();

        return redirect()->route('homepage.booking', ['id' => $id]);
    }

    public function bookingCancel($id)
    {
        $booking = $this->getBookingByUser($id, [0, 1, 2, 3]);
        $booking->load(['gateMasuk', 'gateKeluar', 'pendakis']);

        // hapus pembayaran
        $pembayaran = pembayaran::where('id_booking', $id)->get();
        foreach ($pembayaran as $p) {
            $p->delete();
        }

        // hapus pendakian
        $pendakis = gk_pendaki::where('booking_id', $id)->get();
        foreach ($pendakis as $p) {
            $p->delete();
        }

        // hapus booking
        $booking->delete();

        return redirect()->route('homepage.booking.destinasi.list')->with('success', 'Booking berhasil dibatalkan');
    }

    public function bookingPayment($id)
    {
        $booking = $this->getBookingByUser($id, [3, 4, 5, 6, 7, 8]);
        $booking->load(['gateMasuk', 'gateKeluar', 'gateMasuk.destinasi', 'pendakis']);

        $pembayaran = pembayaran::where('id_booking', $id)->get();
        $qris = gk_gates::where('id', $booking->gate_masuk)->first()->qris;
        $Bank = setting::where('id', '0000bank')->first();

        return view('homepage.booking.bookingPayment', [
            'qris' => $qris,
            'booking' => $booking,
            'pendakis' => $booking->pendakis,
            'pembayaran' => $pembayaran,
            'bank' => $Bank,

        ]);
    }

    public function addBuktiPembayaran(Request $request)
    {
        $request->validate([
            'id' => 'required|string',
            'metode' => 'required|string',
            'bukti_pembayaran' => 'required|file|mimes:jpg,jpeg,png,pdf|max:2048'
        ]);

        $booking = $this->getBookingByUser($request->id, 3);
        $booking->load(['gateMasuk', 'gateKeluar', 'pendakis']);

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

        $this->getBookingByUser($request->id, 3);

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
        $booking = $this->getBookingByUser($id, [3, 4, 5, 6, 7, 8]);

        if ($booking->status_pembayaran) {
            $booking = json_decode($booking->dataStruk);
        } else {
            $booking = $this->helper->getDataStruk($booking->id);
        }

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
