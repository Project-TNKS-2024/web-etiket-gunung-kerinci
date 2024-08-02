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
            <i class="ti ti-arrow-left"></i>
            Kembali
        </a>
        <header class="text-2xl font-bold gk-text-base-black">Ubah Destinasi</header>
        <form class="row gap-2" action="{{ route('admin.tiket.editAction', ['id' => $data->id]) }}" method="post">
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
                <div class="col-md-6 col-sm-12">
                    <label class="form-label">Nama Destinasi</label>
                    <input class="form-control borderx bg-white" name="nama" id="destinasi-nama" value="{{$data->nama}}" required/>
                </div>
           </div>
            <div class="row gap-2">
                <div class="col-md-6 col-sm-12">
                    <label class="form-label">Detail</label>
                    <textarea name="detail" id="destinasi-detail" class="form-control bg-white borderx" style="min-height: 75px;" placeholder="Detail">{{ $data->detail }}</textarea>
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
                <div class="col mt-3 text-base font-medium text-black">Daftar Gates</div>
            </div>
            <div class="row">
                <div class="col">
                    <table class="rounded table table-striped table-bordered">
                        <thead>
                            <tr class="bg-white">
                                <th class="col-1" style="">No</th>
                                <th class="col-4">Nama</th>
                                <th class="col-4">Detail</th>
                                <th class="col-2">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($gates as $g)
                                <tr class="tiket-row">
                                    <td class="col-1" style="">{{$loop->index+1}}</td>
                                    <td class="col-4">{{$g->nama}}</td>
                                    <td class="col-4">{{$g->detail}}</td>
                                    <td class="p-3 d-flex gap-1 bg-transparent align-items-center justify-content-center" >
                                        <a  href="{{route('admin.gate.edit', ['id' => $g->id])}}" class="bg-transparent rounded gk-bg-primary100 cursor-pointer shadow" style="background-color: transparent;"><img width="25" src="{{asset('assets/icon/tnks-pen.svg')}}" class="bg-transparent"/></a>
                                        <a onclick="confirmDelete(event, '{{json_encode($g)}}',  `{{ route('admin.gate.hapus', ['id' => $g->id])}}`)" href="#" class="rounded cursor-pointer shadow-sm"><img width="25" src="{{asset('assets/icon/tnks-bin.svg')}}"/></a>
                                        <a><img class="gk-bg-success100 rounded shadow-sm" width="25" src="{{asset('assets/icon/tnks-detail.svg')}}"/></a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
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
