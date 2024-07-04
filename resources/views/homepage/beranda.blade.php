@extends('homepage.template.index')

@section('css')
<style>
   .bg-body-tertiary {
      background-image: url("{{asset('img/bg/bg jumbotron 1.png')}}");
      background-size: cover;
   }

   .index-kartu-1 img {
      max-height: 150px;
   }

   .index-kartu-1 .index-text {
      height: 130px;
      overflow: hidden;
      text-align: justify;
   }
</style>
@endsection

@section('main')

<div class="position-relative overflow-hidden text-center bg-body-tertiary">
   <div class="col-md-8 p-lg-5 mx-auto my-5">
      <h5 class="text-white">Selamat Datang Di Website Resmi</h5>
      <h1 class=" text-white mt-3">Taman Nasional Kerinci Seblat</h1>
      <a class="btn btn-primary btn-sm w-100 mt-5" href="#" role="button">Booking Online Sekarang!</a>
   </div>
</div>
<div class="product-device shadow-sm d-none d-md-block"></div>
<div class="product-device product-device-2 shadow-sm d-none d-md-block"></div>

<div class="container mt-5">

   <h3 class="text-center">Jalur Pendakian Gunung Kerinci</h3>
   <div class="row mt-3 index-kartu-1">
      @for ($i = 1; $i <= 5; $i++) <div class="col-12 col-md-6 col-lg-4 mb-3">
         <div class="card h-100">
            <img src="{{ asset('img/sampel/sampel 2.png') }}" class="card-img-top" alt="Jalur Pendakian Kersik Tuo">
            <div class="card-body">
               <h5 class="card-title">Jalur Kersik Tuo <span class="ms-2 badge gk-bg-primary400">Primary</span></h5>
               <p class="card-text index-text">Lorem ipsum dolor sit amet consectetur. Lorem posuere amet non in fermentum. Euismod lectus tellus imperdiet amet condimentum semper nulla ipsum. Tortor ut vestibulum diam maecenas elementum viverra. Sed arcu integer sagittis feugiat diam egestas.</p>
               <a href="#" class="btn gk-bg-primary700 w-100 text-white">Pilih Jalur Pendakian</a>
            </div>
         </div>
   </div>
   @endfor
</div>
</div>


<div class="gk-bg-neutrals200">
   <div class="container">
      <p>ejfbwjfbkjwbfwvbkn j njwsfbjwe</p>
   </div>
</div>

@endsection