@extends('etiket.in.template.index')


@section('css')
@endsection

@section('main')
    <div class="w-100 d-flex flex-column justify-content-center align-items-center mt-5">
        @if (session('success'))
            <div class="shadow rounded-2xl text-center gap-4 w-full d-flex flex-column justify-content-between"
                style="padding: 48px; max-width: 540px;">
                <header class="text-lg font-semibold">Lupa Password</header>
                <div class="font-bold">Silakan Cek email untuk mereset kata sandi</div>
                <div class="gk-text-neutrals500 text-center my-0">
                    Ingat Kata Sandi? <a class="gk-text-primary700" href="{{ route('etiket.in.login') }}">Login Sekarang</a>
                </div>
            </div>
        @else
            <form method="post" action="{{ route('etiket.in.actionlupapassword') }}"
                class="shadow rounded-2xl text-center w-full" style="padding: 48px; max-width: 540px;">
                @csrf
                <header class="text-lg font-semibold">Lupa Password</header>
                <div class="text-start mt-3 d-flex flex-column gap-4">
                    <div class="form-group">
                        <label class="form-label my-1 text-sm mx-2 gk-text-neutrals700">Email</label>
                        <input type="email" class="form-control my-0" name="email" id="email" placeholder="Email"
                            required />
                    </div>
                    <div class="form-group">
                        <button class="btn btn-primary gk-bg-primary700 font-semibold w-100">Reset Password</button>
                    </div>
                    <div class="form-group gk-text-neutrals500 text-center">
                        Tidak Punya Akun? <a class="gk-text-primary700" href="{{ route('etiket.in.register') }}">Daftar
                            Sekarang</a>
                    </div>
                </div>
            </form>
        @endif

    </div>
@endsection

@section('js')
@endsection
