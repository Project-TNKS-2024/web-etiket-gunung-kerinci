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
<div class="position-relative overflow-hidden text-center bg-body-tertiary" style="">
    <div class="my-5 w-100">
        <div class="row px-4 gap-4 w-100 mx-auto justify-content-center">
            <div class="col-12 col-lg-5 welcome-text">
                <header class="top">Selamat Datang di Website Resmi</header>
                <div class="divider"></div>
                <header class="bottom">Taman Nasional Kerinci Seblat</header>
                <a class="booking" href="{{ route('homepage.booking.destinasi.paket', ['id' => 1]) }}">Booking Online</a>
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

<div class="container">
    <div class="row" style="overflow-x:hidden">
        <div class="col-12 col-lg-6 position-relative" id="">
            <div class="" id="image-container" style="overflow: hidden;">
                <div class="d-flex align-items justify-content-start "
                    style="gap: 0px; padding: 50px 0; padding-left: 0px; user-select: none;">

                    @foreach ($destinasi as $item)
                    <img data-id="{{ $item['id'] }}" draggable="false" id="gambar-destinasi-{{ $loop->index }}"
                        src="{{ $item->gambar_destinasi[0]->src ?? asset('images/placeholder.jpg') }}" class="shadow card-img-top"
                        style="@if ($loop->index == 0) margin-left: 150px; transform: scale(1.1); @elseif ($loop->last) @endif border-radius: 20px; max-width: 300px; height: 400px; object-fit: cover; user-select: none; margin: 0 15px;">
                    @endforeach
                    <div style="width: 300px;"></div>
                </div>
            </div>
        </div>
        <div class="col-12 col-lg-6" style="overflow-y:hidden;">
            <div class="" style="padding: 50px 20px;">
                <div class="" style="max-height: 100px; height: 50px; overflow-y: hidden; position: relative;">
                    @foreach ($destinasi as $item)
                    <h1 data-id="{{ $item['id'] }}" id="judul-destinasi-{{ $loop->index }}"
                        class="judul fw-bold @if ($loop->index != 0)  @endif"
                        style="color: rgba(43, 43, 43, 1); position: absolute; top: {{ $loop->index * 120 }}px;">
                        {{ $item['nama'] }}
                    </h1>
                    @endforeach
                </div>
                @foreach ($destinasi as $item)
                <article data-id="{{ $item['id'] }}" id="detail-destinasi-{{ $loop->index }}"
                    class="mt-0 @if ($loop->index != 0) d-none @endif">
                    <section>
                        {{ $item['detail'] }}
                    </section>
                    <section class="mt-3">
                        <button class="btn btn-primary gk-bg-primary600 border-0 text-white rounded-pill">Explore
                            Sekarang</button>
                    </section>
                </article>
                @endforeach

            </div>
        </div>
    </div>
</div>

<div class="container my-5">
    <header class="">
        <h4 class="text-center font-semibold">Jelajah TNKS Dalam Angka</h4>
    </header>

    <div class="card w-100 rounded-3 border-0 shadow p-5" style="   ">
        <div class="row h-100">
            <div class="col-12 col-md-6 h-100">
                <div class="d-flex align-items-start justify-content-center h-100" style="flex-direction: column;">
                    <h3 class="fw-semibold">Kondisi Pendakian Gunung Kerinci</h3>
                    <div class="small text-mute">Informasi Realtime Data Pengunjung Gunung Kerinci</div>
                </div>
            </div>
            <div class="col-12 col-md-6 mt-2 mt-sm-0">
                <div class="row">
                    <div class="col">
                        <h4 class="my-0 py-0" id="total-pendaki">0</h4>
                        <div class="my-0 py-0" class="">Total Pendaki</div>
                    </div>
                    <div class="col">
                        <h4 class="my-0 py-0" id="sedang-mendaki">0</h4>
                        <div class="my-0 py-0" class="">Sedang Mendaki</div>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col">
                        <h4 class="my-0 py-0" id="pendaki-wni">0</h4>
                        <div class="my-0 py-0" class="">Pendaki WNI</div>
                    </div>
                    <div class="col">
                        <h4 class="my-0 py-0" id="pendaki-wna">0</h4>
                        <div class="my-0 py-0" class="">Pendaki WNA</div>
                    </div>
                </div>
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
                // if (isset($item['foto'])) {
                //     $headers = @get_headers($item['foto']);
                //     if ($headers && strpos($headers[0], '200')) {
                //         // echo "<script>console.log('Foto ada');</script>";
                //     } else {
                //         // echo "<script>console.log('Link foto tidak valid');</script>";
                //         $item['foto'] = asset('assets/img/cover/kerinci.png');
                //     }
                // } else {
                //     // echo "<script>console.log('Link foto tidak ada');</script>";
                //     $item['foto'] = asset('assets/img/cover/kerinci.png');
                // }
                ?>

                <img src="{{ $item->gambar_destinasi[0]->src ?? asset('assets/img/cover/kerinci.png') }} "
                    class="card-img-top" alt="Jalur Pendakian Kersik Tuo"
                    style="object-fit: cover    ; height: 300px;">
                <div class="card-body">
                    <h5 class="card-title">{{ $item['nama'] }}
                        @if ($item['status'] == 1)
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
<!-- <script src="{{ asset('componen/generateInput.js') }}"></script> -->
<script>
    const container = document.getElementById('image-container');
    const imgs = container.querySelectorAll('img');
    const judul = document.querySelectorAll('.judul')
    let isDragging = false;
    let startX = 0;
    let scrollLeft = 0;

    // Mouse Events for desktop
    container.addEventListener('mousedown', (e) => {
        isDragging = true;
        startX = e.pageX - container.offsetLeft;
        scrollLeft = container.scrollLeft;
        container.style.cursor = 'grabbing';

        // Get all image elements inside the container
    });

    imgs.forEach(element => {
        element.addEventListener('click', (e) => {
            // Loop through all images and reset their styles
            imgs.forEach(img => {
                img.style.transition = "height 0.3s ease, transform 0.3s ease";
                img.style.transform = "scale(1)"; // Reset scale
            });

            idDestinasi = parseInt(element.getAttribute('data-id')) - 1;

            judul.forEach(j => {
                j.style.transform = `translateY(-${idDestinasi*120}px)`
                judulDestinasi = parseInt(j.getAttribute('data-id')) - 1;
                detailDestinasi = document.getElementById('detail-destinasi-' + judulDestinasi);
                if (judulDestinasi == idDestinasi) {
                    detailDestinasi.classList.remove('d-none');
                    j.style.position = `${idDestinasi*120}`
                    j.style.transition = "height 0.3s ease, transform 0.3s ease";
                } else {
                    ;

                    detailDestinasi.classList.add('d-none');
                }
            });

            // Apply styles to the clicked element
            element.style.transition = "height 0.3s ease, transform 0.3s ease";
            element.style.transform = "scale(1.1)"; // Scale up
        });
    });


    container.addEventListener('mousemove', (e) => {
        if (!isDragging) return;
        e.preventDefault();
        const x = e.pageX - container.offsetLeft;
        const walk = (x - startX) * 1;
        container.scrollLeft = scrollLeft - walk;

        const containerRect = container.getBoundingClientRect();
        const containerCenter = containerRect.x + containerRect.width / 2;
    });


    container.addEventListener('mouseup', () => {
        isDragging = false;
        container.style.cursor = 'grab';
    });

    container.addEventListener('mouseleave', () => {
        isDragging = false;
        container.style.cursor = 'grab';
    });

    // Touch Events for mobile
    container.addEventListener('touchstart', (e) => {
        isDragging = true;
        startX = e.touches[0].pageX - container.offsetLeft;
        scrollLeft = container.scrollLeft;
    });

    container.addEventListener('touchmove', (e) => {
        if (!isDragging) return;
        const x = e.touches[0].pageX - container.offsetLeft;
        const walk = (x - startX) * 1; // Adjust sensitivity for mobile
        container.scrollLeft = scrollLeft - walk;
    });

    container.addEventListener('touchend', () => {
        isDragging = false;
    });
</script>

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

<script>
    function animateNumber(id, targetNumber, duration) {
        const element = document.getElementById(id);
        // const targetNumber = parseInt(element.textContent);
        const startTime = performance.now();
        const startNumber = 0;

        function updateNumber(currentTime) {
            const elapsedTime = currentTime - startTime;
            const progress = Math.min(elapsedTime / duration, 1); // Ensure it doesn't exceed 1
            const currentNumber = Math.floor(progress * targetNumber); // Calculate current value
            element.textContent = currentNumber.toLocaleString('en-US'); // Update the element's text

            if (progress < 1) {
                requestAnimationFrame(updateNumber); // Continue animation
            } else {
                element.textContent = targetNumber.toLocaleString('en-US'); // Ensure the final value is accurate
            }
        }

        requestAnimationFrame(updateNumber);
    }

    animateNumber("total-pendaki", 5245, 1000);
    animateNumber("sedang-mendaki", 245, 1000);
    animateNumber("pendaki-wni", 4683, 1000);
    animateNumber("pendaki-wna", 562, 1000);
</script>
@endsection