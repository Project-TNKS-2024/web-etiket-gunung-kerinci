@extends('homepage.template.index')


@section('css')
<style>
   /* styele untuk form detail booking */

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
'caption' => 'Detail Booking',
])

<div class="container my-5">
   @include('homepage.booking.booking-nav', ['step' => 2])

   <div id="booking-detail">
      <h1 class="fs-4 fw-bold text-center">Datail Pemesanan</h1>

      <div class="row mt-3">
         <div class="col-12 col-md-6">
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
         <div class="col-12 col-md-6">
            <div class="card" id="pembayaran">
               <div class="card-body">
                  <h1 class="fs-5 fw-bold text-center">Total Pembayaran</h1>
                  <table class="table table-borderless mb-0 bg-transparent">
                     <tr>
                        <td colspan="2">Total harga tiket</td>
                     </tr>
                     @foreach ($booking->pendakis as $pendaki)
                     <tr>
                        <td>{{$pendaki->first_name . $pendaki->last_name}}</td>
                        <td class="text-end">{{$pendaki->tagihan}}</td>
                     </tr>
                     @endforeach
                     <tr>
                        <td>Total Pembayaran</td>
                        <td class="text-end">{{$booking->total_pembayaran}}</td>
                     </tr>
                  </table>
                  <!-- detail -->
                  <p style="font-size: 11px;" class="mb-0" id="labeliptvol">
                     *
                     <span id="countDays">{{$booking->total_hari}}</span>
                     hari
                     <span id="countNights">{{$booking->total_hari -1}}</span>
                     malam
                     (<span class="countDays">{{$booking->total_hari}}</span>D<span class="countNights">{{$booking->total_hari -1}}</span>N)
                  </p>
               </div>
            </div>
         </div>
      </div>
   </div>

   <div class="text-center">
      <a class="btn btn-primary mt-4 me-3" href="{{route('homepage.booking.formulir', ['id' => $booking->id])}}">Formulir</a>
      <button class="btn btn-primary mt-4" href="#" id="pay-button">Selanjutnya</button>
   </div>
</div>

@endsection
@section('js')

<script type="text/javascript"
   src="https://app.sandbox.midtrans.com/snap/snap.js"
   data-client-key="SB-Mid-client-VueHxJqGrsjdNuZd"></script>
<!-- Note: replace with src="https://app.midtrans.com/snap/snap.js" for Production environment -->
</head>


<script type="text/javascript">
   // For example trigger on button clicked, or any time you need
   var payButton = document.getElementById('pay-button');
   payButton.addEventListener('click', function() {
      // Trigger snap popup. @TODO: Replace TRANSACTION_TOKEN_HERE with your transaction token
      window.snap.pay('{{$snaptoken}}', {
         onSuccess: function(result) {
            window.location.href = "{{ route('homepage.booking.payment', ['id' => $booking->id]) }}";
         },
         onPending: function(result) {
            /* You may add your own implementation here */
            alert("wating your payment!");
            console.log(result);
         },
         onError: function(result) {
            /* You may add your own implementation here */
            alert("payment failed!");
            console.log(result);
         },
         onClose: function() {
            /* You may add your own implementation here */
            alert('you closed the popup without finishing the payment');
         }
      });
      // customer will be redirected after completing payment pop-up
   });
</script>
@endsection