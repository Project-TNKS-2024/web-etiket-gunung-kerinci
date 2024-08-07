@extends('etiket.admin.template.index')

@section('css')

<style>
    .tiket-row:nth-child(odd) {
        background-color: rgb(230, 230, 230);
    }
</style>

@endsection

@section('main')

<div>
    <h3 class="font-bold mb-3 gk-text-base-black">Kelola Tiket</h3>


    <div class="overflow-visible mb-3">
        <a class="text-start text-black font-bold d-flex align-items-center gap-2 w-fit border-neutrals500 border-4 btn shadow gk-bg-base-white " href="{{route('admin.tiket.tambah')}}" style="border: 1px solid var">
            <img src="{{asset('assets/icon/tnks-plus.svg')}}" />
            Tambah Tiket
        </a>
    </div>

    <div style="overflow: visible;">
        <div class="col-12 p-0 shadow rounded" style="overflow:auto;">
            @include('etiket.admin.master-data.tiket.daftar', [
            "headers" => ["Nama", "Kategori", "Golongan", "Destinasi", "Keterangan", "Harga Karcis", "Aksi"],
            "data" => $tiket,
            ])
        </div>

    </div>

</div>
@endsection