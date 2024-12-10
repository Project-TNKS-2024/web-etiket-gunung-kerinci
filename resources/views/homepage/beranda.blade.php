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

    .welcome-text {
        color: white;
        text-align: left;
    }

    .welcome-text>.top {
        margin: 5px 0;
        font-size: 30px;
        font-weight: 300;
        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif;
    }

    .welcome-text>.divider {
        /* height: 2px; */
        width: 33%;
        border-bottom: 1px solid white;
    }

    .welcome-text>.bottom {
        padding: 20px 0;
        font-size: 44px;
        font-weight: 600;
        font-family: sans-serif;

    }

    .welcome-text>.booking {
        background: rgba(1, 105, 191, 1);
        font-size: 15px;
        text-decoration: none;
        color: white;
        padding: 10px 20px;
        border-radius: 50px;
        cursor: pointer;
    }

    .welcome-text>.booking:active {
        background: rgba(1, 105, 191, 1);
        color: white;
    }

    .glassmorphic {
        background-color: rgba(255, 255, 255, 0.1);
        backdrop-filter: blur(5px);
        border-radius: 15px;
        border: 1px solid rgba(255, 255, 255, 0.3);
        color: white;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.2);
    }

    .glassmorphic.card {
        padding: 20px 20px;
    }

    .header-right {
        display: flex;
        flex-direction: column;
        gap: 20px;
    }

    .header {
        display: flex;
        gap: 20px;
    }

    .widget {
        font-weight: 600;
        font-size: 20px;
        /* width: 300px; */
    }

    .widget>.info {}

    .widget>.weather {
        /* display: flex; */
        flex-direction: row;
        align-items: center;

    }

    .widget>.status {
        flex-direction: row;
        align-items: center;
    }
</style>
@endsection

@section('main')
<div class="position-relative overflow-hidden text-center bg-body-tertiary">
    <div class="my-5 w-100">
        <div class="row px-4 gap-4 w-100 mx-auto justify-content-center">
            <div class="col-12 col-lg-5 welcome-text">
                <header class="top">Selamat Datang di Website Resmi</header>
                <div class="divider"></div>
                <header class="bottom">Taman Nasional Kerinci Seblat</header>
                <a class="booking" href="{{route('homepage.booking.destinasi.paket', ['id' => 1])}}">Booking Online</a>
            </div>
            <div class="col-12 col-lg-4 welcome-text header-right widget">
                <section class="col-12 glassmorphic card row info">
                    20 orang sedang mendaki Gunung Kerinci
                </section>
                <section class="col-12 glassmorphic card row weather ">
                    <div class="col-7">Cuaca Gunung Kerinci Hari Ini</div>
                    <div class="col-4">
                        <img width="50" id="weather-icon" />
                        <span style="font-weight: 300; font-size: 24px;"><span id="temp_c"></span>&deg;c</span>
                    </div>
                </section>
                <section class="col-12 glassmorphic card row status">
                    <div class="col-7">Status Gunung Kerinci</div>
                    <div class="col-4" style="display: flex; gap: 10px;" id="status-gunung">
                        <section
                            style="width: 20px; height: 20px; border-radius: 100%; background: rgba(251, 112, 49, 1);">
                        </section>
                        <span id="status-text" style="font-size: 12px; font-weight: 500;"> II Waspada</span>
                    </div>
                </section>
            </div>
        </div>
    </div>
</div>

<script>
    // Your API key and endpoint
    const apiKey = '579c99aaf8c043fb95e90132240912'; // Replace with your actual API key
    const url = `http://api.weatherapi.com/v1/current.json?key=${apiKey}&q=-1.7,101.267&aqi=no`;

    // Fetch data from the API
    fetch(url)
    .then(response => {
        if (!response.ok) {
        throw new Error('Network response was not ok');
        }
        return response.json();
    })
    .then(data => {
        const iconImg = document.getElementById("weather-icon");
        const temp_c = document.getElementById("temp_c");
        iconImg.src = data.current.condition.icon;
        temp_c.innerText = data.current.temp_c
        console.log(data.current.temp_c);
    })
    .catch(error => {
        console.error('There was a problem with the fetch operation:', error);
    });

</script>


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

                <img src="{{$item['foto']}}" class="card-img-top" alt="Jalur Pendakian Kersik Tuo"
                    style="object-fit: cover; max-height: fit-content; height: 100%;">
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
                        Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed euismod, nunc vel tincidunt
                        ultricies, nisl nisl aliquam nisl, eget aliquam nisl nisl eget nisl.
                        Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed euismod, nunc vel tincidunt
                        ultricies, nisl nisl aliquam nisl, eget aliquam nisl nisl eget nisl.
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