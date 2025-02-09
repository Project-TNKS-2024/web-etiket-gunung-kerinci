@extends('etiket.auth.template.index')
@section('css')
@endsection

@section('main')
<div class="card-body text-center">

   <h4>Verifikasi Email Anda</h4>
   <p>Silakan cek email Anda dan klik link verifikasi untuk melanjutkan.</p>
   <form method="POST" action="{{ route('verification.resend') }}">
      @csrf
      <button type="submit" class="btn btn-primary">Kirim Ulang Email Verifikasi</button>
   </form>
</div>
@endsection

@section('js')
@endsection