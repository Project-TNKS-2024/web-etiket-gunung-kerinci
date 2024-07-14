@extends('etiket.in.template.index')


@section('css')
    <style>
        .reset-form {
            max-width: 540px;
            padding: 40px 0;
            width: 100%;
        }
    </style>
@endsection

@section('main')
    <div class="position-fixed w-screen h-screen d-flex justify-content-center align-items-center"
        style="left:0; top:0; background: transparent;">
        <div class="reset-form shadow text-center px-5 d-flex flex-column gap-4 rounded-xl card">
            <h4 class="font-semibold py-0 my-0">HALAMAN TIDAK TERSEDIA</h4>
            <div class="text-sm gk-text-neutrals300 py-0 my-0">Periksa kembali alamat URL</div>
            <a class="btn btn-primary gk-bg-primary700 py-2 w-100" href="{{ route('homepage.beranda') }}">Kembali ke
                Beranda</a>
        </div>
    </div>
@endsection
