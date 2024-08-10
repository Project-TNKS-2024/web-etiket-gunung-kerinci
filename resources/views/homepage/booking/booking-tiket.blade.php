@extends('homepage.template.index')

@section('css')
<style>
   .header-bg {
      position: relative;
      background: url("{{ asset('assets/img/bg/title-header-bg.png') }}") no-repeat;
      background-size: cover;
      background-position: 50% 50%;
      color: white;
   }

   .header-bg::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background-color: rgba(0, 0, 0, 0.6);
      z-index: 1;
   }

   .header-content {
      position: relative;
      z-index: 2;
   }

   .border-between {
      border-top: 2px solid white;
      width: 50px;
      margin: 20px 0;
   }

   /* tiket */

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
   }

   .karcis-per-orang p {
      margin-bottom: 5px !important;
   }

   .qr_code_booking {
      padding: 15px;
      margin: 5px;
      border-radius: 10px;
      background-color: #ffffff;
   }

   .qrcode_kodebooking.pendaki img {

      width: -webkit-fill-available;
      max-width: 200px;
      margin: auto;
   }
</style>
@endsection

@section('main')
@include('homepage.template.header', [
'title' => 'Pendakian Gunung Kerinci',
'caption' => 'Tiket',
])

<div class="container my-5">
   <div class="mb-3">
      <button type="" class="ms-auto d-block w-auto btn btn-primary d-none">Cetak Tiket</button>
   </div>
   <div id="tiket-booking">
      <div class="card m-auto" style="max-width: 900px;">
         <div class="card-body">
            <form id="inform-booking">
               <div class="row">
                  <div class="col-md-4">
                     <div class="qrcode_kodebooking" id="qrcode_kodebooking"></div>
                     <label for="ipt_kodebooking" class="w-100 text-center">Kode Booking</label>
                     <input type="text" name="ipt_kodebooking" value="{{ $booking->unique_code }}" id="ipt_kodebooking" class="form-control text-center fw-bold" readonly style="font-size: 18px;">
                  </div>
                  <div class="col-md-8">
                     <div class="row">
                        <div class="col-12">
                           <span class="badge text-bg-primary" id="ipt_statusbooking" style="margin-left: auto; display: block; width: min-content;">
                              {{-- Status booking --}}
                              @if($booking->status_booking == 4)
                              Sukses
                              @else
                              Menunggu Pembayaran
                              @endif
                           </span>
                        </div>
                        <div class="col-6">
                           <label for="ipt_namaketua" class="fw-bold">Nama Ketua</label>
                           <input type="text" name="ipt_namaketua" value="{{ $pendakis[0]->nama }}" id="ipt_namaketua" class="form-control" readonly>

                           <label for="ipt_gerbangmasuk" class="fw-bold">Gerbang Masuk</label>
                           <input type="text" name="ipt_gerbangmasuk" value="{{ $booking->gateMasuk->nama }}" id="ipt_gerbangmasuk" class="form-control" readonly>

                           <label for="ipt_cekin" class="fw-bold">Cek In</label>
                           <input type="text" name="ipt_cekin" value="{{ $booking->tanggal_masuk }}" id="ipt_cekin" class="form-control" readonly>

                           <label for="ipt_jumlahanggota" class="fw-bold">Jumlah Anggota</label>
                           <input type="text" name="ipt_jumlahanggota" value="{{ $booking->total_pendaki_wni + $booking->total_pendaki_wna }}" id="ipt_jumlahanggota" class="form-control" readonly>
                        </div>
                        <div class="col-6">
                           <label for="ipt_simaksi" class="fw-bold">Simaksi</label>
                           <input type="text" name="ipt_simaksi" value="{{ $booking->id }}" id="ipt_simaksi" class="form-control" readonly>

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
            </form>
         </div>
      </div>

      <div class="mt-3">
         @foreach($pendakis as $pendaki)
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
                  <div class="qrcode_kodebooking pendaki" id="qrcode_{{ $pendaki->id }}"></div>
               </div>
            </div>
         </div>
         @endforeach
      </div>
   </div>
</div>
@endsection

<script src="https://cdn.rawgit.com/davidshimjs/qrcodejs/gh-pages/qrcode.min.js"></script>


@section('js')
<script>
   const div_qr = document.querySelectorAll('.qrcode_kodebooking');

   div_qr.forEach(div => {
      new QRCode(div, {
         text: "{{ $booking->unique_code }}",
         width: 200,
         height: 200
      });
   });

   function printTicket() {
      // Simpan konten yang ingin dicetak dalam variabel
      var printContents = document.getElementById('tiket-booking').innerHTML;

      // Buat jendela baru untuk mencetak
      var originalContents = document.body.innerHTML;
      document.body.innerHTML = printContents;

      // Gunakan perintah cetak
      window.print();

      // Kembalikan konten halaman asli setelah pencetakan
      document.body.innerHTML = originalContents;
   }
</script>
@endsection