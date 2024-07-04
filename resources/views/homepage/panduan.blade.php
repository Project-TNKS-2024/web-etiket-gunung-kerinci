@extends('homepage.template.index')


@section('css')
    <style>
        .pdf-container {
            max-width: 700px;
            width: 100%;
        }

        .header-bg {
            position: relative;
            background: url("{{ asset('assets/img/title-header-bg.png') }}") no-repeat;
            background-size: cover;
            background-position: 50% 50%;
            color: white;
        }


        .header-bg::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.6);
            /* Adjust the alpha value for the desired opacity */
            z-index: 1;
        }

        .header-content {
            position: relative;
            z-index: 2;
        }

        .border-between {
            border-top: 2px solid white;
            width: 50px;
            margin: 20px 0;
        }
    </style>
@endsection

@section('main')
    @include('homepage.template.header', [
        'title' => 'Panduan Booking Online',
        'caption' => 'Pendakian Gunung Kerinci Di Taman Nasional Kerinci Seblat',
    ])
    <div class="container my-5">
        @include('homepage.panduan.stepper', [
            'panduan' => [
                [
                    'title' => 'Login atau Daftar',
                    'caption' =>
                        "We handle all aspects of vetting and choosing the right team that you don't have the time, expertise, or desire to do.",
                ],
                [
                    'title' => 'Pilih Jalur Pendakian',
                    'caption' =>
                        "We handle all aspects of vetting and choosing the right team that you don't have the time, expertise, or desire to do.",
                ],
                [
                    'title' => 'Isi Formulir Pendakian',
                    'caption' =>
                        "We handle all aspects of vetting and choosing the right team that you don't have the time, expertise, or desire to do.",
                ],
                [
                    'title' => 'Baca SOP Pendakian',
                    'caption' =>
                        "We handle all aspects of vetting and choosing the right team that you don't have the time, expertise, or desire to do.",
                ],
                [
                    'title' => 'Cek Kuota Pendakian',
                    'caption' =>
                        "We handle all aspects of vetting and choosing the right team that you don't have the time, expertise, or desire to do.",
                ],
                [
                    'title' => 'Bayar Tiket',
                    'caption' =>
                        "We handle all aspects of vetting and choosing the right team that you don't have the time, expertise, or desire to do.",
                ],
            ],
        ])
    </div>
@endsection
