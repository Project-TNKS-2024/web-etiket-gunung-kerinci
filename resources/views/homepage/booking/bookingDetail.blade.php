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
      /* Adjust the alpha value for the desired opacity */
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

   /* booking detail */
   #booking-detail h1 {
      font-size: 20px;
      text-align: center;
      font-weight: bold;
   }

   #booking-detail #formulir h4 {
      font-size: 16px;
      font-weight: bold;
   }

   #booking-detail h4,
   #booking-detail p {
      color: rgba(52, 64, 84, 1);
   }

   .c-blue {
      color: blue;
   }

   .c-red {
      color: red;
   }

   .c-green {
      color: #00e221;
   }

   #booking-detail #pembayaran {
      background-color: lightcyan;
   }

   #booking-detail #pembayaran h4 {
      font-size: 16px;
      font-weight: bold;
      text-align: center;
   }

   #booking-detail #pembayaran p {
      display: flex;
      justify-content: space-between;
      align-items: center;
   }

   #booking-detail p {
      margin-bottom: 8px;
   }

   #booking-detail #pembayaran .span {
      font-size: 11px;
   }

   .centered {
      display: flex;
      justify-content: center;
   }

   .pilihmetodepembayaran {
      /* margin-top: 5px; */
      /* background-color: #e0ffff; */
      padding: 5px;
      border-radius: 10px;
      /* border: 1px solid #9adbdb; */
   }

   .pilihmetodepembayaran input {
      margin-left: 5px !important;
      margin-right: 10px;
      border: 1px solid #9adbdb;
   }

   /* bukin css gradian */
</style>
@endsection


@section('main')
@include('homepage.template.header', [
'title' => 'Pendakian Gunung Kerinci',
'caption' => 'Detail Booking',
])

<div class="container my-5">
   @include('homepage.booking.booking-nav', ['step' => 2])

   <div class="card border-0 shadow">
      <div class="card-body px-4 px-md-5 pb-4">
         <div class="mt-3">
            <h1 class="fs-5 fw-bold">Booking {{$booking->gateMasuk->destinasi->nama}}</h1>
            <div class="row">
               <div class="col-12 col-md-6">
                  <table class="table table-borderless">
                     <tr>
                        <td>Nama Ketua</td>
                        <td> : </td>
                        <td>{{$formulirPendakis[0]->first_name . ' ' . $formulirPendakis[0]->last_name}}</td>
                     </tr>
                     <tr>
                        <td>Gerbang Masuk</td>
                        <td> : </td>
                        <td>{{$booking->gateMasuk->nama}}</td>
                     </tr>
                     <tr>
                        <td>Gerbang Keluar</td>
                        <td> : </td>
                        <td>{{$booking->gateKeluar->nama}}</td>
                     </tr>
                     <tr>
                        <td>Check In</td>
                        <td> : </td>
                        <td>{{ \Carbon\Carbon::parse($booking->tanggal_masuk)->isoFormat('D MMMM Y') }}</td>
                     </tr>
                     <tr>
                        <td>Check Out</td>
                        <td> : </td>
                        <td>{{ \Carbon\Carbon::parse($booking->tanggal_masuk)->isoFormat('D MMMM Y') }}</td>
                     </tr>
                     <tr>
                        <td>Jumlah Pendaki</td>
                        <td> : </td>
                        <td>{{$booking->total_pendaki_wni}} WNI dan {{$booking->total_pendaki_wna}} WNA</td>
                     </tr>
                  </table>
               </div>
               <div class="col">
                  <h4>SIMAKSI</h4>
                  <p>
                     @if ($booking->lampiran_simaksi == null)
                     <span class="c-red">Tidak</span>
                     @else
                     <span class="c-green">Ya</span>
                     @endif
                  </div>
               </div>
            </div>
         </div>
         @endforeach
         <hr>
         <div class="mt-3">
            <h1 class="fs-5 fw-bold">Barang Bawaan Wajib</h1>
            <div class="form-check">
               <input class="form-check-input" type="checkbox" name="barangWajib[perlengkapan_gunung_standar]" value="1" checked readonly>
               <label class="form-check-label" for="perle_gunung">
                  Perlengkapan Standar Pendaki Gunung
               </label>
            </div>

            <div class="form-check">
               <input class="form-check-input" type="checkbox" name="barangWajib[trash_bag]" value="1" id="trash_bag" checked readonly>
               <label class="form-check-label" for="trash_bag">
                  Trash Bag
               </label>
            </div>

            <div class="form-check">
               <input class="form-check-input" type="checkbox" name="barangWajib[p3k_standart]" value="1" id="p3k_standart" checked readonly>
               <label class="form-check-label" for="p3k_standart">
                  P3K Standart
               </label>
            </div>

            <div class="form-check">
               <input class="form-check-input" type="checkbox" name="barangWajib[survival_kit_standart]" value="1" id="survival_kit_standart" checked readonly>
               <label class="form-check-label" for="survival_kit_standart">
                  Survival Kit Standart
               </label>
            </div>
         </div>
      </div>
   </div>

   <div class="row">
      <div class="col-12 col-md-4"></div>
      <div class="col-12 col-md-4">
         <a type="submit" class="btn btn-primary w-100 fw-bold mt-3" href="{{route('homepage.booking.payment', ['id' => $booking->id])}}">Selanjutnya</a>
      </div>
   </div>
</div>
@endsection
@section('js')




<script type="text/javascript"
   src="https://app.sandbox.midtrans.com/snap/snap.js"
   data-client-key="SB-Mid-client-8mWWkdmzeR1xRVmL"></script>
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
