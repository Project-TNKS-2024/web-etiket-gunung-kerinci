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
                  <p>{{$pendakis[0]->nama}}</p>
                  <h4>Gerbang Masuk</h4>
                  <p>{{$booking->gate_masuk->nama}}</p>
                  <h4>Check In</h4>
                  <p>{{$booking->tanggal_masuk}}</p>
                  <h4>Jumlah Anggota</h4>
                  <p>5 orang</p>
               </div>
               <div class="col">
                  <h4>SIMAKSI</h4>
                  <p>
                     @if ($booking->lampiran_simaksi == null)
                     <span class="c-red">Tidak</span>
                     @else
                     <span class="c-green">Ya</span>
                     @endif
                  </p>
                  <h4>Gerbang Keluar</h4>
                  <p>{{$booking->gate_keluar->nama}}</p>
                  <h4>Check out</h4>
                  <p>{{$booking->tanggal_keluar}}</p>
                  <h4>Kewarganegaraan</h4>
                  <div class="row">
                     <div class="col">
                        <p>{{$booking->total_pendaki_wni}} WNI</p>
                     </div>
                     <div class="col">
                        <p>{{$booking->total_pendaki_wna}} WNA</p>
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
                  @foreach ($pendakis as $pen)
                  <p>{{$pen->nama}}<span class="float-right">Rp. {{number_format($pen->tagihan)}}</span></p>
                  @endforeach
                  <p class="fw-bold c-blue">Total <span class="float-right">Rp. {{number_format($booking->total_pembayaran)}}</span></p>
                  <p class="span">*{{$booking->total_hari}} hari {{$booking->total_hari-1}} malam ({{$booking->total_hari}}D{{$booking->total_hari-1}}M)</p>
               </div>
            </div>
         </div>
      </div>

      <div class="centered">
         <a class="btn btn-primary mt-4 me-3" href="{{route('homepage.booking-fp', ['id' => $booking->id])}}">Formulir</a>
         <a class="btn btn-primary mt-4" href="#">Selanjutnya</a>
      </div>

   </div>

</div>
</div>

@endsection
@section('js')

@endsection