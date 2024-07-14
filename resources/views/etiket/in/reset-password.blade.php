@extends('etiket.in.template.index')
@section('css')
@endsection

@section('main')
<div class="card-body">
    <h5 class="mt-2 text-center fw-bold">Buat Kata Sandi Baru</h5>
    <p class="text-center text-secondary">Kata sandi baru Anda harus unik dari yang digunakan sebelumnya.</p>
    <form method="POST" action="{{ route('etiket.in.actionresetpassword') }}">
        @csrf
        <div class="form-group mb-3">
            <label for="password" class="form-label ">Password</label>
            <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" required autocomplete="current-password">
            @error('password')
            <span class="invalid-feedback" role="alert">
                {{ $message }}
            </span>
            @enderror
        </div>
        <div class="form-group mb-3">
            <label for="password_confirmation" class="form-label">Ulangi Password</label>
            <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
        </div>

        <div class="form-group mb-3">
            <button type="submit" class="btn btn-primary w-100">Atur Ulang Kata Sandi</button>
        </div>
    </form>
</div>
@endsection

@section('js')
@endsection