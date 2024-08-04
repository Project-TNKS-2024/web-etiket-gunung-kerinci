@extends('etiket.admin.template.index')

@section('css')

<style>
</style>

@endsection

@section('main')

<div style="min-height: 100vh;">
    <main class="p-10 d-flex flex-column gap-3">
        <header class="text-2xl font-bold gk-text-base-black">Kelola Destinasi</header>
        <div class="row">
           <div class="overflow-visible">
                <a class="text-start text-black font-bold d-flex align-items-center gap-2 w-fit border-neutrals500 border-4 btn shadow gk-bg-base-white " href="{{route('admin.destinasi.tambah')}}" style="border: 1px solid var)">
                    <img src="{{asset('assets/icon/tnks-plus.svg')}}" />
                    Tambah Destinasi
                </a>
           </div>
        </div>

        <div class="row"  style="overflow: visible;">
            <div class="col-12 p-0 shadow rounded" style="overflow:auto;">
                @include('etiket.admin.master-data.destinasi.daftar', [
                    "headers" => ["Status","Destinasi", "Lokasi","Detail","Aksi"],
                    "data" => $destinasi,
                ])
            </div>
        </div>
    </main>

</div>
@endsection
