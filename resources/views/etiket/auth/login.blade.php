@extends('etiket.auth.template.index')
@section('css')
@endsection

@section('main')
<div class="card-body">
    <h5 class="mt-2 text-center fw-bold">Masuk ke akun</h5>
    <form method="POST" action="{{ route('login.action') }}">
        @csrf
        <div class="form-group mb-3">
            <label for="email" class="form-label ">Email</label>
            <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email"
                value="{{ old('email') }}" required autocomplete="email" autofocus>
            @error('email')
            <span class="invalid-feedback" role="alert">
                {{ $message }}
            </span>
            @enderror
        </div>

        <div class="form-group mb-3">
            <label for="password" class="form-label ">Password</label>
            <div class="input-group">
                <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" required autocomplete="current-password">
                <span class="input-group-text btn-visibility" data-target='password'><i class="fa-solid fa-eye"></i> </i></span>
            </div>
        </div>
        @error('password')
        <span class="invalid-feedback" role="alert">
            {{ $message }}
        </span>
        @enderror
</div>

<div class="form-group mb-3">
    <div class="d-flex justify-content-between">
        <div>
            <input type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
            <label for="remember">Ingat saya</label>
        </div>
        <div>
            <a href="{{ route('lupaPassword') }}">Lupa Password?</a>
        </div>
    </div>
</div>

<div class="form-group mb-3">
    <button type="submit" class="btn btn-primary w-100">Login</button>
</div>

<div class="form-group mb-3">
    <a href="{{ route('oauth.google') }}" class="btn w-100 d-flex align-items-center justify-content-center google-btn">
        <img src="{{ asset('assets/icon/google.png') }}" alt="Google Logo" class="me-2" width="20">
        Login Dengan Akun Google
    </a>
</div>


<div class="form-group mb-3 text-center">
    <p>Tidak Punya Akun? <a href="{{ route('register') }}">Daftar Sekarang</a>
    </p>
</div>
</form>
</div>
@endsection

@section('js')
@endsection