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

<div class="card">
   <div class="card-header">
      <h5><b>Detail Booking</b></h5>
      <span class="badge bg-primary">Cek in</span>
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
                        <td>{{ $booking->pendakis[0]->biodata->first_name .' '. $booking->pendakis[0]->biodata->last_name }}</td>
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
            <h6 class="text-center"><b>Progres booking</b></h6>
            <div class="progress my-1" style="height: 25px;">
               <div class="progress-bar bg-success" role="progressbar"
                  style="width: {{ ($booking->status_booking / 8) * 100 }}%;"
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
         </div>
      </div>
   </div>
</div>

<div class="card">
   <div class="card-body py-3">
      <div class="text-end">
         <a href="{{route('admin.destinasi.booking.struk.show', ['id' => $booking->id])}}" class="btn btn-warning">Ubah Tanggal Pendakian</a>
         <a href="{{route('admin.destinasi.booking.payment.show', ['id' => $booking->id])}}" class="btn btn-primary">Cek Pembelian</a>
         <a href="{{route('admin.destinasi.booking.tiket.show', ['id' => $booking->id])}}" class="btn btn-primary">Cek Tiket</a>
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
            <button type="button" class="btn btn-primary btn-aksi btn-cekin" data-target="cekin-checkbox" data-name="2">Cek in</button>
            <button type="button" class="btn btn-primary btn-aksi btn-cekout" data-target="cekout-checkbox" data-name="3">Cek out</button>
            <button type="button" class="btn btn-danger btn-batal d-none">Batal</button>
            <form method="POST" action="{{route('admin.destinasi.booking.updateStatus')}}" class="d-inline">
               @if ($booking->status_booking == 7)
               @csrf
               <input type="hidden" name="id" value="{{$booking->id}}">
               <input type="hidden" name="status" value="8">
               <button type="submit" class="btn btn-success">Selesai</button>
               @endif
            </form>
         </div>
      </div>
   </div>
</div>

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
                  <td>{{$status->detail}}</td>
               </tr>
               @endforeach
            </tbody>
         </table>
      </div>
   </div>
</div>

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
@endsection