@extends('homepage.template.index')


@section('css')
<style>
   .pdf-container {
      max-width: 700px;
      width: 100%;
   }

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

   .snk h1 {
      font-weight: bold;
      font-size: 22px;
   }

   .snk h2 {
      font-size: 16px;
      font-weight: bold;
   }

   .snk p,
   .snk li {
      text-align: left;
   }
</style>

@endsection


@section('main')
@include('homepage.template.header', [
'title' => 'Pendakian Gunung Kerinci',
'caption' => 'Syarat dan Ketentuan',
])
<div class="container my-5">
   @include('homepage.booking.booking-nav', ['step' => $status])
   <div class="border border-2 rounded-3 p-3 snk">
      <form method="post" action="{{ route('homepage.booking.snk.action') }}" id="form-snk">
         <input type="hidden" name="id" value="{{$id}}">
         @csrf
         <div id="ketentuan">
            @include('homepage.booking.snk.ketentuan')
         </div>
         <hr>
         <div class="kewajiban">
            @include('homepage.booking.snk.kewajiban')
         </div>
         <hr>
         <div class="kewajiban">
            @include('homepage.booking.snk.larangan')
         </div>
         <div class="form-check mt-2">
            <input class="form-check-input" type="checkbox" name="snk" id="flexCheckDefault" required>
            <label class="form-check-label fw-bold" for="flexCheckDefault">
               Saya sudah membaca, sudah memahami, dan saya setuju dengan persyaratan di atas.
            </label>
         </div>
   </div>
   <button type="submit" class="btn btn-primary w-100 mt-3">Selanjutnya</button>
   </form>
</div>

@endsection
@section('js')

@endsection