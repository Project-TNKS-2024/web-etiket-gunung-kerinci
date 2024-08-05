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
        <form class="row gap-2" action="{{ route('admin.destinasi.editAction', ['id' => $data->id]) }}" method="post">
            @csrf
            @if (session('success'))
                <div class="row">
                    <div class="col btn btn-success">
                        {{ session('success') }}
                    </div>
                </div>
            @elseif (session('error'))
            @endif
            @if ($errors->any())
                <div class="row gap-1">
                    @foreach ($errors->all() as $error)
                        <div class="row btn btn-danger">
                            {{ $error }}
                        </div>
                    @endforeach
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
                    <button type="submit" class="btn font-bold text-black btn-outline w-fit text-start shadow d-flex align-items-center gap-1 px-3" style="border-color: var(--neutrals700)">
                        <img width="20" src="{{asset('assets/icon/tnks/save-light.svg')}}" class="gk-bg-primary700" />
                        Simpan
                    </button>
                </div>
            </div>
        </form>
        <form class="form row" action="{{route('admin.destinasi.upload', ['id' => $data->id])}}" method="post" enctype="multipart/form-data">
            @csrf
            <div class="col-md-5">
                <div class="col">
                    <label class="form-label">Judul Foto</label>
                    <input class="form-control borderx bg-white" name="foto_nama" id="foto-nama" placeholder="Judul Foto" value="" required/>
                </div>
                <div class="col mt-2">
                    <label class="form-label">Detail Foto</label>
                    <textarea cols="1" name="foto_detail" id="foto-detail" class="form-control bg-white borderx" style="" placeholder="Detail Foto" required></textarea>
                </div>
            </div>
            <div class="col-md-6">
                <div class="col">
                    <label class="form-label" for="gate-foto">Upload Foto</label>
                    <div class="d-flex gap-2">
                        <label class="borderx form-control d-flex align-items-center w-fit p-0 py-2 px-2 bg-white cursor-pointer text-nowrap" for="gate-foto" style="user-select: none;max-width: 300px;border: 1px solid var(--neutrals500); overflow-x: hidden;">
                            <div class="m-0 p-0 pe-3 py-0 borderx gk-text-primary700 font-medium d-flex gap-1">
                                <img class="p-0 m-0" width="20" src="{{asset('assets/icon/tnks/upload.svg')}}"/> <div>Pilih Foto</div>
                            </div>
                            <div id="input-file-label" class="m-0 p-0 d-flex" style=""> No File Chosen</div>
                        </label>
                        <div class="btn btn-warning text-black h-fit d-flex align-items-center gap-1"> <img src="{{asset('assets/icon/tnks/search_alt-dark.svg')}}" width="20" /></div>
                    </div>
                    <label class="text-sm text-black px-1">2-3 Foto</label>
                    <input class="form-control borderx bg-white d-none" type="file" name="foto" id="gate-foto" accept="image/*" onchange="collectPics(event)"/>
                </div>
                <div class="col mt-3">
                    <button type="submit" class="btn btn-primary gk-bg-primary700 d-flex align-items-center gap-1"><img width="20" src="{{asset('assets/icon/tnks/save-light.svg')}}" />Upload</button>
                </div>
            </div>
        </form>
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
                                <td class="d-flex gap-1 bg-transparent align-items-center justify-content-center" >
                                    <a  href="{{route('admin.gate.edit', ['id' => $g->id])}}" class="bg-transparent rounded gk-bg-primary100 cursor-pointer shadow" style="background-color: transparent;"><img width="25" src="{{asset('assets/icon/tnks-pen.svg')}}" class="bg-transparent"/></a>
                                    <a onclick="confirmDelete(event, '{{json_encode($g)}}',  `{{ route('admin.gate.hapus', ['id' => $g->id])}}`)" href="#" class="rounded cursor-pointer shadow-sm"><img width="25" src="{{asset('assets/icon/tnks-bin.svg')}}"/></a>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="row gap-1  mt-3">
            <div class="row">
                <div class="col text-base font-medium text-black">Daftar Gambar</div>
            </div>
            <div class="row">
                <div class="col">
                    <table class="rounded table table-striped table-bordered">
                        <thead>
                            <tr class="bg-white">
                                <th class="col-1">No</th>
                                <th class="col-4">Nama</th>
                                <th class="col-4">Detail</th>
                                <th class="col-2 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="data-body">
                            @if(count($gambar) == 0)
                            <tr>
                                <td  colspan="4">Belum Ada Gambar</td>
                            </tr>
                            @else
                                <tr>
                                    @foreach ($gambar as $g)
                                    <tr class="tiket-row">
                                        <td class="col-1">{{$loop->index+1}}</td>
                                        <td class="col-4">{{$g->nama}}</td>
                                        <td class="col-4">{{$g->detail}}</td>
                                        <td class="d-flex gap-1 bg-transparent align-items-center justify-content-center" >
                                            <div onclick="openModal([{{$g}}])" class="btn btn-warning text-black h-fit d-flex align-items-center gap-1"> <img src="{{asset('assets/icon/tnks/search_alt-dark.svg')}}" width="20"  /></div>
                                            <div class="btn btn-danger gk-bg-error200 text-black h-fit d-flex align-items-center gap-1"> <img src="{{asset('assets/icon/tnks/trash-dark.svg')}}" width="20" /></div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
    </main>
</div>

<script>
    const selectedFiles = [];
    function collectPics(event) {
        const fileInput = event.target;
        if (fileInput.files.length > 0) {
            const file = fileInput.files[0];
            const inputFileLabel = document.getElementById("input-file-label");
            inputFileLabel.textContent = event.target.value.split("\\").slice(-1);
        }


    }
</script>
@endsection
