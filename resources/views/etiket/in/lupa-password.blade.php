@extends('etiket.in.template.index')
@section('css')
@endsection

@section('main')
<div class="card-body">
    <h5 class="mt-2 text-center fw-bold">Lupa Password</h5>
    @if (session('success'))
    <p class="fw-bold text-center my-3">Silakan cek email untuk mereset kata sandi</p>
    <div class="form-group my-3 text-center">
        <p>Ingat kata sandi? <a href="{{ route('register') }}">Login Sekarang</a>
        </p>
    </div>
    @else
    <form method="POST" action="{{ route('etiket.in.actionlupapassword') }}">
        @csrf
        <div class="form-group mb-3">
            <label for="email" class="form-label ">Email</label>
            <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>
            @error('email')
            <span class="invalid-feedback" role="alert">
                {{ $message }}
            </span>
            @enderror
        </div>

        <div class="form-group mb-3">
            <button type="submit" class="btn btn-primary w-100">Reset Password</button>
        </div>

        <div class="form-group mb-3 text-center">
            <p>Tidak Punya Akun? <a href="{{ route('register') }}">Daftar Sekarang</a>
            </p>
        </div>
    </form>
    @endif
</div>
@endsection

@section('js')
@endsection