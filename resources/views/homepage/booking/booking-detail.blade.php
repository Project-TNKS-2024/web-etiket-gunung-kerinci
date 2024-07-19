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
</style>

@endsection
@section('main')
@include('homepage.template.header', [
'title' => 'Pendakian Gunung Kerinci',
'caption' => 'Syarat dan Ketentuan',
])

<div class="container my-5">
   @include('homepage.booking.booking-nav', ['step' => 2])

   <div id="booking-detail">
      <h1>Detail Pemesanan</h1>

      <div class="row mt-3">
         <div class="col-12 col-md-6" id="formulir">
            <div class="row">
               <div class="col">
                  <h4>Nama Ketua</h4>
                  <p>Pendaki Handal</p>
                  <h4>Gerbang Masuk</h4>
                  <p>Kersik Tua</p>
                  <h4>Check In</h4>
                  <p>14 Agustus 2024</p>
                  <h4>Jumlah Anggota</h4>
                  <p>5 orang</p>
               </div>
               <div class="col">
                  <h4>SIMAKSI</h4>
                  <p><span class="c-red">Tidak</span>/<span class="c-green">Ya</span></p>
                  <h4>Gerbang Masuk</h4>
                  <p>Kersik Tua</p>
                  <h4>Check out</h4>
                  <p>19 Agustus 2024</p>
                  <h4>Kewarganegaraan</h4>
                  <div class="row">
                     <div class="col">
                        <p>3 WNI</p>
                     </div>
                     <div class="col">
                        <p>3 WNA</p>
                     </div>
                  </div>
               </div>
            </div>

            <div>
               <h4>Pilih Metode Pembayaran</h4>



            </div>
         </div>

         <div class="col-12 col-md-6">
            <div class="card" id="pembayaran">
               <div class="card-body">
                  <h4>Total Pembayaran</h4>
                  <p>WNI <span class="float-right">Rp. 40.000</span></p>
                  <p class="fw-bold">3 x WNI <span class="float-right">Rp. 120.000</span></p>
                  <p>WNA <span class="float-right">Rp. 30.000</span></p>
                  <p class="fw-bold">3 x WNA <span class="float-right">Rp. 90.000</span></p>
                  <p class="fw-bold c-blue">Total <span class="float-right">Rp. 210.000</span></p>
                  <p class="span">*2 hari 1 malam (2D1M)</p>
               </div>
            </div>
         </div>
      </div>

      <div class="centered">
         <a class="btn btn-primary mt-4 me-3" href="#">Formulir</a>
         <a class="btn btn-primary mt-4" href="#">Selanjutnya</a>
      </div>

   </div>

</div>
</div>

@endsection
@section('js')

@endsection