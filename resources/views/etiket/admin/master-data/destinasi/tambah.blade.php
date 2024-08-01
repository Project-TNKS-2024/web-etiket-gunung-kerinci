@extends('etiket.admin.template.index')

@section('css')
<style>
    .borderx {
        border-color: var(--neutrals500);
    }
</style>
@endsection

@section('main')
<div style="min-height: 100vh;">
    <main class="p-10 d-flex flex-column gap-3">
        <a class="btn btn-primary w-fit text-start" href="{{ route('admin.destinasi.daftar') }}">
            < Kembali
        </a>
        <header class="text-2xl font-bold gk-text-base-black">Tambah Destinasi</header>
        <form class="row gap-2" action="{{ route('admin.destinasi.tambahAction') }}" method="post">
            @csrf
            @if (session('success'))
            <div class="row">
                <div class="col btn btn-success">
                    {{ session('success') }}
                </div>
            </div>
            @endif
            @if ($errors->any())
            <div class="row">
                <div class="col btn btn-danger">
                    @error('destinasi') Destinasi tidak valid @enderror
                    @error('tipe') tipe tidak valid @enderror
                    @error('jenis') jenis tidak valid @enderror
                    @error('gate') gate tidak valid @enderror
                    @error('hargaTiket') Harga Tiket tidak valid @enderror
                </div>
            </div>
            @endif
           <div class="row gap-2">
                <div class="col-md-3 col-sm-6">
                    <label class="form-label">Nama Destinasi</label>
                    <input class="form-control borderx bg-white" name="nama" id="destinasi-nama" value="" placeholder="Nama Destinasi" required/>
                </div>
                <div class="col-md-3 col-sm-6">
                    <label class="form-label">Lokasi Destinasi</label>
                    <input class="form-control borderx bg-white" name="lokasi" id="destinasi-lokasi" value="" placeholder="Lokasi Destinasi" required/>
                </div>

                <div class="col w-fit">
                    <label class="form-label" for="destinasi-foto">Upload Foto</label>
                    <label class="borderx form-control d-flex align-items-center w-fit p-0 py-2 px-2 bg-white cursor-pointer" for="destinasi-foto" style="user-select: none;max-width: 240px;border: 1px solid var(--neutrals500)">
                        <div  class="m-0 p-0 pe-3 py-0 borderx gk-text-primary700 font-medium">
                            <img class="p-0 m-0" width="20" src="{{asset('assets/icon/tnks/upload.svg')}}"/> Pilih Foto
                        </div>
                        <div class="m-0 p-0"> No File Choosen</div>
                    </label>
                    <label class="text-sm text-black px-1">2-3 Foto</label>
                    <input class="form-control borderx bg-white d-none" styl type="file" name="foto" id="destinasi-foto" value=""  required/>
                </div>
           </div>
            <div class="row gap-2">
                <div class="col-md-3 col-sm-6">
                    <label class="form-label">Detail</label>
                    <textarea name="detail" id="destinasi-detail" class="form-control bg-white borderx" style="min-height: 75px;" placeholder="Detail"></textarea>
                </div>
            </div>
            <div class="row gap-2">
                <div class="col-md-3 col-sm-6">
                    <button type="submit" class="btn font-bold text-black btn-outline w-fit text-start shadow" style="border-color: var(--neutrals700)">
                        <i class="ti ti-device-floppy gk-text-primary600"></i>
                        Simpan
                    </button>
                </div>
            </div>
            <div class="row">
                <div class="col mt-3 text-base font-medium text-black">Daftar Gambar</div>
            </div>
            <div class="row">
                <div class="col">
                    <table class="rounded table table-striped table-bordered">
                        <thead>
                            <tr class="bg-white">
                                <th class="col-2">No</th>
                                <th class="col-8">Nama</th>
                                <th class="col-2">Aksi</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </form>
    </main>
</div>
@endsection
