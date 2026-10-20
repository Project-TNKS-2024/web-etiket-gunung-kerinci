@extends('etiket.admin.template.index')

@section('css')

<style>
   /* Set fixed dimensions for the card */
   .fixed-card {
      width: 100%;
      border-radius: 10px;
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
      background-color: #fff;
   }

   /* Style the table */
   .table {
      font-size: 0.9rem;
   }

   .qrcode_kodebooking img {
      width: -webkit-fill-available !important;
   }


   .tableCardTiket tbody tr td {
      padding: 3px 5px;
   }

   .table {
      --bs-table-bg: transparent;
   }

   #status-badge .badge {
      margin: 2px;
   }
</style>

@endsection

@section('main')
@if ((int) $booking->status_booking === 9)
<div class="alert alert-danger" role="status">
   <strong>Pesanan dibatalkan</strong>
   <p class="mb-1">{{ $booking->cancellation_reason }}</p>
   <small>{{ $booking->cancelled_at?->timezone(config('app.timezone'))->format('d/m/Y H:i') }} WIB
      · {{ $booking->cancelledBy?->email ?? 'Pemesan/petugas' }}</small>
   <div>Riwayat pemesanan dan bukti pembayaran tetap tersimpan.</div>
</div>
@endif
@can('edit-booking')
@if (in_array((int) $booking->status_booking, [0, 1, 2, 3], true))
<div class="card"><div class="card-body">
   <h5>Batalkan pesanan</h5>
   <p>Hanya tersedia sebelum konfirmasi pembayaran. Data pesanan tidak dihapus.</p>
   <form method="POST" action="{{ route('admin.destinasi.booking.cancel') }}">
      @csrf
      <input type="hidden" name="booking_id" value="{{ $booking->id }}">
      <label for="cancellationReason" class="form-label">Alasan pembatalan</label>
      <textarea id="cancellationReason" name="reason" class="form-control mb-2" rows="2" maxlength="500" required>{{ old('reason') }}</textarea>
      @error('reason')<div class="text-danger">{{ $message }}</div>@enderror
      <button type="submit" class="btn btn-danger">Batalkan Pesanan</button>
   </form>
</div></div>
@endif
@endcan


<div class="card">
   <div class="card-header">
      <h5><b>Detail Booking</b></h5>
   </div>
   <div class="card-body">
      <div class="row">
         <div class="col-12 col-lg-8">
            <div class="row">
               <div class="col-12 col-sm-6 col-md-8">
                  <table class="table tableCardTiket table-borderless align-middle">
                     <tr class="fw-bold">
                        <td>Nama Ketua</td>
                        <td>Destinasi</td>
                     </tr>
                     <tr>
                        <td>{{ $booking->pendakis->first()?->biodata?->fullName ?? 'Belum diisi' }}</td>
                        <td>
                           {{$booking->destinasi->nama}}
                        </td>
                     </tr>
                     <tr class="fw-bold">
                        <td>Gerbang Masuk</td>
                        <td>Gerbang Keluar</td>
                     </tr>
                     <tr>
                        <td>{{ $booking->gateMasuk->nama }}</td>
                        <td>{{ $booking->gateKeluar->nama }}</td>
                     </tr>
                     <tr class="fw-bold">
                        <td>Check In</td>
                        <td>Check Out</td>
                     </tr>
                     <tr>
                        <td>{{ $booking->tanggal_masuk }}</td>
                        <td>{{ $booking->tanggal_keluar }}</td>
                     </tr>
                     <tr class="fw-bold">
                        <td>Jumlah Anggota</td>
                        <td>Kewarganegaraan</td>
                     </tr>
                     <tr>
                        <td>{{ $booking->total_pendaki_wni + $booking->total_pendaki_wna }} orang</td>
                        <td>
                           <div class="row">
                              <div class="col">
                                 <p>{{ $booking->total_pendaki_wni }} WNI</p>
                              </div>
                              <div class="col">
                                 <p>{{ $booking->total_pendaki_wna }} WNA</p>
                              </div>
                           </div>
                        </td>
                     </tr>
                  </table>
               </div>
               <div class="col-12 col-sm-6 col-md-4 text-center card-qrboooking">
                  <div class="qrcode_kodebooking" data-qr='{{$booking->unique_code}}'></div>
                  <label for="ipt_kodebooking" class="w-100">Kode Booking</label>
                  <input type="text" name="ipt_kodebooking" value="{{ $booking->unique_code }}" id="ipt_kodebooking"
                     class="form-control text-center fw-bold border-0" readonly>
               </div>
            </div>
         </div>
         <div class="col-12 col-lg-4">
            <h6 class="text-center"><b>{{ (int) $booking->status_booking === 9 ? 'Pesanan dibatalkan' : 'Progres booking' }}</b></h6>
            @if ((int) $booking->status_booking !== 9)
            <div class="progress my-1" style="height: 25px;">

               <div class="progress-bar bg-success" role="progressbar"
                  style="width: {{ min($booking->status_booking, 8) / 8 * 100 }}%;"
                  aria-valuenow="{{ $booking->status_booking }}"
                  aria-valuemin="0" aria-valuemax="8">
                  {{ $booking->status_booking }}/8 Selesai
               </div>
            </div>

            <div id="status-badge">
               <span class="badge {{ $booking->status_booking >= 1 ? 'bg-secondary' : 'bg-light text-dark' }}">1. Syarat & Ketentuan Disetujui</span>
               <span class="badge {{ $booking->status_booking >= 2 ? 'bg-secondary' : 'bg-light text-dark' }}">2. Formulir Terisi</span>
               <span class="badge {{ $booking->status_booking >= 3 ? 'bg-warning' : 'bg-light text-dark' }}">3. Menunggu Pembayaran</span>
               <span class="badge {{ $booking->status_booking >= 4 ? 'bg-info' : 'bg-light text-dark' }}">4. Pembayaran Selesai</span>
               <span class="badge {{ $booking->status_booking >= 5 ? 'bg-primary' : 'bg-light text-dark' }}">5. Konfirmasi Pendakian</span>
               <span class="badge {{ $booking->status_booking >= 6 ? 'bg-primary' : 'bg-light text-dark' }}">6. Check-in</span>
               <span class="badge {{ $booking->status_booking >= 7 ? 'bg-primary' : 'bg-light text-dark' }}">7. Check-out</span>
               <span class="badge {{ $booking->status_booking >= 8 ? 'bg-success' : 'bg-light text-dark' }}">8. Selesai</span>
            </div>
            @endif
         </div>
      </div>
   </div>
</div>

<div class="card">
   <div class="card-body py-3">
      <div class="text-end">
         @if ($booking->dataStruk)
         <a href="{{route('admin.destinasi.booking.struk.show', ['id' => $booking->id])}}" class="btn btn-primary">Cek Struk</a>
         @endif
         <a href="{{route('admin.destinasi.booking.payment.show', ['id' => $booking->id])}}" class="btn btn-primary">Cek Pembelian</a>
         @if (in_array((int) $booking->status_booking, [4, 5, 6, 7, 8], true))
         <a href="{{route('admin.destinasi.booking.tiket.show', ['id' => $booking->id])}}" class="btn btn-primary">Cek Tiket</a>
         @endif
         @if ((int) $booking->status_booking !== 9)
         <a href="" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#modalGantiTanggal">Ganti Hari</a>
         @endif
      </div>
   </div>
</div>

<div class="modal fade" id="modalGantiTanggal" tabindex="-1" aria-labelledby="modalGantiTanggalLabel" aria-hidden="true">
   <div class="modal-dialog">
      <div class="modal-content">
         <div class="modal-header">
            <h5 class="modal-title" id="modalGantiTanggalLabel">Ganti Tanggal Pendakian</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
         </div>
         <div class="modal-body">
            <form id="bookingGantiForm" method="post" action="{{route('admin.destinasi.booking.gantiTanggal')}}">
               @csrf
               <div>
                  <input type="hidden" name="id_booking" value="{{$booking->id}}">
                  <input type="hidden" name="totalHari" value="{{$booking->total_hari}}">
               </div>
               <div class="mb-3">
                  <label for="bookinfStartDate" class="form-label">Tanggal Mulai:</label>
                  <input type="date" id="bookinfStartDate" name="bookinfStartDate" class="form-control" required value="{{$booking->tanggal_masuk}}">
               </div>
               <fieldset disabled>
                  <div class="mb-3">
                     <label for="bookingEndDate" class="form-label">Tanggal Selesai:</label>
                     <input type="date" id="bookingEndDate" name="bookingEndDate" class="form-control" value="{{$booking->tanggal_keluar}}">
                  </div>
               </fieldset>
               <div class="d-flex justify-content-end">
                  <button type="button" class="btn btn-secondary me-2" data-bs-dismiss="modal">Batal</button>
                  <button type="submit" class="btn btn-primary">Simpan</button>
               </div>
            </form>
         </div>
      </div>
   </div>
</div>

<div class="card">
   <div class="card-header">
      <h5><b>Anggota Pendakian</b></h5>
   </div>

   <div class="card-body">
      <div class="table-responsive">
         <table class="table table-bordered">
            <thead class="bg-dark text-white">
               <tr>
                  <td>Nama</td>
                  <td>Umur</td>
                  <td>Warga Negara</td>
                  <td>Jenis Kelamin</td>
                  <td>Batal Mendaki</td>
                  <td>Cek In</td>
                  <td>Cek Out</td>
                  <td></td>
               </tr>
            </thead>
            <tbody class="table-group-divider">
               @foreach ($booking->pendakis as $pendaki)
               <tr>
                  <td>{{$pendaki->biodata->first_name . ' ' .$pendaki->biodata->last_name}}</td>
                  <td>{{$pendaki->usia}}</td>
                  <td>{{$pendaki->biodata->kenegaraan == 'ID' ? '(WNI) Indonesia' : '(WNA) '.$pendaki->biodata->dataNegara->name }}</td>
                  <td>{{$pendaki->biodata->jenis_kelamin=='l' ? 'Laki - laki' : 'Perempuan' }}</td>
                  <td>
                     <div class="form-check">
                        <input class="form-check-input batal-checkbox" {{ optional($pendaki->getStatus->last())->status == 1 ? 'checked' : '' }} type="checkbox" data-id="{{$pendaki->id}}" disabled>
                     </div>
                  </td>
                  <td>
                     <div class="form-check">
                        <input class="form-check-input cekin-checkbox" {{ optional($pendaki->getStatus->last())->status >= 2 ? 'checked' : '' }} type="checkbox" data-id="{{$pendaki->id}}" disabled>
                     </div>
                  </td>
                  <td>
                     <div class="form-check">
                        <input class="form-check-input cekout-checkbox" {{ optional($pendaki->getStatus->last())->status == 3 ? 'checked' : '' }} type="checkbox" data-id="{{$pendaki->id}}" disabled>
                     </div>
                  </td>
                  <td>
                     <a href="{{route('admin.master.pengunjung.biodata', ['id' => $pendaki->biodata->user->id])}}" class="btn btn-sm btn-warning">
                        <i class="fa-solid fa-eye"></i>
                     </a>
                  </td>
               </tr>
               @endforeach
            </tbody>
         </table>
      </div>
      @if ($booking->status_booking >= 4 && $booking->status_booking <= 8)
      <div class="mt-1 d-flex justify-content-between align-items-center">
         <form id="statusForm" action="{{ route('admin.destinasi.booking.updateStatus') }}" method="POST">
            @csrf
            <input type="hidden" name="booking_id" value="{{ $booking->id }}">
            <input type="hidden" name="name" id="form-name">

            <div id="checkbox-container"></div> <!-- Tempat input hidden -->

            <button type="submit" class="btn btn-success btn-simpan d-none">Simpan Perubahan</button>
         </form>
         <div>
            <button type="button" class="btn btn-warning btn-aksi btn-batal-pendakian" data-target="batal-checkbox" data-name="1">Batal Pendakian</button>
            @if ($booking->tanggal_masuk < now())
               <button type="button" class="btn btn-primary btn-aksi btn-cekin" data-target="cekin-checkbox" data-name="2">Cek in</button>
               @endif

               <button type="button" class="btn btn-danger btn-batal d-none">Batal</button>
               <form method="POST" action="{{route('admin.destinasi.booking.updateStatus')}}" class="d-inline">
                  @if ($booking->status_booking == 7)
                  @csrf
                  <input type="hidden" name="booking_id" value="{{$booking->id}}">
                  <input type="hidden" name="name" value="4">
                  <button type="submit" class="btn btn-success">Selesai</button>
                  @endif
               </form>
         </div>
      </div>
      @endif

   </div>
</div>

@if ($booking->status_booking >= 4 && $booking->status_booking <= 8)
@php
   $hikeStateLabel = [
      'belum_masuk' => 'Belum masuk',
      'batal' => 'Batal mendaki',
      'mendaki' => 'Sedang mendaki',
      'turun' => 'Sudah turun',
   ];
   $chargeStatusLabel = [
      'none' => 'Tidak ada',
      'estimate' => 'Estimasi',
      'final' => 'Final',
      'needs_review' => 'Perlu ditinjau',
   ];
   $paymentStateLabel = [
      'none' => '-',
      'unpaid' => 'Belum dibayar',
      'paid' => 'Lunas',
   ];
   $dayTypeLabel = [
      'weekday' => 'Hari biasa',
      'weekend' => 'Akhir pekan',
      'holiday' => 'Hari libur',
   ];
   $activePendakis = collect($overstay['pendakis'])->where('hike_state', 'mendaki');
@endphp

<div class="card">
   <div class="card-header d-flex justify-content-between align-items-center">
      <h5 class="mb-0"><b>Konfirmasi Turun (Cek Out)</b></h5>
      <span class="badge text-bg-warning">Waktu default: sekarang (WIB). Ubah hanya jika turun lebih awal, sertakan alasan.</span>
   </div>
   <div class="card-body">
      @if ($activePendakis->isEmpty())
         <p class="text-muted mb-0">Tidak ada pendaki yang sedang mendaki.</p>
      @else
      <form id="checkoutForm" method="POST" action="{{ route('admin.destinasi.booking.checkout') }}">
         @csrf
         <input type="hidden" name="booking_id" value="{{ $booking->id }}">
         <div class="table-responsive">
            <table class="table table-bordered align-middle">
               <thead class="bg-dark text-white">
                  <tr>
                     <td style="width:40px">Pilih</td>
                     <td>Nama</td>
                     <td>Waktu turun</td>
                     <td>Alasan (jika mundur)</td>
                  </tr>
               </thead>
               <tbody>
                  @foreach ($activePendakis as $p)
                  <tr>
                     <td class="text-center">
                        <input class="form-check-input checkout-select" type="checkbox" value="{{ $p['id'] }}">
                     </td>
                     <td>{{ $p['full_name'] }}</td>
                     <td>
                        <input type="datetime-local" class="form-control checkout-time" data-id="{{ $p['id'] }}"
                           value="{{ \Carbon\Carbon::now()->setTimezone(config('app.timezone'))->format('Y-m-d\TH:i') }}">
                     </td>
                     <td>
                        <input type="text" class="form-control checkout-reason" data-id="{{ $p['id'] }}" maxlength="255" placeholder="Opsional">
                     </td>
                  </tr>
                  @endforeach
               </tbody>
            </table>
         </div>
         <div class="text-end">
            <button type="submit" class="btn btn-success">Simpan Kepulangan</button>
         </div>
      </form>
      @endif
   </div>
</div>

<div class="card">
   <div class="card-header">
      <h5><b>Biaya Tambahan (Keterlambatan Tiket)</b></h5>
   </div>
   <div class="card-body">
      <div class="row mb-3">
         <div class="col-12 col-md-3">
            <div class="border rounded p-2 text-center">
               <small class="text-muted">Overdue aktif</small>
               <h5 class="mb-0">{{ $overstay['team']['overdue_active_count'] }}</h5>
            </div>
         </div>
         <div class="col-12 col-md-3">
            <div class="border rounded p-2 text-center">
               <small class="text-muted">Tagihan final belum bayar</small>
               <h5 class="mb-0">{{ $overstay['team']['unpaid_final_count'] }}</h5>
            </div>
         </div>
         <div class="col-12 col-md-3">
            <div class="border rounded p-2 text-center">
               <small class="text-muted">Estimasi</small>
               <h5 class="mb-0">Rp {{ number_format($overstay['team']['estimate_total'], 0, ',', '.') }}</h5>
            </div>
         </div>
         <div class="col-12 col-md-3">
            <div class="border rounded p-2 text-center">
               <small class="text-muted">Final</small>
               <h5 class="mb-0">Rp {{ number_format($overstay['team']['final_total'], 0, ',', '.') }}</h5>
            </div>
         </div>
      </div>

      <div class="table-responsive">
         <table class="table table-bordered align-middle">
            <thead class="bg-dark text-white">
               <tr>
                  <td>Nama</td>
                  <td>Status</td>
                  <td>Jadwal turun</td>
                  <td>Turun aktual</td>
                  <td>Hari tambahan</td>
                  <td>Biaya</td>
                  <td>Status biaya</td>
                  <td>Pembayaran</td>
                  <td></td>
               </tr>
            </thead>
            <tbody>
               @foreach ($overstay['pendakis'] as $p)
               <tr>
                  <td>{{ $p['full_name'] }}</td>
                  <td>{{ $hikeStateLabel[$p['hike_state']] ?? $p['hike_state'] }}</td>
                  <td>{{ $p['scheduled_exit_date'] }}</td>
                  <td>
                     @if ($p['actual_checkout_at'])
                        {{ \Carbon\Carbon::parse($p['actual_checkout_at'])->setTimezone(config('app.timezone'))->format('d M Y H:i') }}
                     @else
                        -
                     @endif
                  </td>
                  <td>
                     @if ($p['overdue_days'] === null)
                        -
                     @else
                        {{ $p['overdue_days'] }} hari
                     @endif
                  </td>
                  <td>
                     @if ($p['amount'] === null)
                        Biaya belum dapat dihitung
                     @else
                        Rp {{ number_format($p['amount'], 0, ',', '.') }}
                     @endif
                  </td>
                  <td>
                     @php $cs = $p['charge_status']; @endphp
                     <span class="badge {{ $cs === 'final' ? 'text-bg-primary' : ($cs === 'needs_review' ? 'text-bg-danger' : ($cs === 'estimate' ? 'text-bg-warning' : 'text-bg-secondary')) }}">
                        {{ $chargeStatusLabel[$cs] ?? $cs }}
                     </span>
                  </td>
                  <td>
                     @php $ps = $p['payment_state']; @endphp
                     <span class="badge {{ $ps === 'paid' ? 'text-bg-success' : ($ps === 'unpaid' ? 'text-bg-warning' : 'text-bg-secondary') }}">
                        {{ $paymentStateLabel[$ps] ?? $ps }}
                     </span>
                  </td>
                  <td>
                     @if ($p['charge_id'] && $p['charge_status'] === 'final' && $p['payment_state'] === 'unpaid')
                        <button type="button" class="btn btn-sm btn-primary btn-catat-pembayaran"
                           data-charge-id="{{ $p['charge_id'] }}"
                           data-nama="{{ $p['full_name'] }}"
                           data-amount="{{ $p['amount'] }}">
                           Catat pembayaran
                        </button>
                     @endif
                  </td>
               </tr>
               @endforeach
            </tbody>
         </table>
      </div>
      <small class="text-muted">Biaya tambahan hanya mencakup tiket masuk per tanggal tambahan.</small>
   </div>
</div>

<div class="modal fade" id="modalCatatPembayaran" tabindex="-1" aria-hidden="true">
   <div class="modal-dialog">
      <div class="modal-content">
         <form method="POST" action="{{ route('admin.destinasi.booking.paymentRecord') }}">
            @csrf
            <input type="hidden" name="booking_id" value="{{ $booking->id }}">
            <input type="hidden" name="charge_id" id="paymentChargeId">
            <div class="modal-header">
               <h5 class="modal-title">Catat Pembayaran Biaya Tambahan</h5>
               <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
               <p id="paymentDesc" class="fw-bold"></p>
               <div class="mb-3">
                  <label class="form-label">Metode pembayaran</label>
                  <select name="method" class="form-select" required>
                     <option value="Tunai">Tunai</option>
                     <option value="QRIS">QRIS</option>
                     <option value="Transfer Bank">Transfer Bank</option>
                  </select>
               </div>
               <div class="mb-3">
                  <label class="form-label">Referensi / No. (opsional)</label>
                  <input type="text" name="reference" class="form-control" maxlength="100">
               </div>
               <div class="mb-3">
                  <label class="form-label">Catatan (opsional)</label>
                  <input type="text" name="note" class="form-control" maxlength="255">
               </div>
            </div>
            <div class="modal-footer">
               <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
               <button type="submit" class="btn btn-success">Simpan Pembayaran</button>
            </div>
         </form>
      </div>
   </div>
</div>
@endif

@if ($booking->status_booking >= 4 && $booking->status_booking <= 8)
<div class="card">
   <div class="card-header">
      <h5><b>Riwayat Pendakian</b></h5>
   </div>
   <div class="card-body">
      <div class="table-responsive">
         <table class="table table-bordered">
            <thead class="bg-dark text-white">
               <tr>
                  <td>Status</td>
                  <td>Tanggal</td>
                  <td>Jam</td>
                  <td>Nama</td>
                  <td>Validator</td>
                  <td>Keterangan</td>
               </tr>
            </thead>
            <tbody class="table-group-divider">
               @foreach ($listStatusPendakian as $status)
               <tr>
                  <td>
                     @php
                     $badgeClass = match($status->status) {
                     1 => 'text-bg-danger', // Merah untuk status 1
                     2 => 'text-bg-primary', // Biru untuk status 2
                     3 => 'text-bg-success', // Hijau untuk status 3
                     default => 'text-bg-secondary', // Warna default jika tidak ada yang cocok
                     };
                     @endphp
                     <span class="badge rounded-pill {{ $badgeClass }}">{{ $status->statusName }}</span>
                  </td>

                  <td>{{$status->tanggal}}</td>
                  <td>{{$status->jam}}</td>
                  <td>{{$status->fullName}}</td>
                  <td>#{{$status->validator ?? '-'}}</td>
                  <td>{{$status->detail}}</td>
               </tr>
               @endforeach
            </tbody>
         </table>
      </div>
   </div>
</div>
@endif


@endsection

@section('js')
<script src="https://cdn.rawgit.com/davidshimjs/qrcodejs/gh-pages/qrcode.min.js"></script>
<script>
   const qrcodes = document.querySelectorAll('.qrcode_kodebooking');
   qrcodes.forEach(e => {
      qr = e.dataset['qr'];
      new QRCode(e, {
         text: qr,
         width: 200,
         height: 200
      });
   });
</script>

<!-- ganti tanggal -->
<script>
   const formGantiDate = document.getElementById('bookingGantiForm');
   const bookingStartDateInput = document.getElementById('bookinfStartDate'); // Pastikan id benar
   const bookingEndDateInput = document.getElementById('bookingEndDate');

   formGantiDate.addEventListener('change', function() {
      const parts = bookingStartDateInput.value.split('-').map(Number);
      const selectedDate = parts.length === 3 && parts.every(Number.isFinite)
         ? new Date(parts[0], parts[1] - 1, parts[2], 12)
         : new Date(NaN);
      const totalDays = parseInt(formGantiDate.totalHari.value, 10) - 1; // Ambil dari input hidden

      if (!isNaN(totalDays) && selectedDate instanceof Date && !isNaN(selectedDate)) {
         selectedDate.setDate(selectedDate.getDate() + totalDays); // Tambahkan hari dengan benar
         bookingEndDateInput.value = `${selectedDate.getFullYear()}-${String(selectedDate.getMonth() + 1).padStart(2, '0')}-${String(selectedDate.getDate()).padStart(2, '0')}`;
      }
   });
</script>

@if ($booking->status_booking >= 4 && $booking->status_booking <= 8)
<!-- JavaScript untuk mengatur tampilan checkbox -->
<script>
   document.addEventListener("DOMContentLoaded", function() {
      let simpanBtn = document.querySelector(".btn-simpan");
      let batalBtn = document.querySelector(".btn-batal");
      let actionButtons = document.querySelectorAll(".btn-aksi");
      let formName = document.getElementById("form-name");
      let checkboxContainer = document.getElementById("checkbox-container");
      let previousState = {};

      function saveState() {
         previousState = {
            batal: Array.from(document.querySelectorAll(".batal-checkbox")).map(chk => chk.checked),
            cekin: Array.from(document.querySelectorAll(".cekin-checkbox")).map(chk => chk.checked),
            cekout: Array.from(document.querySelectorAll(".cekout-checkbox")).map(chk => chk.checked)
         };
      }

      function restoreState() {
         document.querySelectorAll(".batal-checkbox").forEach((chk, i) => chk.checked = previousState.batal[i]);
         document.querySelectorAll(".cekin-checkbox").forEach((chk, i) => chk.checked = previousState.cekin[i]);
         document.querySelectorAll(".cekout-checkbox").forEach((chk, i) => chk.checked = previousState.cekout[i]);
      }

      function disableAllCheckboxes() {
         document.querySelectorAll(".form-check-input").forEach(chk => chk.disabled = true);
      }

      function enableCheckboxes(targetClass, actionName) {
         saveState();
         disableAllCheckboxes();
         document.querySelectorAll(`.${targetClass}`).forEach(chk => chk.disabled = false);
         simpanBtn.classList.remove("d-none");
         batalBtn.classList.remove("d-none");
         actionButtons.forEach(btn => btn.classList.add("d-none"));
         formName.value = actionName;
      }

      function resetAll() {
         restoreState();
         disableAllCheckboxes();
         simpanBtn.classList.add("d-none");
         batalBtn.classList.add("d-none");
         actionButtons.forEach(btn => btn.classList.remove("d-none"));
         checkboxContainer.innerHTML = ""; // Hapus semua input hidden
      }

      function updateHiddenInputs() {
         checkboxContainer.innerHTML = "";
         document.querySelectorAll(".form-check-input").forEach(chk => {
            if (!chk.disabled) {
               let input = document.createElement("input");
               input.type = "hidden";
               input.name = `pendakis[${chk.getAttribute("data-id")}]`;
               input.value = chk.checked ? "1" : "0";
               checkboxContainer.appendChild(input);
            }
         });
      }

      actionButtons.forEach(button => {
         button.addEventListener("click", function() {
            let targetClass = this.getAttribute("data-target");
            let actionName = this.getAttribute("data-name");
            enableCheckboxes(targetClass, actionName);
         });
      });

      batalBtn.addEventListener("click", resetAll);

      document.getElementById("statusForm").addEventListener("submit", function(event) {
         updateHiddenInputs();
      });
   });
</script>
@endif

<script>
// Konfirmasi turun: kumpulkan pendaki yang dipilih menjadi checkout[]/reasons[].
const checkoutForm = document.getElementById('checkoutForm');
if (checkoutForm) {
   checkoutForm.addEventListener('submit', function (e) {
      this.querySelectorAll('input.dynamic-checkout').forEach(i => i.remove());
      let any = false;
      this.querySelectorAll('.checkout-select:checked').forEach(cb => {
         const id = cb.value;
         const timeInput = this.querySelector(`.checkout-time[data-id="${id}"]`);
         const reasonInput = this.querySelector(`.checkout-reason[data-id="${id}"]`);
         const time = timeInput ? timeInput.value : '';
         const reason = reasonInput ? reasonInput.value : '';

         const t = document.createElement('input');
         t.type = 'hidden'; t.name = `checkout[${id}]`; t.value = time; t.className = 'dynamic-checkout';
         this.appendChild(t);

         if (reason) {
            const r = document.createElement('input');
            r.type = 'hidden'; r.name = `reasons[${id}]`; r.value = reason; r.className = 'dynamic-checkout';
            this.appendChild(r);
         }
         any = true;
      });
      if (!any) {
         e.preventDefault();
         alert('Pilih minimal satu pendaki yang sudah turun.');
      }
   });
}

// Catat pembayaran biaya tambahan.
document.querySelectorAll('.btn-catat-pembayaran').forEach(btn => {
   btn.addEventListener('click', function () {
      document.getElementById('paymentChargeId').value = this.dataset.chargeId;
      const rupiah = 'Rp ' + Number(this.dataset.amount).toLocaleString('id-ID');
      document.getElementById('paymentDesc').textContent = this.dataset.nama + ' — ' + rupiah;
      new bootstrap.Modal(document.getElementById('modalCatatPembayaran')).show();
   });
});
</script>
@endsection
