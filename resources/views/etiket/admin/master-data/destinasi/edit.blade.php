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
            <div class="row">
                <div class="col-md-3 col-sm-6">
                    <label class="form-label p-0 m-0">Destinasi</label>
                    <div class="dropdown w-100">
                        <button class="btn btn-outline gk-text-neutrals700 w-100 text-start d-flex justify-content-between  align-items-center dropdown-toggle" type="button" id="destinasi" data-bs-toggle="dropdown" aria-expanded="false" style="border-color: var(--neutrals700)">
                            {{$data->destinasi->nama}}
                        </button>
                        <ul class="dropdown-menu" aria-labelledby="destinasi">
                            @foreach ($destinasi as $item )
                                <li><a class="{{$data->destinasi->id == $item->id ? 'bg-primary text-white' : ''}} dropdown-item" onclick="select(event, 'destinasi','destinasi-value', '{{$item->id}}')" href="#">{{$item->nama}}</a></li>
                            @endforeach
                        </ul>
                    </div>
                </div>
                <input type="hidden" name="destinasi" id="destinasi-value" value="{{$data->id_destinasi}}"/>
                <div class="col-md-3 col-sm-6">
                    <label class="form-label p-0 m-0">Nama Tiket</label>
                    <div class="align-items-center d-flex py-2">
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="tipe" id="domestik" value="domestik" {{strtolower($data->nama) == "domestik" ? "checked" : ""}} required>
                            <label class="form-check-label" for="domestik">Domestik</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="tipe" id="mancanegara" value="mancanegara" {{strtolower($data->nama) == "mancanegara" ? "checked" : ""}} required>
                            <label class="form-check-label" for="mancanegara">Mancanegara</label>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <label class="form-label p-0 m-0">Jenis Tiket</label>
                    <div class="dropdown w-100">
                        <button class="btn btn-outline gk-text-neutrals700 w-100 text-start d-flex justify-content-between  align-items-center dropdown-toggle" type="button" id="jenisTiket" data-bs-toggle="dropdown" aria-expanded="false" style="border-color: var(--neutrals700)">
                            {{$jenisTiket[$data->jenisTiket]}}
                        </button>
                        <ul class="dropdown-menu" aria-labelledby="jenisTiket">
                            @foreach ($jenisTiket as $item )
                                <li><a class="{{$data->jenisTiket == $loop->index+1 ? 'bg-primary text-white' : ''}} dropdown-item" onclick="select(event, 'jenisTiket','jenis-value', '{{$loop->index}}')" href="#">{{$item}}</a></li>
                            @endforeach
                        </ul>
                    </div>
                </div>
                <input type="hidden" name="jenis" id="jenis-value" value="{{$data->jenisTiket}}"/>
            </div>
            <div class="row gap-2">
                <div class="col-md-3 col-sm-6">
                    <label class="form-label p-0 m-0">Gate Masuk</label>
                    <div class="dropdown w-100">
                        <button class="btn btn-outline gk-text-neutrals700 w-100 text-start d-flex justify-content-between  align-items-center dropdown-toggle" type="button" id="gateMasuk" data-bs-toggle="dropdown" aria-expanded="false" style="border-color: var(--neutrals700)">
                          {{$data->gk_gates->nama}}
                        </button>
                        <ul class="dropdown-menu" aria-labelledby="gateMasuk">

                            @foreach ($gate as $item )
                                <li><a onclick="select(event, 'gateMasuk','gate-value', '{{$item->id}}')"  class="dropdown-item" href="#">{{$item->nama}}</a></li>
                            @endforeach

                        </ul>
                      </div>
                </div>
                <input type="hidden" name="gate" id="gate-value" value="{{$data->gate}}"/>
                <div class="col-md-3 col-sm-6">
                    <label class="form-label p-0 m-0">Harga Tiket</label>
                    <div class="d-flex align-items-center gap-1 ">
                        <span class="p-2 flex gk-bg-neutrals500 h-100 text-white shadow rounded-md ">Rp</span>
                        <input class="form-control" type="number" name="hargaTiket" min="0" style="border-color: var(--neutrals500)" value="{{$data->harga}}" step="any" required/>
                    </div>

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

