@extends('homepage.template.index')

@section('css')
<style>
   .bg-body-tertiary {
      background-image: url("{{asset('img/bg/bg jumbotron 1.png')}}");
      background-size: cover;
      padding-bottom: 80px;
   }

   .bg-body-tertiary::after {
      content: '';
      position: absolute;
      bottom: 0;
      left: 0;
      width: 100%;
      height: 40px;
      /* Adjust height as needed */
      background: linear-gradient(to top, white, rgba(255, 255, 255, 0));
   }

   .index-kartu-1 img {
      max-height: 150px;
   }

   .index-kartu-1 .index-text {
      height: 130px;
      overflow: hidden;
      text-align: justify;
   }

   .index-info h3 {
      margin-bottom: 0px;
   }

   .index-info i {
      font-size: 40px;
      height: min-content;
   }

   .index-info .icon {
      width: 50px;
   }

   .index-info p {
      margin-bottom: 0px;
   }

   .galeri img {
      height: 200px;
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


<div class="container mt-5">

   <h3 class="text-center">Jalur Pendakian Gunung Kerinci</h3>
   <div class="row mt-3 index-kartu-1">
      @for ($i = 1; $i <= 5; $i++)<div class="col-12 col-md-6 col-lg-4 mb-3">
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


<div class="gk-bg-neutrals100 mt-4">
   <div class="container py-5">
      <div class="row">
         <div class="col-12 col-md-6">
            <h2>Kondisi Pendakian Gunung Kerinci</h2>
            <p>Informasi realtime data pengunjung Gunung Kerinci</p>
         </div>
         <div class="col-12 col-md-6 row index-info">
            <div class="col-6 d-flex align-items-center">
               <div class="icon">
                  <i class="fa-solid fa-users"></i>
               </div>
               <div class="ms-2">
                  <h3>5.245</h3>
                  <p>Total Pendaki</p>
               </div>
            </div>
            <div class="col-6 d-flex align-items-center">
               <div class="icon">
                  <i class="fa-solid fa-person-hiking"></i>
               </div>
               <div class="ms-2">
                  <h3>245</h3>
                  <p>Sedang Mendaki</p>
               </div>
            </div>
            <div class="col-6 d-flex align-items-center">
               <div class="icon">
                  <i class="fa-solid fa-person-dress"></i>
               </div>
               <div class="ms-2">
                  <h3>5.245</h3>
                  <p>Pendali Perempuan</p>
               </div>
            </div>
            <div class="col-6 d-flex align-items-center">
               <div class="icon">
                  <i class="fa-solid fa-person"></i>
               </div>
               <div class="ms-2">
                  <h3>5.245</h3>
                  <p>Pendaki Laki-laki</p>
               </div>
            </div>
         </div>
      </div>
   </div>
</div>

<div class="mt-4">
   <div class="container pt-5">
      <div class="w-100">
         <h3 class="text-center">Seputar jelajah TNKS</h3>
      </div>
      <div class="row galeri">
         @for ($i = 1; $i <= 5; $i++) <div class="col-12 col-sm-6 col-lg-4">
            <div class="overflow-hidden shadow-sm rounded-4 mt-4 mx-2">

               <img src="{{asset('img/sampel/sampel 2.png')}}" alt="" class="w-100">
            </div>
      </div>
      @endfor
   </div>
</div>
</div>

<div style="height:50px;"></div>

@endsection