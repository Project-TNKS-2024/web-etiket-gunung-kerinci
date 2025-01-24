@extends('homepage.template.index')


@section('css')
<style>
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
   <div class="card border-0 shadow snk">
      <div class="card-body px-4 px-md-5">
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
   </div>
   <button type="submit" class="btn btn-primary gk-bg-primary700 border-0 w-100 mt-3">Selanjutnya</button>
   </form>
</div>

@endsection
@section('js')

@endsection