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

@section('main')<div class="position-relative overflow-hidden text-center bg-body-tertiary py-5">
    <div class="container">
        <div class="row g-4 justify-content-center">
            <!-- Bagian Selamat Datang -->
            <div class="col-12 col-md-6 col-lg-8 welcome-text text-lg-start text-center">
                <header class="top">Selamat Datang di Website Resmi</header>
                <div class="divider mx-auto"></div>
                <header class="bottom">Taman Nasional Kerinci Seblat</header>
                <a class="btn btn-primary mt-3" href="{{ route('homepage.booking.destinasi.list') }}">
                    Booking Online
                </a>
            </div>

            <!-- Bagian Info Cuaca & Status Gunung -->
            <div class="col-12 col-md-6 col-lg-3">
                <div class="row g-3">
                    <!-- Info Pendaki -->
                    <div class="col-12 col-sm-4 col-md-12">
                        <section class="glassmorphic card p-3 d-flex align-items-center justify-content-between">
                            <div class="fw-bold">Pendaki Saat Ini</div>
                            <div class="fs-5 fw-semibold text-primary">{{$total_mendaki}} orang</div>
                        </section>
                    </div>

                    <!-- Cuaca Gunung -->
                    <div class="col-12 col-sm-4 col-md-12">
                        <section class="glassmorphic card p-3 d-flex align-items-center justify-content-between">
                            <div>Cuaca Gunung Kerinci</div>
                            <div class="d-flex align-items-center">
                                <img width="40" id="weather-icon" src="{{$weatherData['current']['condition']['icon']}}" alt="Cuaca">
                                <span class="ms-2 fw-bold fs-5">
                                    <span id="temp_c">{{$weatherData['current']['temp_c']}}</span>&deg;C
                                </span>
                            </div>
                        </section>
                    </div>

                    <!-- Status Gunung -->
                    <div class="col-12 col-sm-4 col-md-12">
                        <section class="glassmorphic card p-3 d-flex align-items-center justify-content-between">
                            <div>Status Gunung Kerinci</div>
                            <div class="d-flex align-items-center gap-2">
                                <div style="width: 20px; height: 20px; border-radius: 50%; background: rgba(251, 112, 49, 1);"></div>
                                <span class="fw-bold" id="status-text">{{$destinasi[0]->getStatusGunung()}}</span>
                            </div>
                        </section>
                    </div>
                </div>
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
                        src="{{ $item->gambar_destinasi[0]->src ?? asset('assets/img/sampel/sampel 2.png') }}"
                        class="shadow card-img-top"
                        style="@if ($loop->index == 0) margin-left: 150px; transform: scale(1.1); @elseif ($loop->last) @endif border-radius: 20px; max-width: 300px; height: 400px; object-fit: cover; user-select: none; margin: 0 15px;">
                    @endforeach
                    {{-- <div style="width: 300px;"></div> --}}
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
                <article data-id="{{ $item['id'] }}" id="detail-destinasi-{{ $loop->index }}" class="mt-0 @if ($loop->index != 0) d-none @endif">
                    <section style="display: -webkit-box;-webkit-box-orient: vertical;overflow: hidden;text-overflow: ellipsis;-webkit-line-clamp: 15;">
                        {!! $item['detail'] !!}
                    </section>
                    @if ($item->id == 1)
                    <section class="mt-3">
                        <a href="{{ route('homepage.booking.destinasi.paket', ['id' => $item->id]) }}"
                            class="btn btn-primary gk-bg-primary600 border-0 text-white rounded-pill">Explore
                            Sekarang
                        </a>
                    </section>
                    @endif
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
                <img src="{{ $item->gambar_destinasi[0]->src ?? asset('assets/img/cover/kerinci.png') }} "
                    class="card-img-top" alt="Jalur Pendakian Kersik Tuo"
                    style="object-fit: cover    ; height: 300px;">
                <div class="card-body">
                    <h5 class="card-title">{{ $item['nama'] }}
                        @if ($item['status'] == 1)
                        <span class="ms-2 badge gk-bg-primary400">Open</span>
                        @elseif ($item['status'] == 2)
                        <span class="ms-2 badge gk-bg-primary400">Open Booking</span>
                        @elseif ($item['status'] == 0)
                        <span class="ms-2 badge gk-bg-error200">Close</span>
                        @endif
                    </h5>
                    <div class="card-text index-text-cardDestinasi">
                        {!! $item['detail'] !!}
                    </div>
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

    animateNumber("total-pendaki", @json($total_pendaki), 1000);
    animateNumber("sedang-mendaki", @json($total_mendaki), 1000);
    animateNumber("pendaki-wni", @json($total_pendaki_wni), 1000);
    animateNumber("pendaki-wna", @json($total_pendaki_wna), 1000);
</script>
@endsection