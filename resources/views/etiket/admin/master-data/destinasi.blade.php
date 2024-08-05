@extends('etiket.admin.template.index')

@section('css')

<style>
</style>

@endsection

@section('main')

<div class="container-fluid" style="min-height: 80vh;">
    <!-- title -->
    <h3 class="font-bold mb-3 gk-text-base-black">Kelola Destinasi</h3>

    <!-- tombol tambah -->
    <div class="overflow-visible mb-3">
        <a class="text-start text-black font-bold d-flex align-items-center gap-2 w-fit border-neutrals500 border-4 btn shadow gk-bg-base-white " href="{{route('admin.destinasi.tambah')}}" style="border: 1px solid var)">
            <img src="{{asset('assets/icon/tnks-plus.svg')}}" />
            Tambah Destinasi
        </a>
    </div>

    <div style="overflow: visible;">
        <div class="col-12 p-0 shadow rounded" style="overflow:auto;">
            @include('etiket.admin.master-data.destinasi.daftar', [
            "headers" => ["Status","Destinasi","kategori","Lokasi","Detail","Aksi"],
            "data" => $destinasi,
            ])
        </div>
    </div>
</div>
@endsection
