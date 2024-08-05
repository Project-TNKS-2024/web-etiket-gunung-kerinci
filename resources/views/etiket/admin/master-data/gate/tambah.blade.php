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
        <a class="btn btn-primary w-fit text-start" href="{{ route('admin.gate.daftar') }}">
            < Kembali </a>
                <header class="text-2xl font-bold gk-text-base-black">Tambah Gate</header>
                <form id="gate-form" action="{{ route('admin.gate.tambahAction') }}" method="post" enctype="multipart/form-data">
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
                            @error('nama') nama tidak valid @enderror
                            @error('lokasi') lokasi tidak valid @enderror
                            @error('detail') detail tidak valid @enderror
                        </div>
                    </div>
                    @endif

                    <input type="hidden" name="destinasi" id="destinasi-value" />

                    <div class="row">
                        <div class="col-md-6 col-sm-12">
                            <label class="form-label">Nama Gate</label>
                            <input class="form-control borderx bg-white" name="nama" id="lokasi-nama" value="" placeholder="Nama Gate" required />
                        </div>
                        <div class="col-md-6 col-sm-12">
                            <label class="form-label">Destinasi Gate</label>
                            <button class="btn btn-outline gk-text-neutrals700 w-100 text-start d-flex justify-content-between  align-items-center dropdown-toggle" type="button" id="destinasi" data-bs-toggle="dropdown" aria-expanded="false" style="border-color: var(--neutrals700)">
                                <p class="overflow-x-hidden p-0 m-0">Pilih Lokasi Destinasi</p>
                            </button>
                            <ul class="dropdown-menu" aria-labelledby="destinasi">
                                @foreach ($destinasi as $d )
                                <li><a onclick="select(event, 'destinasi','destinasi-value', '{{$d->id}}')" class="dropdown-item" href="#">{{$d->nama}}</a></li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4 col-sm-12">
                            <label class="form-label" for="status">Status</label>
                            <select class="form-control borderx bg-white" id="status" name="status" required>
                                <option value="1" selected>Open</option>
                                <option value="0">Close</option>
                            </select>
                        </div>
                        <div class="col-md-4 col-sm-6">
                            <label class="form-label" for="status">Status</label>
                            <input class="form-control borderx bg-white" name="nama" id="lokasi" value="" placeholder="Max Pendaki per Hari" required />

                        </div>
                    </div>


                    <div class="row">
                        <div class="col-md-6 col-sm-12">
                            <label class="form-label">Detail</label>
                            <textarea name="detail" id="lokasi-detail" class="form-control bg-white borderx" style="min-height: 75px;" placeholder="Detail"></textarea>
                        </div>
                    </div>


                    <div class="row">
                        <div class="col-md-6 col-sm-12">
                            <button type="submit" class="btn font-bold text-black btn-outline w-fit text-start shadow" style="border-color: var(--neutrals700)">
                                <i class="ti ti-device-floppy gk-text-primary600"></i>
                                Simpan
                            </button>
                        </div>
                    </div>
                </form>
    </main>
</div>

<script>
</script>
@endsection