@extends('homepage.template.index')

@section('css')
<style>
   .bg-body-tertiary {
      background-image: url("{{asset('img/bg/bg jumbotron 1.png')}}");
      background-size: cover;
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
</div>

<div class="container">
   badan
</div>
<div class="container">
   gate
</div>

@endsection