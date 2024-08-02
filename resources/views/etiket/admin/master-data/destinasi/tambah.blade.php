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
                    @error('detail') Harap menginputkan detail destinasi  @enderror
                </div>
            </div>
            @endif
           <div class="row gap-2">
                <div class="col-md-6 col-sm-12">
                    <label class="form-label">Nama Destinasi</label>
                    <input class="form-control borderx bg-white" name="nama" id="nama" value="" placeholder="Nama Destinasi" required/>
                </div>
           </div>
            <div class="row gap-2">
                <div class="col-md-6 col-sm-12">
                    <label class="form-label">Detail</label>
                    <textarea name="detail" id="destinasi-detail" class="form-control bg-white borderx" style="min-height: 75px;" placeholder="Detail" required></textarea>
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

        </form>
    </main>
</div>
@endsection
