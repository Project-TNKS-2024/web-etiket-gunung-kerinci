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

                <header class="text-2xl font-bold gk-text-base-black">Edit Gate</header>
                <form id="gate-form" class="row gap-2" action="{{ route('admin.gate.editAction', ['id' => $data->id]) }}" method="post">
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
                            @error('foto') foto tidak valid @enderror
                            @error('detail') detail tidak valid @enderror
                        </div>
                    </div>
                    @endif


                    <div class="row">
                        <div class="col-md-6 col-sm-12">
                            <label class="form-label">Nama Gate</label>
                            <input class="form-control borderx bg-white" name="nama" id="lokasi-nama" value="{{$data->nama}}" placeholder="Nama Gate" required />
                        </div>
                        <div class="col-md-6 col-sm-12">
                            <label class="form-label">Destinasi Gate</label>
                            <button class="btn btn-outline gk-text-neutrals700 w-100 text-start d-flex justify-content-between  align-items-center dropdown-toggle" type="button" id="destinasi" data-bs-toggle="dropdown" aria-expanded="false" style="border-color: var(--neutrals700)">
                                <p class="overflow-x-hidden p-0 m-0">{{$data->destinasi->nama}}</p>
                            </button>
                            <ul class="dropdown-menu" aria-labelledby="destinasi">
                                @foreach ($destinasi as $d )
                                <li><a onclick="select(event, 'destinasi','destinasi-value', '{{$d->id}}')" class="dropdown-item" href="#">{{$d->nama}}</a></li>
                                @endforeach
                            </ul>
                            <input type="hidden" name="id_destinasi" id="destinasi-value" value="{{$data->id_destinasi}}" required />
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4 col-sm-12">
                            <label class="form-label" for="status">Status</label>
                            <select class="form-control borderx bg-white" id="status" name="status" required>
                                <option value="1" {{ $data->status == 1 ? 'selected' : '' }}>Open</option>
                                <option value="0" {{ $data->status == 0 ? 'selected' : '' }}>Close</option>
                            </select>
                        </div>
                        <div class="col-md-4 col-sm-6">
                            <label class="form-label" for="max_pendaki_hari">Max Pendaki / Hari</label>
                            <input class="form-control borderx bg-white" name="max_pendaki_hari" id="max_pendaki_hari" type="number" value="{{$data->max_pendaki_hari}}" placeholder="Max Pendaki per Hari" required />
                        </div>
                        <div class="col-md-4 col-sm-6">
                            <label class="form-label" for="min_pendaki_booking">Min Pendaki / Booking</label>
                            <input class="form-control borderx bg-white" name="min_pendaki_booking" id="min_pendaki_booking" type="number" value="{{$data->min_pendaki_booking}}" placeholder="Max Pendaki per Hari" required />
                        </div>
                    </div>


                    <div class="row">
                        <div class="col-md-6 col-sm-12">
                            <div>
                                <label class="form-label" for="lokasi">Lokasi</label>
                                <input class="form-control borderx bg-white" name="lokasi" id="lokasi" type="text" value="{{$data->lokasi}}" placeholder="Alamat" required />
                            </div>
                        </div>
                        <div class="col-md-6 col-sm-12">
                            <label class="form-label">Detail</label>
                            <textarea name="detail" id="lokasi-detail" class="form-control bg-white borderx" style="min-height: 75px;" placeholder="Detail">{{$data->detail}}</textarea>
                        </div>
                    </div>
                    <div class="row mt-3">
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