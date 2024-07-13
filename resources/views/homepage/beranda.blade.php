@extends('homepage.template.index')

@section('css')
<style>
    .bg-body-tertiary {
        background-image: url("{{ asset('assets/img/bg/bg wide.png') }}");
        background-size: cover;
        background-repeat: no-repeat;
        background-position: center;
        padding-bottom: 80px;
        font-family: 'Poppins';
        margin: 0 auto;
        position: relative;
    }

    .berita-img {
        position: relative;
    }

    .overlay {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        /* Adjust the opacity (last value) as needed */
    }

    .bg-body-tertiary::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        /* Adjust opacity as needed */
        z-index: 1;
        /* Ensure the overlay stays on top of the background image */
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
        z-index: 2;
        /* Ensure the gradient stays on top of the overlay */
    }

    .bg-body-tertiary>* {
        position: relative;
        z-index: 3;
        /* Ensure the content stays on top of everything */
    }

    .index-kartu-1 img {
        max-height: 150px;
    }

    .index-kartu-1 .index-text {
        height: 130px;
        overflow: hidden;
        text-align: justify;
    }

    .galeri .col-6 {
        overflow: hidden;
        border-radius: 0.5rem;
        /* Adjust as needed */
    }

    .galeri img {
        border-radius: 0.5rem;
        max-height: 250px;
    }

    .text-shadow {
        text-shadow: 1px 1px 2px black, 0 0 1em black, 0 0 0.2em black;
    }

    .font-semibold {
        font-weight: 600;
    }

    .text-sm {
        font-size: 12px;
    }

    .status-badge {
        width: 15px;
        height: 15px;
        border-radius: 100%;
    }
</style>
@endsection

@section('main')
<div class="position-relative overflow-hidden text-center bg-body-tertiary">
    <div class="col-md-8 p-lg-5 mx-auto my-5">
        <h5 class="text-white fw-light">Selamat Datang Di Website Resmi</h5>
        <div class="border-between d-block m-auto w-25"></div>
        <h1 class=" text-white mt-3">Taman Nasional Kerinci Seblat</h1>
        <div class="gk-bg-base-white w-100 mt-5 rounded-pill p-2 px-3  d-flex align-items-center justify-content-between">
            <div class="d-flex my-0 gap-3 font-semibold text-sm px-2">
                <div class="d-flex flex-column align-items-center my-0">
                    <div>Status Gunung</div>
                    <div class="d-flex gap-2">
                        <div class="status-badge gk-bg-success200"></div>
                        <div>Normal</div>
                    </div>
                </div>
                <div class="d-flex flex-column align-items-center">
                    <div>Cuaca Saat Ini</div>
                    <div class="d-flex gap-2 align-items-center justify-content-center">
                        <div> <img width="20" src="{{ asset('assets/img/logo/awan.png') }}"></img></div>
                        <div>28&deg;c</div>
                    </div>
                </div>
            </div>
            <div class="my-0">
                <button class="my-0 btn btn-sm border-0 btn-primary gk-bg-primary700 gk-text-base-white rounded-pill">Booking
                    Online</button>
            </div>
        </div>
    </div>
</div>


<div class="container my-5">
    <header class="">
        <h4 class="text-center font-semibold">Destinasi Jelajah</h4>
        <h4 class="text-center font-semibold">Taman Nasional Kerinci Seblat</h4>
        <div class="border-between d-block mx-auto mt-0" style="width: 70px;border-color: var(--base-black)"></div>
    </header>
    <div class="row mt-3 index-kartu-1">
        @include('homepage.beranda.daftar-destinasi', [
        'destinasi' => [
        [
        'name' => 'Gunung Kerinci',
        'description' =>
        'Lorem ipsum dolor sit amet consectetur. Lorem posuere amet non in fermentum. Euismod lectus tellus imperdiet amet condimentum semper nulla ipsum. Tortor ut vestibulum diam maecenas elementum viverra. Sed arcu integer sagittis feugiat diam egestas',
        'cover' => asset('assets/img/cover/kerinci.png'),
        'target' => '#',
        ],
        [
        'name' => 'Danau Kaco',
        'description' =>
        'Lorem ipsum dolor sit amet consectetur. Lorem posuere amet non in fermentum. Euismod lectus tellus imperdiet amet condimentum semper nulla ipsum. Tortor ut vestibulum diam maecenas elementum viverra. Sed arcu integer sagittis feugiat diam egestas',
        'cover' => asset('assets/img/cover/danau-kaco.png'),
        'target' => '#',
        ],
        [
        'name' => 'Gunung Tujuh',
        'description' =>
        'Lorem ipsum dolor sit amet consectetur. Lorem posuere amet non in fermentum. Euismod lectus tellus imperdiet amet condimentum semper nulla ipsum. Tortor ut vestibulum diam maecenas elementum viverra. Sed arcu integer sagittis feugiat diam egestas',
        'cover' => asset('assets/img/cover/gunung-tujuh.png'),
        'target' => '#',
        ],
        ],
        ])
    </div>
</div>


<div class="py-5">
    <div class="container">
        <div class="w-100 mb-4">
            <header class="">
                <h4 class="text-center font-semibold">Seputar Jelajah TNKS</h4>
                <div class="border-between d-block mx-auto mt-0" style="width: 70px;border-color: var(--base-black)">
                </div>
            </header>
        </div>


        <div class="row mt-3 index-kartu-1">
            @for ($i = 1; $i <= 6; $i++) <div class="col-12 col-md-6 col-lg-4 mb-3">
                <div class="card h-100 rounded" style="max-height: 256px; position: relative; overflow:hidden;">
                    <img src="{{ asset('assets/img/cover/document-xx.png') }}" class="rounded berita-img" alt="Jalur Pendakian Kersik Tuo" style="object-fit: cover; max-height: fit-content; height: 100%;"></img>
                    <div class="overlay rounded"></div> <!-- Dark overlay -->
                    <div class="card-body rounded gk-text-base-white d-flex flex-column justify-content-end h-100" style="top: 0; left: 0; position: absolute;">
                        <p class="card-title font-semibold">PEMBATASAN PENDAKIAN GUNUNG KERINCI SAMPAI RADIUS 3 KM
                            DARI KAWAH
                            AKTIF</p>
                        <p class="card-text text-sm py-0 my-0 ">Last uploaded 3 mins ago</p>
                    </div>
                </div>
        </div>
        {{-- <div class="col">
                        <div class="card card-cover overflow-hidden text-bg-dark rounded-4 shadow-lg"
                            style="background-image: url(' {{ asset('assets/img/sampel/sampel 2.png') }}'); background-size: cover;">
        <div class="d-flex flex-column h-100 p-5 pb-5 text-white text-shadow-1">
            <h5 class="pt-5 mt-5 mb-1 text-shadow">Short title, long jacket</h5>
        </div>
    </div>
</div> --}}
@endfor
</div>

</div>
</div>
@endsection