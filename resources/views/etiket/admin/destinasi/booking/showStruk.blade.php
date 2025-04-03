@extends('etiket.admin.template.index')

@section('css')

<style>
   .tabel1 th {
      text-align: center;
      vertical-align: middle;
   }
</style>

@endsection

@section('main')
<a class="btn btn-secondary w-fit text-start mb-3" href="{{ route('admin.destinasi.booking.show', ['id' => $data->id]) }}">
   <i class="ti ti-arrow-left"></i>
   Kembali
</a>

@include('homepage.template.tiket.cardStruk', ['data' => $data])


@endsection

@section('js')

@endsection