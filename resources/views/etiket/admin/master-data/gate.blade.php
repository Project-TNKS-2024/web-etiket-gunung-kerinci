@extends('etiket.admin.template.index')

@section('css')

<style>
    .tiket-row:nth-child(odd) {
        background-color: rgb(230, 230, 230);
    }
</style>

@endsection

@section('main')

<div style="min-height: 80vh;">
    <h3 class="font-bold mb-3 gk-text-base-black">Kelola Gate</h3>

    <div class="overflow-visible mb-3">
        <a class="text-start text-black font-bold d-flex align-items-center gap-2 w-fit border-neutrals500 border-4 btn shadow gk-bg-base-white " href="{{route('admin.gate.tambah')}}" style="border: 1px solid var)">
            <img src="{{asset('assets/icon/tnks-plus.svg')}}" />
            Tambah Gate
        </a>
    </div>

    <div style="overflow: visible;">
        <div class="col-12 p-0 shadow rounded" style="overflow:auto;">
            @include('etiket.admin.master-data.gate.daftar', [
            "headers" => ["Status", "Nama", "Destinasi", "Lokasi", "Detail", "Aksi"],
            "data" => $gates,
            ])
        </div>
    </div>

</div>
@endsection