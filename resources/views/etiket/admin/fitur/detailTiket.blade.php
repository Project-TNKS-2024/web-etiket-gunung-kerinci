@extends('etiket.admin.template.index')

@section('css')
<style>
   #tiket-booking input {
      border: none;
      background: none;
   }

   #tiket-booking textarea {
      border: none;
      background: none;
   }

   #qrcode_kodebooking img {
      width: -webkit-fill-available !important;
      margin: auto;
   }

   .karcis-per-orang p {
      margin-bottom: 5px !important;
   }

   .qrcode_kodebooking {
      padding: 15px;
      margin: 5px;
      border-radius: 10px;
      background-color: #ffffff;
   }

   .qrcode_kodebooking img {
      width: -webkit-fill-available !important;
      margin: auto;
   }
</style>
@endsection

@section('main')
<div id="tiket-booking">
   <div class="card m-auto" style="max-width: 900px;">
      <div class="card-body">
         <div class="row">
            <div class="col-md-4">
               <div class="qrcode_kodebooking" data-qr="{{ $booking->unique_code }}"></div>
               <!-- <label for="ipt_kodebooking" class="w-100 text-center">Kode Booking</label> -->
               <input type="text" name="ipt_kodebooking" value="{{ $booking->unique_code }}" id="ipt_kodebooking" class="form-control text-center fw-bold" readonly style="font-size: 18px;">
            </div>
            <div class="col-md-8">
               <div class="row">
                  <div class="col-12">
                     <span class="badge text-bg-primary" id="ipt_statusbooking" style="margin-left: auto; display: block; width: min-content;">
                        {{-- Status booking --}}
                        @if($booking->status_booking == 4)
                        Sukses
                        @elseif($booking->status_booking < 4)
                           Sedang booking
                           @else
                           Menunggu Pembayaran
                           @endif
                           </span>
                  </div>
                  <div class="col-6">
                     <label for="ipt_namaketua" class="fw-bold">Nama Ketua</label>
                     <input type="text" name="ipt_namaketua" value="{{ $booking->pendakis[0]->nama }}" id="ipt_namaketua" class="form-control" readonly>

                     <label for="ipt_gerbangmasuk" class="fw-bold">Gerbang Masuk</label>
                     <input type="text" name="ipt_gerbangmasuk" value="{{ $booking->gateMasuk->nama }}" id="ipt_gerbangmasuk" class="form-control" readonly>

                     <label for="ipt_cekin" class="fw-bold">Cek In</label>
                     <input type="text" name="ipt_cekin" value="{{ $booking->tanggal_masuk }}" id="ipt_cekin" class="form-control" readonly>

                     <label for="ipt_jumlahanggota" class="fw-bold">Jumlah Anggota</label>
                     <input type="text" name="ipt_jumlahanggota" value="{{ $booking->total_pendaki_wni + $booking->total_pendaki_wna }}" id="ipt_jumlahanggota" class="form-control" readonly>
                  </div>
                  <div class="col-6">
                     <label for="ipt_simaksi" class="fw-bold">Simaksi</label>
                     @if ($booking->simaksi)
                     <input type="text" name="ipt_simaksi" value="Ada" style="color: green;" id="ipt_simaksi" class="form-control" readonly>
                     @else
                     <input type="text" name="ipt_simaksi" value="Tidak" style="color: red;" id="ipt_simaksi" class="form-control" readonly>
                     @endif

                     <label for="ipt_gerbangkeluar" class="fw-bold">Gerbang Keluar</label>
                     <input type="text" name="ipt_gerbangkeluar" value="{{ $booking->gateKeluar->nama }}" id="ipt_gerbangkeluar" class="form-control" readonly>

                     <label for="ipt_cekout" class="fw-bold">Cek Out</label>
                     <input type="text" name="ipt_cekout" value="{{ $booking->tanggal_keluar }}" id="ipt_cekout" class="form-control" readonly>

                     <label for="ipt_kewarganegaraan" class="fw-bold">Kewarganegaraan</label>
                     <div class="row">
                        <div class="col-6">
                           <input type="text" name="ipt_kewarganegaraanwni" value="{{ $booking->total_pendaki_wni }} WNI" id="ipt_kewarganegaraanwni" class="form-control" readonly>
                        </div>
                        <div class="col-6">
                           <input type="text" name="ipt_kewarganegaraanwna" value="{{ $booking->total_pendaki_wna }} WNA" id="ipt_kewarganegaraanwna" class="form-control" readonly>
                        </div>
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>
   <div class="mt-3">
      @foreach($booking->pendakis as $pendaki)
      <div class="karcis-per-orang overflow-hidden bg-transparent mt-2 m-auto" style="max-width: 800px;">
         <div class="row m-0">
            <div class="col-8" style="background-color: rgba(106, 169, 152, 1); color: white; border-radius: 25px; padding: 20px;">
               <p style="font-size: 18px; font-style: italic;">Nomor Seri : {{ $pendaki->id }} /TNKS/NUS</p>
               <p style="font-size: 18px;">Karcis Masuk Pengunjung</p>
               <p style="font-size: 18px; font-weight: bold;">Pendakian Gunung Kerinci Melalui Pos 10</p>
               <p style="font-size: 18px; font-weight: bold;">Nama Pendaki : {{ $pendaki->nama }}</p>
               <p style="font-size: 18px; font-weight: bold;">Berlaku untuk satu orang</p>
               <p style="font-size: 24px; font-weight: bold;">Rp. {{ number_format($pendaki->tagihan, 0, ',', '.') }}</p>
            </div>
            <div class="col-4" style="background-color: rgba(255, 209, 51, 1); border-radius: 25px; padding: 20px;">
               <div class="qrcode_kodebooking" data-qr="{{ $booking->unique_code }}"></div>
            </div>
         </div>
      </div>
      @endforeach
   </div>
</div>

@endsection

@section('js')
<script src="https://cdn.rawgit.com/davidshimjs/qrcodejs/gh-pages/qrcode.min.js"></script>
<script>
   const div_qr = document.querySelectorAll('.qrcode_kodebooking');

   div_qr.forEach(div => {
      // ambl data
      qr = div.dataset['qr'];
      new QRCode(div, {
         text: qr,
         width: 200,
         height: 200
      });
   });
</script>


@endsection