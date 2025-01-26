@extends('homepage.template.index')


@section('css')
<style>
    .pdf-container {
        max-width: 700px;
        width: 100%;
    }

    .header-bg {
        position: relative;
        background: url("{{ asset('assets/img/bg/title-header-bg.png') }}") no-repeat;
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

    .guide-container {
        display: flex;
        gap: 50px;
        width: fit-content;
        align-items: stretch;
    }

    /* .guide-container {
            display: flex;
            flex-direction: column;
            max-height: 450px;
            height: 100%;
            flex-wrap: wrap;
            gap: 0;
            padding: 0;
            margin: 0;
            row-gap: 0;
            grid-gap: 0;
            column-gap: 0;
        } */

    .guide-card {
        margin: 0;
        padding: 20px;
        min-width: 150px;
        max-width: 300px;
        min-height: 150px;
        max-height: 300px;
        height: 100%;
        font-size: 1rem;
        display: flex;
        flex-direction: column;
    }

    .guide-card main {
        font-size: 13px;
    }

    .guide-card header {
        margin-bottom: 10px;
    }

    .guide-card header span {
        color: var(--primary700);
    }

    .guide-card-tail {
        width: 1px;
        height: 25px;
        border-right: 2px solid var(--primary700);
    }

    .guide-card-head {
        width: 1px;
        height: 25px;
        border-right: 2px solid var(--primary700);
    }

    .guide-card-border-b {
        border-bottom: 2px solid var(--primary700);
    }

    .guide-card-border-t {
        border-top: 2px solid var(--primary700);
        width: fit-content;
        margin-top: -2px;
    }

    .guide-right {
        padding-right: 50px;
    }

    .guide-left {
        padding-left: 50px;
    }

    @media (max-width: 770px) {
        .guide-container {
            flex-direction: column;
            gap: 10px;
            width: 100%;
            align-items: center;
        }

        .guide-card {
            max-width: 100%;
            width: auto;
            border-bottom: 2px solid var(--primary700);
        }

        .guide-card-tail,
        .guide-card-head,
        .guide-card-border-b,
        .guide-card-border-t {
            border: none;
        }

        .guide-left,
        .guide-right {
            margin: 0;
        }
    }
</style>
@endsection

@section('main')
@include('homepage.template.header', [
'title' => 'Alur Booking Online Tiket Wisata Pendakian Gunung Kerinci',
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
    'title' => 'Verifikasi Akun',
    'caption' =>
    "We handle all aspects of vetting and choosing the right team that you don't have the time, expertise, or desire to do.",
    ],
    [
    'title' => 'Pilih Destinasi',
    'caption' =>
    "We handle all aspects of vetting and choosing the right team that you don't have the time, expertise, or desire to do.",
    ],
    [
    'title' => 'Baca SOP Pendakian',
    'caption' =>
    "We handle all aspects of vetting and choosing the right team that you don't have the time, expertise, or desire to do.",
    ],
    [
    'title' => 'Lakukan Booking',
    'caption' =>
    "We handle all aspects of vetting and choosing the right team that you don't have the time, expertise, or desire to do.",
    ],
    [
    'title' => 'Lakukan Pembayaran',
    'caption' =>
    "We handle all aspects of vetting and choosing the right team that you don't have the time, expertise, or desire to do.",
    ],
    ],
    ])
</div>
@endsection