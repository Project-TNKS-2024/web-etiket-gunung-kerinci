<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Controllers\helper\ApiResponse;
use App\Http\Controllers\helper\BookingHelperController;
use App\Http\Controllers\helper\uploadFileControlller;
use App\Models\bio_pendaki;
use App\Models\gk_barang;
use App\Models\gk_barang_bawaan;
use App\Models\gk_booking;
use App\Models\gk_gates;
use App\Models\gk_paket_tiket;
use App\Models\gk_pendaki;
use App\Models\pembayaran;
use App\Models\setting;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class BookingController extends Controller
{
    public function __construct(
        private BookingHelperController $helper,
        private uploadFileControlller $upload
    ) {}

    private function bookingQuery(Request $request)
    {
        return gk_booking::where('id_user', $request->user()->id);
    }

    private function findUserBooking(Request $request, string $id, $status = null): ?gk_booking
    {
        $booking = $this->bookingQuery($request)->where('id', $id)->first();

        if (!$booking) {
            return null;
        }

        if ($status !== null) {
            $allowed = is_array($status) ? $status : [$status];
            if (!in_array((int) $booking->status_booking, array_map('intval', $allowed), true)) {
                return null;
            }
        }

        return $booking;
    }

    private function loadBooking(gk_booking $booking): gk_booking
    {
        return $booking->load([
            'gktiket:id,id_destinasi,nama,min_pendaki,penugasan,keterangan',
            'gktiket.tiket_pendaki:id,id_paket_tiket,kategori_pendaki,harga_masuk_wk,harga_masuk_wd,harga_kemah,harga_traking,harga_ansuransi,masa_ansuransi',
            'destinasi:id,nama,status,statusGunung,kategori,lokasi,detail,sop',
            'gateMasuk:id,nama,status,id_destinasi,max_pendaki_hari,min_pendaki_booking,lokasi,lokasi_maps,detail',
            'gateKeluar:id,nama,status,id_destinasi,max_pendaki_hari,min_pendaki_booking,lokasi,lokasi_maps,detail',
            'pendakis:id,booking_id,tagihan,id_bio,usia,lampiran_surat_izin_ortu',
            'pendakis.biodata:id,nik,kenegaraan,first_name,last_name,no_hp,no_hp_darurat,jenis_kelamin,tanggal_lahir,verified',
            'pembayaran:id,id_booking,amount,status,payment_method,bukti_pembayaran,deadline,keterangan',
        ]);
    }

    private function getBookingByDate(string $startDate, string $endDate, string $idBio)
    {
        return gk_booking::whereHas('pendakis', fn ($query) => $query->where('id_bio', $idBio))
            ->where(function ($query) use ($startDate, $endDate) {
                $query->whereBetween('tanggal_masuk', [$startDate, $endDate])
                    ->orWhere(function ($query) use ($startDate) {
                        $query->where('tanggal_masuk', '<=', $startDate)
                            ->where('tanggal_keluar', '>=', $startDate);
                    });
            })->get();
    }

    private function gateCapacity($idDestinasi, $startDate, $endDate = null, $idGate = null): array
    {
        $endDate = $endDate ?? Carbon::parse($startDate)->addMonths(2)->format('Y-m-d');
        $bookings = gk_booking::with('gateMasuk:id,nama,status,id_destinasi,max_pendaki_hari,min_pendaki_booking,lokasi,lokasi_maps,detail')
            ->whereHas('gktiket', fn ($query) => $query->where('id_destinasi', $idDestinasi))
            ->where('status_booking', '>=', 4)
            ->where(function ($query) use ($startDate, $endDate) {
                $query->whereBetween('tanggal_masuk', [$startDate, $endDate])
                    ->orWhere(function ($query) use ($startDate) {
                        $query->where('tanggal_masuk', '<=', $startDate)
                            ->where('tanggal_keluar', '>=', $startDate);
                    });
            });

        if ($idGate !== null) {
            $bookings->where('gate_masuk', $idGate);
        }

        $result = [];
        foreach ($bookings->get() as $booking) {
            $currentDate = Carbon::parse($booking->tanggal_masuk);
            $exitDate = Carbon::parse($booking->tanggal_keluar);
            $gate = $booking->gateMasuk;
            if (!$gate) {
                continue;
            }
            while ($currentDate <= $exitDate) {
                $date = $currentDate->format('Y-m-d');
                $result[$date][$gate->id] ??= [
                    'tanggal' => $date,
                    'id_gate' => $gate->id,
                    'gate_masuk' => $gate,
                    'jumlah_pendaki' => 0,
                ];
                $result[$date][$gate->id]['jumlah_pendaki'] += $booking->total_pendaki_wni + $booking->total_pendaki_wna;
                $currentDate->addDay();
            }
        }

        return collect($result)->flatMap(fn ($dates) => array_values($dates))->values()->all();
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'date_start' => 'required|date_format:Y-m-d',
            'date_end' => 'required|date_format:Y-m-d',
            'wni' => 'required|integer|min:0',
            'wna' => 'required|integer|min:0',
            'jenis_tiket' => 'required|integer|exists:gk_paket_tikets,id',
            'gerbang_masuk' => 'required|integer|exists:gk_gates,id',
            'gerbang_keluar' => 'required|integer|exists:gk_gates,id',
        ]);

        if ($validator->fails()) {
            return ApiResponse::error('Validasi gagal', $validator->errors(), 422);
        }

        $user = $request->user()->load('biodata');
        if (!$user->biodata || $user->biodata->verified !== 'verified') {
            return ApiResponse::error('Biodata anda belum terverifikasi', null, 403);
        }

        $dateStart = Carbon::createFromFormat('Y-m-d', $request->date_start);
        $dateEnd = Carbon::createFromFormat('Y-m-d', $request->date_end);
        if ($dateStart->isPast() && !$dateStart->isToday()) {
            return ApiResponse::error('Tanggal masuk tidak boleh kurang dari tanggal sekarang', null, 422);
        }
        if ($dateStart->gt($dateEnd)) {
            return ApiResponse::error('Tanggal tidak sesuai', null, 422);
        }
        if (now()->addMonth()->lt($dateStart)) {
            return ApiResponse::error('Jarak booking tidak boleh lebih dari 1 bulan dari sekarang', null, 422);
        }

        $paket = gk_paket_tiket::find($request->jenis_tiket);
        $totalPendaki = (int) $request->wni + (int) $request->wna;
        if ($totalPendaki < (int) $paket->min_pendaki) {
            return ApiResponse::error('Jumlah pendaki tidak mencukupi. Minimal ' . $paket->min_pendaki . ' orang', null, 422);
        }

        $gateMasuk = gk_gates::where('id', $request->gerbang_masuk)->where('id_destinasi', $paket->id_destinasi)->where('status', true)->first();
        $gateKeluar = gk_gates::where('id', $request->gerbang_keluar)->where('id_destinasi', $paket->id_destinasi)->where('status', true)->first();
        if (!$gateMasuk || !$gateKeluar) {
            return ApiResponse::error('Gerbang masuk atau keluar tidak valid', null, 422);
        }

        foreach ($this->gateCapacity($paket->id_destinasi, $request->date_start, $request->date_start, $request->gerbang_masuk) as $capacity) {
            if ($gateMasuk->max_pendaki_hari < $capacity['jumlah_pendaki'] + $totalPendaki) {
                return ApiResponse::error('Kapasitas penuh', null, 422);
            }
        }

        $userAge = Carbon::parse($user->biodata->tanggal_lahir)->age;
        if ($userAge < 17) {
            return ApiResponse::error('Umur ketua tidak mencukupi. Minimal 17 tahun', null, 422);
        }

        $existingDraft = $this->bookingQuery($request)->where('status_booking', '<', 4)->latest()->first();
        $conflictingBooking = $this->getBookingByDate($request->date_start, $request->date_end, $user->biodata->id)
            ->when($existingDraft, fn ($collection) => $collection->where('id', '!=', $existingDraft->id));
        if ($conflictingBooking->count() > 0) {
            return ApiResponse::error('Anda sudah melakukan booking di tanggal tersebut', null, 422);
        }

        $booking = DB::transaction(function () use ($request, $user, $userAge, $dateStart, $dateEnd, $existingDraft) {
            $booking = $existingDraft;
            $payload = [
                'id_tiket' => $request->jenis_tiket,
                'tanggal_masuk' => $dateStart,
                'tanggal_keluar' => $dateEnd,
                'total_hari' => $dateStart->diffInDays($dateEnd) + 1,
                'total_pendaki_wni' => $request->wni,
                'total_pendaki_wna' => $request->wna,
                'gate_masuk' => $request->gerbang_masuk,
                'gate_keluar' => $request->gerbang_keluar,
            ];
            if ($booking) {
                $booking->update($payload + ['status_booking' => min((int) $booking->status_booking, 0)]);
            } else {
                $booking = gk_booking::create($payload + [
                    'id_user' => $user->id,
                    'status_booking' => 0,
                    'total_pembayaran' => 0,
                    'status_pembayaran' => false,
                ]);
                gk_pendaki::create([
                    'booking_id' => $booking->id,
                    'tagihan' => 0,
                    'id_bio' => $user->biodata->id,
                    'usia' => $userAge,
                ]);
            }
            return $booking;
        });

        return ApiResponse::success($this->loadBooking($booking), 'Booking awal berhasil disimpan', 200);
    }

    public function show(Request $request, $id)
    {
        $booking = $this->findUserBooking($request, $id);
        if (!$booking) {
            return ApiResponse::error('Booking tidak ditemukan', null, 404);
        }
        $booking = $this->loadBooking($booking);
        $booking->status_label = $booking->getStatusBooking();
        return ApiResponse::success($booking, 'Berhasil mengambil detail booking', 200);
    }

    public function acceptSnk(Request $request)
    {
        $validator = Validator::make($request->all(), ['id' => 'required|uuid', 'snk' => 'required|accepted']);
        if ($validator->fails()) {
            return ApiResponse::error('Validasi gagal', $validator->errors(), 422);
        }
        $booking = $this->findUserBooking($request, $request->id, [0, 1, 2]);
        if (!$booking) {
            return ApiResponse::error('Booking tidak ditemukan atau status tidak valid', null, 404);
        }
        $booking->update(['status_booking' => 2]);
        return ApiResponse::success($this->loadBooking($booking), 'Syarat dan ketentuan disetujui', 200);
    }

    public function formulir(Request $request, $id)
    {
        $booking = $this->findUserBooking($request, $id, [1, 2]);
        if (!$booking) {
            return ApiResponse::error('Booking tidak ditemukan atau status tidak valid', null, 404);
        }
        return ApiResponse::success([
            'booking' => $this->loadBooking($booking),
            'barang_wajib' => gk_barang::select('id', 'title', 'detail')->get(),
            'barang_bawaan' => gk_barang_bawaan::where('id_booking', $booking->id)->select('id', 'id_booking', 'nama_barang', 'jumlah')->get(),
        ], 'Berhasil mengambil data formulir', 200);
    }

    public function addPendaki(Request $request)
    {
        $validator = Validator::make($request->all(), ['booking' => 'required|uuid', 'code' => 'required|string|max:36', 'id' => 'nullable|uuid']);
        if ($validator->fails()) {
            return ApiResponse::error('Validasi gagal', $validator->errors(), 422);
        }
        $booking = $this->findUserBooking($request, $request->booking, 2);
        if (!$booking) {
            return ApiResponse::error('Booking tidak ditemukan atau status tidak valid', null, 404);
        }
        $bio = bio_pendaki::where('id', $request->code)->where('verified', 'verified')->first();
        if (!$bio) {
            return ApiResponse::error('Kode biodata tidak ditemukan atau belum terverifikasi', null, 404);
        }
        if ($this->getBookingByDate($booking->tanggal_masuk, $booking->tanggal_keluar, $bio->id)->where('id', '!=', $booking->id)->count() > 0) {
            return ApiResponse::error('Pendaki sudah terdaftar dalam pendakian lain di tanggal booking ini', null, 422);
        }
        $duplicate = gk_pendaki::where('booking_id', $booking->id)
            ->where('id_bio', $bio->id)
            ->when($request->id, fn ($query) => $query->where('id', '!=', $request->id))
            ->exists();
        if ($duplicate) {
            return ApiResponse::error('Pendaki sudah ada dalam booking ini', null, 422);
        }
        $age = Carbon::parse($bio->tanggal_lahir)->age;
        $pendaki = $request->id
            ? gk_pendaki::where('id', $request->id)->where('booking_id', $booking->id)->first()
            : new gk_pendaki(['booking_id' => $booking->id]);
        if (!$pendaki) {
            return ApiResponse::error('Pendaki tidak ditemukan', null, 404);
        }
        $pendaki->fill(['id_bio' => $bio->id, 'usia' => $age, 'tagihan' => $pendaki->tagihan ?? 0, 'lampiran_surat_izin_ortu' => null])->save();
        return ApiResponse::success($this->loadBooking($booking), 'Berhasil menyimpan pendaki', 200);
    }

    public function saveFormulir(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id_booking' => 'required|uuid',
            'action' => 'required|in:next,save',
            'barangWajib' => 'required_if:action,next|boolean',
            'formulir' => 'required|array',
            'formulir.*.id_pendaki' => 'required|uuid',
            'formulir.*.kode_bio' => 'required|string|max:36',
            'formulir.*.no_hp_darurat' => 'nullable|string|regex:/^(\+?[1-9]\d{0,2})?\s?\d{6,15}$/',
            'formulir.*.surat_izin_ortu' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'barang_bawaan' => 'nullable|array',
            'barang_bawaan.*.nama_barang' => 'required_with:barang_bawaan|string|max:255',
            'barang_bawaan.*.jumlah' => 'required_with:barang_bawaan|integer|min:1|max:999',
        ]);
        if ($validator->fails()) {
            return ApiResponse::error('Validasi gagal', $validator->errors(), 422);
        }
        $booking = $this->findUserBooking($request, $request->id_booking, 2);
        if (!$booking) {
            return ApiResponse::error('Booking tidak ditemukan atau status tidak valid', null, 404);
        }

        try {
            DB::transaction(function () use ($request, $booking) {
                $totalTagihan = 0;
                $wni = 0;
                $wna = 0;

                foreach ($request->formulir as $index => $formulir) {
                    $bio = bio_pendaki::where('id', $formulir['kode_bio'])->where('verified', 'verified')->first();
                    $pendaki = gk_pendaki::where('id', $formulir['id_pendaki'])->where('booking_id', $booking->id)->first();
                    if (!$bio || !$pendaki) {
                        throw new \InvalidArgumentException('Data pendaki tidak valid');
                    }
                    if ($pendaki->id_bio !== $bio->id) {
                        throw new \InvalidArgumentException('Kode biodata tidak sesuai dengan pendaki');
                    }

                    $bio->no_hp_darurat = $formulir['no_hp_darurat'] ?? $bio->no_hp_darurat;
                    $bio->save();
                    $bio->kenegaraan === 'ID' ? $wni++ : $wna++;

                    if ($pendaki->usia < 17) {
                        if ($request->hasFile("formulir.$index.surat_izin_ortu")) {
                            if ($pendaki->lampiran_surat_izin_ortu) {
                                $this->upload->delete($pendaki->lampiran_surat_izin_ortu);
                            }
                            $pendaki->lampiran_surat_izin_ortu = $this->upload->create($booking->id, 'booking', $request->file("formulir.$index.surat_izin_ortu"));
                        } elseif ($request->action === 'next' && !$pendaki->lampiran_surat_izin_ortu) {
                            throw new \InvalidArgumentException('Lampiran surat izin orang tua wajib untuk pendaki di bawah 17 tahun');
                        }
                    }

                    $pendaki->tagihan = $this->helper->getTagihanPendaki($pendaki);
                    $pendaki->save();
                    $totalTagihan += $pendaki->tagihan;
                }

                gk_barang_bawaan::where('id_booking', $booking->id)->delete();
                foreach ($request->input('barang_bawaan', []) as $barang) {
                    gk_barang_bawaan::create(['id_booking' => $booking->id, 'nama_barang' => $barang['nama_barang'], 'jumlah' => $barang['jumlah']]);
                }

                if ($request->action === 'next') {
                    if (($wni + $wna) !== ($booking->total_pendaki_wni + $booking->total_pendaki_wna)) {
                        throw new \InvalidArgumentException('Jumlah data pendaki tidak sesuai dengan booking');
                    }
                    if (($wni + $wna) < (int) $booking->gateMasuk->min_pendaki_booking) {
                        throw new \InvalidArgumentException('Minimal pendaki ' . $booking->gateMasuk->min_pendaki_booking . ' orang');
                    }
                    $booking->dataStruk = $this->helper->getDataStruk($booking->id);
                    $booking->total_pendaki_wni = $wni;
                    $booking->total_pendaki_wna = $wna;
                    $booking->total_pembayaran = $totalTagihan;
                    $booking->status_booking = 3;
                }

                $booking->save();
            });
        } catch (\InvalidArgumentException $error) {
            return ApiResponse::error($error->getMessage(), null, 422);
        }

        return ApiResponse::success($this->loadBooking($booking->fresh()), 'Formulir booking berhasil disimpan', 200);
    }

    public function detail(Request $request, $id)
    {
        return $this->show($request, $id);
    }

    public function payment(Request $request, $id)
    {
        $booking = $this->findUserBooking($request, $id, [3, 4, 5, 6, 7, 8]);
        if (!$booking) {
            return ApiResponse::error('Booking tidak ditemukan atau status tidak valid', null, 404);
        }
        return ApiResponse::success([
            'booking' => $this->loadBooking($booking),
            'bank' => setting::where('id', '0000bank')->select('id', 'nama', 'text1', 'text2')->first(),
            'qris' => gk_gates::where('id', $booking->gate_masuk)->value('qris'),
        ], 'Berhasil mengambil data pembayaran', 200);
    }

    public function addPayment(Request $request)
    {
        $validator = Validator::make($request->all(), ['id' => 'required|uuid', 'metode' => 'required|in:scan,transfer', 'bukti_pembayaran' => 'required|file|mimes:jpg,jpeg,png,pdf|max:2048']);
        if ($validator->fails()) {
            return ApiResponse::error('Validasi gagal', $validator->errors(), 422);
        }
        $booking = $this->findUserBooking($request, $request->id, 3);
        if (!$booking) {
            return ApiResponse::error('Booking tidak ditemukan atau status tidak valid', null, 404);
        }
        $path = $this->upload->create($booking->id, 'booking', $request->file('bukti_pembayaran'));
        pembayaran::create([
            'id_booking' => $booking->id,
            'amount' => $booking->total_pembayaran,
            'status' => 'pending',
            'payment_method' => $request->metode === 'scan' ? 'Scan Qris Gate Masuk' : 'Transfer Bank',
            'bukti_pembayaran' => $path,
            'deadline' => Carbon::now()->addDay(),
            'keterangan' => '',
            'spesial' => '',
        ]);
        return ApiResponse::success($this->loadBooking($booking), 'Bukti pembayaran berhasil dikirim', 200);
    }

    public function deletePayment(Request $request, $id)
    {
        $validator = Validator::make(['id' => $id] + $request->all(), [
            'id' => 'required|uuid',
            'booking_id' => 'required|uuid',
        ]);
        if ($validator->fails()) {
            return ApiResponse::error('Validasi gagal', $validator->errors(), 422);
        }
        $booking = $this->findUserBooking($request, $request->input('booking_id'), 3);
        if (!$booking) {
            return ApiResponse::error('Booking tidak ditemukan atau status tidak valid', null, 404);
        }
        $payment = pembayaran::where('id', $id)->where('id_booking', $booking->id)->first();
        if (!$payment) {
            return ApiResponse::error('Pembayaran tidak ditemukan', null, 404);
        }
        $this->upload->delete($payment->bukti_pembayaran);
        $payment->delete();
        return ApiResponse::success($this->loadBooking($booking), 'Bukti pembayaran berhasil dihapus', 200);
    }

    public function struk(Request $request, $id)
    {
        $booking = $this->findUserBooking($request, $id, [3, 4, 5, 6, 7, 8]);
        if (!$booking) {
            return ApiResponse::error('Booking tidak ditemukan atau status tidak valid', null, 404);
        }
        return ApiResponse::success($booking->dataStruk ? json_decode($booking->dataStruk) : $this->helper->getDataStruk($booking->id), 'Berhasil mengambil struk', 200);
    }

    public function tiket(Request $request, $id)
    {
        $booking = $this->findUserBooking($request, $id, [4, 5, 6, 7, 8]);
        if (!$booking) {
            return ApiResponse::error('Tiket tidak ditemukan atau belum tersedia', null, 404);
        }
        return ApiResponse::success($this->loadBooking($booking), 'Berhasil mengambil tiket', 200);
    }

    public function myTickets(Request $request)
    {
        $bookings = $this->bookingQuery($request)->latest()->get()->map(fn ($booking) => $this->loadBooking($booking));
        return ApiResponse::success($bookings, 'Berhasil mengambil daftar booking', 200);
    }

    public function cancel(Request $request, $id)
    {
        $booking = $this->findUserBooking($request, $id, [0, 1, 2, 3]);
        if (!$booking) {
            return ApiResponse::error('Booking tidak ditemukan atau tidak dapat dibatalkan', null, 404);
        }
        $booking->delete();
        return ApiResponse::success(null, 'Booking berhasil dibatalkan', 200);
    }
}
