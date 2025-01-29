@extends('homepage.template.index')

@section('css')
<style>

</style>
@endsection

@section('main')
@include('homepage.template.header', [
'title' => 'Pendakian Gunung Kerinci',
'caption' => 'Tiket',
])

<div class="container my-5">

   <div class="container ">
      <div class="d-flex justify-content-between mb-3">
         @if (auth()->user()->id === $booking->id_user)
         <a type="button" class="btn btn-primary" href="{{route('homepage.booking.payment', ['id' => $booking->id])}}">Lihat Pembayaran</a>
         @endif
         <button type="button" class="btn btn-primary ms-auto" onclick="printTicket()">Unduh Tiket</button>
      </div>

      <div id="container-print" class="overflow-x-auto">
         @include('homepage.template.tiket.cardBooking', [$booking])
      </div>

   </div>

</div>

</div>
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

<script>
   function printTicket() {
      // Ambil elemen yang ingin dicetak
      const div = document.getElementById('container-print');

      // Buat jendela baru untuk cetak
      const printWindow = window.open('', '_blank', 'width=800,height=600');

      // Tambahkan konten HTML dari elemen ke jendela baru
      printWindow.document.open();
      printWindow.document.write(`
         <html>
         <head>
            <title>Cetak Tiket</title>
            <style>
               body {
                  font-family: Arial, sans-serif;
                  margin: 0;
                  padding: 20px;
               }
               .fixed-card {
                  margin: auto;
                  border-radius: 10px;
                  box-shadow: none;
                  width: 100%;
                  border: none;
               }
               table {
                  width: 100%;
                  border-collapse: collapse;
               }
               .qrcode_kodebooking img {
                  width: 150px;
               }
               hr {
                  border: none;
                  border-top: 2px dashed #555;
               }
            </style>
               <link rel="stylesheet" href="{{ asset('bootstrap-5.3.3-dist/css/bootstrap.min.css') }}">
         </head>
         <body>
            ${div.innerHTML}
         </body>
         </html>
      `);
      printWindow.document.close();

      // Tunggu konten dimuat, lalu cetak
      printWindow.onload = () => {
         printWindow.print();
         printWindow.close();
      };
   }

   // Panggil fungsi cetak melalui tombol
</script>

@endsection