@extends('etiket.in.template.index')
@section('css')
@endsection

@section('main')
<div class="card-body">
    <h5 class="mt-2 text-center fw-bold">Lupa Password</h5>
    <p class="fw-bold text-center my-3">Silakan cek email [{{ $email }}] untuk mereset kata sandi</p>
    <div class="form-group my-3 text-center">
        <p>Ingat kata sandi? <a href="{{ route('register') }}">Login Sekarang</a>
        </p>
    </div>
</div>
@endsection