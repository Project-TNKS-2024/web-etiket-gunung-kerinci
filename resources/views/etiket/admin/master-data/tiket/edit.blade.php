@extends('etiket.admin.template.index')

@section('css')

@endsection

@section('main')

<div style="min-height: 100vh;">
    <main class="p-10 d-flex flex-column gap-3">
        <a class="btn btn-primary w-fit text-start" href="{{route('admin.tiket.daftar')}}">
            <i class="ti ti-arrow-left"></i>
            Kembali
        </a>
        <header class="text-2xl font-bold gk-text-base-black">Tambah Tiket</header>
        <form class="row gap-2" action="{{route('admin.tiket.editAction', ['id' => $data->id])}}" method="post">
            @csrf
            @if (session('success'))
            <div class="row">
                <div class="col btn btn-success">
                    {{session('success')}}
                </div>
            </div>
            @endif
            @if ($errors->any())
            <div class="row">
                <div class="col btn btn-danger">
                    @error('destinasi') Destinasi tidak valid  @enderror
                    @error('tipe') tipe tidak valid  @enderror
                    @error('jenis') jenis tidak valid  @enderror
                    @error('gate') gate tidak valid  @enderror
                    @error('hargaTiket') Harga Tiket tidak valid  @enderror
                </div>
            </div>
            @endif
            <hr class="">
            <div class="row">
                <div class="col-md-3 col-sm-6">
                    <label class="form-label p-0 m-0">Paket Tiket</label>
                    <div class="dropdown w-100">
                        <button class="btn btn-outline gk-text-neutrals700 w-100 text-start d-flex justify-content-between  align-items-center dropdown-toggle" type="button" id="destinasi" data-bs-toggle="dropdown" aria-expanded="false" style="border-color: var(--neutrals700)">
                            {{$data->nama}}
                        </button>
                        <ul class="dropdown-menu" aria-labelledby="destinasi">
                            @foreach ($paket_tiket as $item )
                                <li><a class="{{$data->id == $item->id ? 'bg-primary text-white' : ''}} dropdown-item" onclick="select(event, 'destinasi','destinasi-value', '{   {$item->id}}')" href="#">{{$item->nama}}</a></li>
                            @endforeach
                        </ul>
                    </div>
                </div>
                <input type="hidden" id="paket-value" name="paket_tiket" value="{{$data->id}}"/>
                <div class="col-md-3 col-sm-6">
                    <label class="form-label p-0 m-0">Destinasi</label>
                    <div class="btn btn-outline gk-text-neutrals700 w-100 text-start d-flex justify-content-between align-items-center" style="border-color: var(--neutrals700)">
                        {{$data->destinasi->nama}}
                    </div>
                </div>
                <input name="destinasi" id="destinasi-value" value="{{$data->destinasi->id}}" type="hidden"/>
                <div class="col-md-3 col-sm-6">
                    <label class="form-label p-0 m-0">Nama Tiket</label>
                    <input disabled id="nama-tiket" name="nama" value="{{$data->nama}}" class="form-control " style="border: 1px solid var(--neutrals500)"/>
                </div>
            </div>
            <div class="row gap-0">
                <div class="col-md-3 col-sm-6">
                    <label class="form-label p-0 m-0">Keterangan</label>
                    <textarea disabled id="keterangan-tiket" name="keterangan" placeholder="Keterangan" class="form-control" style="border: 1px solid var(--neutrals500);" rows="1">{{$data->keterangan}}</textarea>
                </div>

                <div class="col-md-3 col-sm-6">
                    <label class="form-label p-0 m-0">Penugasan</label>
                    <input disabled id="penugasan-tiket" name="penugasan" value="{{$data->penugasan}}" placeholder="Penugasan" class="form-control" style="border: 1px solid var(--neutrals500)"/>
                </div>
                <div class="col-md-3 col-sm-6">
                    <label class="form-label p-0 m-0">Kuota Pendaki</label>
                    <input disabled id="penugasan-tiket" min="1" type="number" name="penugasan" value="{{$data->min_pendaki}}" placeholder="Penugasan" class="form-control " style="border: 1px solid var(--neutrals500)"/>

                </div>
            </div>
            <div class="row gap-2 mt-3">
                <div class="col-md-3 col-sm-6">
                    <button type="submit" class="btn font-bold text-black btn-outline w-fit text-start shadow" style="border-color: var(--neutrals700)">
                        <i class="ti ti-device-floppy gk-text-primary600"></i>
                        Simpan
                    </button>
                </div>
            </div>
        </form>
    </main>
    <script>
        function select(event, callerId, inputId, value) {
            const caller = document.getElementById(callerId);
            const input = document.getElementById(inputId);

            caller.textContent = event.target.textContent;
            input.value = value;
        }
    </script>
</div>
@endsection

