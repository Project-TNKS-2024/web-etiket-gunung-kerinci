@extends('homepage.template.index')


@section('css')
<style>
   .btn-destinasi {
      display: block;
      transition: transform 0.3s ease, box-shadow 0.3s ease;
   }

   .btn-destinasi:hover {
      transform: translateY(-10px);
      /* Mengangkat kotak ke atas */
      box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
      /* Memberikan bayangan */
   }

   .index-text {}
</style>
@endsection

@section('main')
@include('homepage.template.header', [
'title' => 'Pilih Destinasi',
'caption' => '.',
])
<div class="container my-5">
   <div class="row">
      @foreach ($destinasi as $item)
      <div class="col-12 col-md-6 col-lg-4 mb-3">
         <div class="card h-100 btn-destinasi ">
            <a href="{{ route('homepage.booking.destinasi.paket', ['id' => $item->id]) }}" class="text-decoration-none">
               @if(count($item->gambar_destinasi) > 0)
               <img src="{{asset($item->gambar_destinasi[0]->src)}}" class="card-img-top" alt="Jalur Pendakian Kersik Tuo" style="object-fit: cover; max-height: fit-content; height: 100%;">
               @else
               <img src="{{asset('assets/img/sampel/sampel 2.png')}}" class="card-img-top" alt="Default Image" style="object-fit: cover; max-height: fit-content; height: 100%;">
               @endif
               <div class="card-body">
                  <h5 class="card-title">{{$item->nama}}</h5>
                  <p class="card-text index-text-cardDestinasi">
                     {{$item->detail}}
                  </p>
               </div>
            </a>
         </div>
      </div>
      @endforeach
   </div>
</div>
</div>

@endsection

@section('js')

@endsection