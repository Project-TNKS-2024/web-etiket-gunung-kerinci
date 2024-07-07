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
        background-color: rgba(0, 0, 0, 0.4);
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
'title' => 'Standar Operasional Prosedur',
'caption' => 'Pendakian Gunung Kerinci Di Taman Nasional Kerinci Seblat',
])
<div class="container mt-5">
    <div class="">
        {{-- <iframe src="{{ asset('assets/pdf/panduan-sop.pdf') }}"
        class="pdf-container m-auto d-block my-3 border border-1 border border-secondary rounded shadow"
        height="700px"></iframe> --}}
        <p class="m-auto d-block text-center">Jika dokumen tidak muncul, <a href="{{ asset('assets/pdf/panduan-sop.pdf') }}" download>Unduh dokumen</a>.</p>
        <embed src="{{ asset('assets/pdf/panduan-sop.pdf') }}" class="pdf-container m-auto d-block my-3 border border-1 border border-secondary rounded shadow" height="700px" type="application/pdf">

    </div>
</div>
@endsection