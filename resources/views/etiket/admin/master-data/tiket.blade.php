@extends('etiket.admin.template.index')

@section('css')

<style>
    .tiket-row:nth-child(odd) {
        background-color: rgb(230, 230, 230);
    }
</style>

@endsection

@section('main')

<div style="min-height: 100vh;">
<<<<<<< HEAD

    <h3 class="font-bold mb-3 gk-text-base-black">Kelola Tiket</h3>


    <div class="overflow-visible mb-3">
        <a class="text-start text-black font-bold d-flex align-items-center gap-2 w-fit border-neutrals500 border-4 btn shadow gk-bg-base-white " href="{{route('admin.tiket.tambah')}}" style="border: 1px solid var)">
            <img src="{{asset('assets/icon/tnks-plus.svg')}}" />
            Tambah Tiket
        </a>
    </div>

    <div style="overflow: visible;">
        <div class="col-12 p-0 shadow rounded" style="overflow:auto;">
            @include('etiket.admin.master-data.tiket.daftar', [
            "headers" => ["Nama", "Kategori", "Golongan", "Tipe", "Destinasi", "Gate", "Keterangan", "Harga Karcis", "Aksi"],
            "data" => $tiket,
            ])
        </div>

    </div>
=======
    <main class="p-10 d-flex flex-column gap-3">
        <header class="text-2xl font-bold gk-text-base-black">Kelola Tiket</header>
        <div class="row">
           <div class="overflow-visible d-flex gap-2">
                <a class="text-start text-black font-bold d-flex align-items-center gap-2 w-fit border-neutrals500 border-4 btn shadow gk-bg-base-white " href="{{route('admin.tiket.tambah')}}" style="border: 1px solid var)">
                    <img src="{{asset('assets/icon/tnks-plus.svg')}}" />
                    Tambah Tiket
                </a>

                <a class="text-start text-black font-bold d-flex align-items-center gap-2 w-fit border-neutrals500 border-4 btn shadow gk-bg-base-white " href="{{route('admin.tiket.tambah')}}" style="border: 1px solid var)">
                    <img src="{{asset('assets/icon/tnks/package_favourite_alt-light.svg')}}" class="gk-bg-primary700 rounded" />
                    Tambah Paket Tiket
                </a>
           </div>

        </div>

        <div class="row"  style="overflow: visible;">
            <div class="col-12 p-0 shadow rounded" style="overflow:auto;">
                @include('etiket.admin.master-data.tiket.daftar', [
                    "headers" => ["Nama", "Kategori", "Golongan", "Destinasi", "Keterangan", "Harga Karcis", "Aksi"],
                    "data" => $tiket,
                ])
            </div>
        </div>
    </main>

>>>>>>> ad4414b27858d2c6d0dfd51170a42d9948cda922
</div>
@endsection