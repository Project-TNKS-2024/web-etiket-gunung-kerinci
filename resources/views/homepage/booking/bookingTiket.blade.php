@extends('homepage.template.index')

@section('css')
<style>
   #qrcode_kodebooking img {
      width: -webkit-fill-available !important;
   }

   .qrcode_kodebooking.pendaki img {

      width: -webkit-fill-available;
      max-width: 150px;
      margin: auto;
   }

   .karcis-per-orang p {
      margin-bottom: 0;
   }

   .table tbody tr td {
      padding: 3px 5px;
   }

   .table {
      --bs-table-bg: transparent;
   }
</style>
@endsection

@section('main')
@include('homepage.template.header', [
'title' => 'Pendakian Gunung Kerinci',
'caption' => 'Tiket',
])

<div class="container my-5">
   <div class="card">
      <div class="card-body">
         <!-- tiket booking -->
         <div class="m-auto p-4 mt-3" style="max-width: 900px;">
            <div class="row">
               <div class="col-md-4">
                  <div class="qrcode_kodebooking" id="qrcode_kodebooking"></div>
                  <label for="ipt_kodebooking" class="w-100 text-center">Kode Booking</label>
                  <input type="text" name="ipt_kodebooking" value="{{ $booking->unique_code }}" id="ipt_kodebooking" class="form-control text-center fw-bold border-0" readonly style="font-size: 18px;">
               </div>
               <div class="col-md-8">
                  <table class="table table-borderless">
                     <tr class="fw-bold">
                        <td>Nama Ketua</td>
                        <td>Simaksi</td>
                     </tr>
                     <tr>
                        <td>{{$pendakis[0]->first_name .' '. $pendakis[0]->last_name}}</td>
                        <td>
                           @if ($booking->lampiran_simaksi == null)
                           <span class="text-danger">Tidak</span>
                           @else
                           <span class="text-success">Ya</span>
                           @endif
                        </td>
                     </tr>
                     <tr class="fw-bold">
                        <td>Gerbang Masuk</td>
                        <td>Gerbang Keluar</td>
                     </tr>
                     <tr>
                        <td>{{$booking->gateMasuk->nama}}</td>
                        <td>{{$booking->gateKeluar->nama}}</td>
                     </tr>
                     <tr class="fw-bold">
                        <td>Check In</td>
                        <td>Check Out</td>
                     </tr>
                     <tr>
                        <td>{{$booking->tanggal_masuk}}</td>
                        <td>{{$booking->tanggal_keluar}}</td>
                     </tr>
                     <tr class="fw-bold">
                        <td>Jumlah Anggota</td>
                        <td>Kewarganegaraan</td>
                     </tr>
                     <tr>
                        <td>
                           {{$booking->total_pendaki_wni + $booking->total_pendaki_wni}} orang
                        </td>
                        <td>
                           <div class="row">
                              <div class="col">
                                 <p>{{$booking->total_pendaki_wni}} WNI</p>
                              </div>
                              <div class="col">
                                 <p>{{$booking->total_pendaki_wna}} WNA</p>
                              </div>
                           </div>
                        </td>
                     </tr>

                  </table>
               </div>
            </div>
         </div>

         <!-- tiket pendaki -->

         <div class="mt-3">
            @foreach($pendakis as $pendaki)
            <!-- <div class="karcis-per-orang overflow-hidden bg-transparent mt-2 m-auto" style="max-width: 800px;">
               <div class="row m-0">
                  <div class="col-8" style="background-color: rgba(106, 169, 152, 1); color: white;">
                     <p>Nomor Seri : {{ $pendaki->id }} /TNKS/NUS</p>
                     <p>Karcis Masuk Pengunjung</p>
                     <p>Pendakian Gunung Kerinci Melalui Pos 10</p>
                     <p>Nama Pendaki : {{ $pendaki->nama }}</p>
                     <p>Berlaku untuk satu orang</p>
                     <p>Rp. {{ number_format($pendaki->tagihan, 0, ',', '.') }}</p>
                  </div>
                  <div class="col-4 p-3" style="background-color: rgba(255, 209, 51, 1); ">
                     <div class="bg-white p-1">
                        <div class="qrcode_kodebooking pendaki pt-3" id="qrcode_{{ $pendaki->id }}"></div>
                        <p class="text-center fw-bold mb-0">Scan disini</p>
                     </div>
                  </div>
               </div>
            </div> -->
            <div class="karcis-per-orang overflow-hidden bg-transparent mt-2 m-auto" style="max-width: 800px;">
            </div>
            @endforeach
         </div>

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