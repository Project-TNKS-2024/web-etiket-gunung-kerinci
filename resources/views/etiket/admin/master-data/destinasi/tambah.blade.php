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
    <main class="d-flex flex-column gap-3">
        <a class="btn btn-primary w-fit text-start" href="{{ route('admin.destinasi.daftar') }}">
            < Kembali </a>
                <header class="text-2xl font-bold gk-text-base-black">Tambah Destinasi</header>
                <form class="row gap-2" action="{{ route('admin.destinasi.tambahAction') }}" method="post">
                    @csrf
                    @if (session('success'))
                    <div class="row">
                        <div class="col btn btn-success">
                            {{ session('success') }}
                        </div>
                    </div>
                    @elseif (session('error'))
                    <div class="row px-2">
                        <div class="col btn btn-warning gk-bg-error200">
                            {{ session('error') }}
                        </div>
                    </div>
                    @endif

                    @if ($errors->any())
                    <div class="row">
                        <div class="col btn btn-danger">
                            @error('nama') Harap menginputkan nama destinasi @enderror
                            @error('detail') Harap menginputkan detail destinasi @enderror
                        </div>
                    </div>
                    @endif
                    <div class="row">
                        <div class="col-md-6 col-sm-12">
                            <label class="form-label" for="nama">Nama Destinasi</label>
                            <input class="form-control borderx bg-white" name="nama" id="nama" value="" placeholder="Nama Destinasi" required />
                        </div>
                        <div class="col-md-6 col-sm-12">
                            <label class="form-label" for="status">Status</label>
                            <select class="form-control borderx bg-white" id="status" name="status" required>
                                <option value="1" selected>Open</option>
                                <option value="0">Close</option>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 col-sm-12">
                            <label class="form-label" for="kategori">Kategori</label>
                            <select class="form-control borderx bg-white" id=" kategori" name="kategori" required>
                                <option value="taman" selected>Taman</option>
                                <option value="gunung">Gunung</option>
                            </select>

                        </div>
                        <div class="col-md-6 col-sm-12">
                            <label class="form-label" for="lokasi">Lokasi</label>
                            <input class="form-control borderx bg-white" name="lokasi" id="lokasi" value="" placeholder="Lokasi Destinasi" required />
                        </div>

                    </div>
                    <div class="row ">
                        <div class="col-md-6 col-sm-12">
                            <label class="form-label">Detail</label>
                            <textarea name="detail" id="destinasi-detail" class="form-control bg-white borderx" style="min-height: 75px;" placeholder="Detail" required></textarea>
                        </div>
                        <div class="col-md-4 col-sm-6">
                            <label class="form-label" for="lokasi-foto">Upload Foto</label>
                            <label class="form-label fst-italic gk-text-neutrals500">Upload foto bisa dilakukan pada bagian Edit</label>

                        </div>
                    </div>
                    <div class="row ">
                        <div class="col-md-12">
                            <button type="submit" class="btn font-bold btn-primary gk-bg-primary700 w-fit text-start shadow d-flex align-items-center gap-1 px-3 ms-auto" style="border-color: var(--neutrals700)">
                                <img width="20" src="{{asset('assets/icon/tnks/save-light.svg')}}" class="gk-bg-primary700" />
                                Simpan
                            </button>

                        </div>
                    </div>

                </form>
    </main>
</div>
@endsection