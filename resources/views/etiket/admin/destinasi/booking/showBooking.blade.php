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

   hr {
      border: none;
      border-top: 2px dashed #555;
      /* Warna dan tipe garis */
      margin: 20px 0;
      /* Jarak atas dan bawah */
      width: 100%;
      /* Lebar penuh */
   }

   /* Responsiveness */
   @media (max-width: 786px) {
      .card-body {
         overflow-x: auto;
         white-space: nowrap;
      }

      .table {
         display: inline-block;
         width: auto;
      }
   }


   /* Ensure print layout */
   @media print {
      .fixed-card {
         width: 100%;
         box-shadow: none;
         border: none;
      }

      body {
         margin: 0;
         padding: 0;
         background: none;
      }

      table {
         width: 100%;
      }
   }
</style>

@endsection

@section('main')

<div class="card">
   <div class="card-header">
      <h5><b>Detail Booking</b></h5>
   </div>

   <div class="card-body">
      <h5><b>Tiket Booking</b></h5>
      <div class="row">
         <div class="col-12 col-lg-9">

            <div class="card fixed-card shadow p-3">
               <div class="card-body">
                  <div class="row">
                     <div class="col-12 col-md-8">
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
                     <div class="col-4 text-center card-qrboooking">
                        <div class="qrcode_kodebooking" data-qr='{{$booking->unique_code}}'></div>
                        <label for="ipt_kodebooking" class="w-100">Kode Booking</label>
                        <input type="text" name="ipt_kodebooking" value="{{ $booking->unique_code }}" id="ipt_kodebooking"
                           class="form-control text-center fw-bold border-0" readonly>
                     </div>
                  </div>
               </div>
            </div>
         </div>
         <div class="col">
            <div class="card shadow">
               <div class="card-header">
                  <h6><b>Status booking</b></h6>
               </div>
               <div>
                  <div class="card-body">
                     <fieldset disabled>
                        <div class="form-check">
                           <input class="form-check-input" type="checkbox" value="" id="flexCheckDefault" {{ $booking->status_booking >= 4? 'checked' : ''}}>
                           <label class="form-check-label" for="flexCheckDefault">
                              Pembayaran
                           </label>
                        </div>
                        <div class="form-check">
                           <input class="form-check-input" type="checkbox" value="" id="flexCheckDefault" {{ $booking->status_booking >= 5? 'checked' : ''}}>
                           <label class="form-check-label" for="flexCheckDefault">
                              Konfirmasi Pendakian
                           </label>
                        </div>
                        <div class="form-check">
                           <input class="form-check-input" type="checkbox" value="" id="flexCheckDefault" {{ $booking->status_booking >= 6? 'checked' : ''}}>
                           <label class="form-check-label" for="flexCheckDefault">
                              Cek In
                           </label>
                        </div>
                        <div class="form-check">
                           <input class="form-check-input" type="checkbox" value="" id="flexCheckDefault" {{ $booking->status_booking >= 7? 'checked' : ''}}>
                           <label class="form-check-label" for="flexCheckDefault">
                              Cek oUT
                           </label>
                        </div>
                     </fieldset>

                     @if ($booking->status_booking < 7)
                        <div class="mt-2">
                        <a href="" class="btn btn-primary w-100" data-bs-toggle="modal" data-bs-target="#modalUpdateStatus">Update</a>
                  </div>
                  @endif
               </div>
            </div>
         </div>
      </div>
   </div>

   <h5><b>Anggota Pendaki</b></h5>
   <div class="table-responsive">
      <table class="table table-bordered ">
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
               <td>{{$pendaki->biodata->kenegaraan == 'wni' ? 'Warga Negara Indoneia' : 'Warga Negara Asing' }}</td>
               <td>{{$pendaki->biodata->jenis_kelamin=='l' ? 'Laki - laki' : 'Perempuan' }}</td>
               <td>
                  <div class="form-check">
                     <input class="form-check-input" type="checkbox" value="" id="flexCheckDefault">
                  </div>
               </td>
               <td>
                  <div class="form-check">
                     <input class="form-check-input" type="checkbox" value="" id="flexCheckDefault">
                  </div>
               </td>
               <td>
                  <div class="form-check">
                     <input class="form-check-input" type="checkbox" value="" id="flexCheckDefault">
                  </div>
               </td>

               <td>
                  <a href="" class="btn btn-sm btn-warning"><i class="fa-solid fa-eye"></i></a>
               </td>
            </tr>
            @endforeach
         </tbody>
      </table>
   </div>

   <div class="mt-2 text-end">
      <a href="{{route('admin.destinasi.booking.struk.show', ['id' => $booking->id])}}" class="btn btn-primary">Cek Struk Pembelian</a>
      <a href="{{route('admin.destinasi.booking.tiket.show', ['id' => $booking->id])}}" class="btn btn-primary">Cek Tiket</a>
      @if ($booking->status_booking == 7)
      <form method="POST" action="{{route('admin.destinasi.booking.updateStatus')}}" class="d-inline">
         @csrf
         <input type="hidden" name="id" value="{{$booking->id}}">
         <input type="hidden" name="status" value="8">
         <button type="submit" class="btn btn-success">Selesai</button>
         @endif
      </form>
   </div>
</div>
</div>


<!-- Modal Update Status -->
@if ($booking->status_booking < 8)
   <div class="modal fade" id="modalUpdateStatus" tabindex="-1">
   <div class="modal-dialog">
      <div class="modal-content">
         <form method="POST" action="{{route('admin.destinasi.booking.updateStatus')}}">
            @csrf
            <input type="hidden" name="id" value="{{$booking->id}}">
            <div class="modal-header">
               <h5 class="modal-title">Perbarui Status Booking</h5>
               <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
               <select name="status" class="form-select" required>
                  <option value="" selected disabled>-- Pilih Status --</option>
                  <option value="5" {{$booking->status_booking >= 5 ? 'disabled' :''}}>Konfirmasi</option>
                  <option value="6" {{$booking->status_booking >= 6 ? 'disabled' :''}}>Cek In</option>
                  <option value="7" {{$booking->status_booking >= 7 ? 'disabled' :''}}>Cek Out</option>
               </select>
            </div>
            <div class="modal-footer">
               <button type="submit" class="btn btn-primary">Simpan</button>
               <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
            </div>
         </form>
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
   @endsection