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
        height: 100vh;
        display: flex;
        align-items: center;
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
                <a href="{{route('homepage.booking.destinasi.paket', ['id' => 1])}}" class="my-0 btn btn-sm border-0 btn-primary gk-bg-primary700 gk-text-base-white rounded-pill">Booking
                    Online</a>
            </div>
        </div>
    </div>
</div>


<div class="container my-5">
    <header class="">
        <h4 class="text-center font-semibold">Destinasi Jelajah Taman Nasional Kerinci Seblat</h4>
    </header>
    <div class="row mt-3">
        @foreach ($destinasi as $item)
        <div class="col-12 col-md-6 col-lg-4 mb-3">
            <div class="card h-100">

                <?php
                // echo "<script>console.log('link " . $item['foto'] . "');</script>";
                if (isset($item['foto'])) {
                    $headers = @get_headers($item['foto']);
                    if ($headers && strpos($headers[0], '200')) {
                        // echo "<script>console.log('Foto ada');</script>";
                    } else {
                        // echo "<script>console.log('Link foto tidak valid');</script>";
                        $item['foto'] = asset('assets/img/cover/kerinci.png');
                    }
                } else {
                    // echo "<script>console.log('Link foto tidak ada');</script>";
                    $item['foto'] = asset('assets/img/cover/kerinci.png');
                }
                ?>

                <img src="{{$item['foto']}}" class="card-img-top" alt="Jalur Pendakian Kersik Tuo" style="object-fit: cover; max-height: fit-content; height: 100%;">
                <div class="card-body">
                    <h5 class="card-title">{{ $item['nama'] }}
                        @if ($item['status']== 1)
                        <span class="ms-2 badge gk-bg-primary400">Open</span>
                        @elseif ($item['status'] == 0)
                        <span class="ms-2 badge gk-bg-error200">Close</span>
                        @endif
                    </h5>
                    <p class="card-text index-text-cardDestinasi">
                        {{ $item['detail'] }}
                        Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed euismod, nunc vel tincidunt ultricies, nisl nisl aliquam nisl, eget aliquam nisl nisl eget nisl.
                        Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed euismod, nunc vel tincidunt ultricies, nisl nisl aliquam nisl, eget aliquam nisl nisl eget nisl.
                    </p>
                    <!-- <a href="{{ $item['id'] }}" class="btn btn-primary w-100 gk-text-base-white">Pilih Jalur
                    Pendakian</a> -->
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>



@endsection

@section('js')
<!-- <script src="{{asset('componen/generateInput.js')}}"></script> -->
@endsection