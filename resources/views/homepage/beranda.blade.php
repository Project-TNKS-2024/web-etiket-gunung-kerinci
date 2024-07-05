@extends('homepage.template.index')

@section('css')
    <style>
        .bg-body-tertiary {
            background-image: url("{{ asset('img/bg/bg jumbotron 1.png') }}");
            background-size: cover;
            padding-bottom: 80px;
            z-index: 1;
            height: 100%;
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
            z-index: 1;
        }

        .index-kartu-1 img {
            max-height: 150px;
        }

        .index-kartu-1 .index-text {
            height: 130px;
            overflow: hidden;
            text-align: justify;
        }

        .glass {
            background: rgba(0, 0, 0, 0.2);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            padding: 20px;
            text-align: center;
        }

        .border-between {
            border-top: 2px solid var(--base-white);
            width: 100px;
        }

        .widget-container {
            width: 100%;
            z-index: 2;
            position: absolute;
            display: flex;
            bottom: -10px;
            justify-content: end;
            gap: 10px;
            padding: 0px 20px;
            height: 100px;
            /* mix-blend-mode: difference; */

        }

        .widget {
            height: 100px;
            width: 120px;
            z-index: 2;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 2px;
            padding: 0;
            color: var(--base-white);
        }

        .widget-header {
            font-size: 15px;
        }

        .widget-caption {
            font-size: 15px;
            font-weight: 300;
            mix-blend-mode: difference;

        }

        .widget-content {
            height: fit-content;
            align-items: center;
            width: 100%;
            justify-content: center;
            font-size: 15px;
            font-weight: 300;
            padding: 0;
            mix-blend-mode: difference;

        }

        .widget-content img {
            width: 20px;
        }

        @media (max-width: 1000px) {
            .widget-container {
                bottom: -50px;

            }
        }

        @media (max-width: 600px) {
            .widget-container {
                justify-content: center;
            }
        }
    </style>
@endsection

@section('main')
    <div class="position-relative text-center bg-body-tertiary m-0 py-5 " style="overflow: visible">
        <div class="col-sm-8 col-md-8 p-lg-5 mx-auto my-5 mb-3">
            <h5 class="text-white">Selamat Datang Di Website Resmi</h5>
            <div class="border-between m-auto d-block"></div>
            <h1 class=" text-white mt-3">Taman Nasional Kerinci Seblat</h1>
            <a class="btn btn-primary btn-sm w-100 mt-5" href="#" role="button">Booking Online Sekarang!</a>
        </div>

        <div class="widget-container">
            <div class="widget glass rounded " style="right: 10px">
                <div class="widget-header">Kayu Aro</div>
                <div class="widget-content d-flex gap-2"
                    style="height: fit-content; align-items: center; width: 100%; justify-content: center;">
                    <img src="{{ asset('assets/img/cloud-icon.png') }}" alt="Cloud Icon" />
                    <p class="d-block my-auto" style="width: fit-content">28&deg;C</p>
                </div>

                <div class="widget-caption">Cerah</div>
            </div>
            <div class="widget glass rounded" style="right: 140px">
                <div class="widget-header">Solok Selatan</div>
                <div class="widget-content d-flex gap-2"
                    style="height: fit-content; align-items: center; width: 100%; justify-content: center;">
                    <img src="{{ asset('assets/img/cloud-icon.png') }}" alt="Cloud Icon" />
                    <p class="d-block my-auto" style="width: fit-content">27&deg;C</p>
                </div>

                <div class="widget-caption">Cerah</div>
            </div>
        </div>
    </div>
    <div class="product-device shadow-sm d-none d-md-block"></div>
    <div class="product-device product-device-2 shadow-sm d-none d-md-block"></div>

    <div class="container mt-5">

        <h3 class="text-center">Jalur Pendakian Gunung Kerinci</h3>
        <div class="row mt-3 index-kartu-1">
            @for ($i = 1; $i <= 5; $i++)
                <div class="col-12 col-md-6 col-lg-4 mb-3">
                    <div class="card h-100">
                        <img src="{{ asset('img/sampel/sampel 2.png') }}" class="card-img-top"
                            alt="Jalur Pendakian Kersik Tuo">
                        <div class="card-body">
                            <h5 class="card-title">Jalur Kersik Tuo <span class="ms-2 badge gk-bg-primary400">Primary</span>
                            </h5>
                            <p class="card-text index-text">Lorem ipsum dolor sit amet consectetur. Lorem posuere amet non
                                in fermentum. Euismod lectus tellus imperdiet amet condimentum semper nulla ipsum. Tortor ut
                                vestibulum diam maecenas elementum viverra. Sed arcu integer sagittis feugiat diam egestas.
                            </p>
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
