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
        <header class="text-2xl font-bold gk-text-base-black">Edit Tiket</header>
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
                    @error('destinasi') Destinasi tidak valid @enderror
                    @error('tipe') tipe tidak valid @enderror
                    @error('jenis') jenis tidak valid @enderror
                    @error('gate') gate tidak valid @enderror
                    @error('hargaTiket') Harga Tiket tidak valid @enderror
                </div>
            </div>
            @endif
            <hr class="">
            <div class="row">
                <div class="col-md-3 col-sm-6">
                    <label class="form-label p-0 m-0">Paket Tiket</label>
                    <div class="dropdown w-100">
                        <button
                            class="btn btn-outline gk-text-neutrals700 w-100 text-start d-flex justify-content-between  align-items-center dropdown-toggle"
                            type="button" data-bs-toggle="dropdown" aria-expanded="false"
                            style="border-color: var(--neutrals700);  overflow-x: hidden">
                            <span id="paket_tiket" style=" overflow-x: hidden">{{$data->paket_tiket->nama ?
                                $data->paket_tiket->nama : "" }}</span>
                        </button>
                        <ul class="dropdown-menu" aria-labelledby="destinasi">
                            @foreach ($paket_tiket as $item )
                            <li><a class="{{$data->id == $item->id ? 'bg-primary text-white' : ''}} dropdown-item"
                                    onclick="gantiPaket(event, 'paket_tiket','paket-value', '{{$item}}', '{{$item->destinasi}}')"
                                    href="#">{{$item->nama}}</a></li>
                            @endforeach
                        </ul>
                    </div>
                </div>
                <input type="hidden" id="paket-value" name="paket_tiket" value="{{$data->id}}" />
                <div class="col-md-3 col-sm-6">
                    <label class="form-label p-0 m-0">Destinasi</label>
                    <div class="btn btn-outline gk-text-neutrals700 w-100 text-start d-flex justify-content-between align-items-center"
                        style="border-color: var(--neutrals700)" id="destinasi-label">
                        {{$data->paket_tiket->destinasi->nama}}
                    </div>
                </div>
                <input name="destinasi" id="destinasi-value" value="{{$data->paket_tiket->destinasi->id}}"
                    type="hidden" />
                <div class="col-md-3 col-sm-6">
                    <label class="form-label p-0 m-0">Nama Tiket</label>
                    <input disabled id="nama-tiket" name="nama" value="{{$data->paket_tiket->nama}}"
                        class="form-control " style="border: 1px solid var(--neutrals500)" />
                </div>
            </div>
            <div class="row gap-0">
                <div class="col-md-3 col-sm-6">
                    <label class="form-label p-0 m-0">Keterangan</label>
                    <textarea disabled id="keterangan-tiket" name="keterangan" placeholder="Keterangan"
                        class="form-control" style="border: 1px solid var(--neutrals500);"
                        rows="1">{{$data->paket_tiket->keterangan}}</textarea>
                </div>

                <div class="col-md-3 col-sm-6">
                    <label class="form-label p-0 m-0">Penugasan</label>
                    <input disabled id="penugasan-tiket" name="penugasan" value="{{$data->paket_tiket->penugasan}}"
                        placeholder="Penugasan" class="form-control" style="border: 1px solid var(--neutrals500)" />
                </div>
                <div class="col-md-3 col-sm-6">
                    <label class="form-label p-0 m-0">Minimal Pendaki</label>
                    <input disabled id="minimal-tiket" min="1" type="number" name="penugasan"
                        value="{{$data->paket_tiket->min_pendaki}}" placeholder="Penugasan" class="form-control "
                        style="border: 1px solid var(--neutrals500)" />

                </div>
            </div>
            <hr class="mt-3">
            <div class="row">
                <div class="col-md-3 col-sm-6">
                    <label class="form-label p-0 m-0">Kategori Pendaki</label>
                    <div class="dropdown w-100">
                        <button
                            class="btn btn-outline gk-text-neutrals700 w-100 text-start d-flex justify-content-between  align-items-center dropdown-toggle"
                            type="button" id="kategori_pendaki" data-bs-toggle="dropdown" aria-expanded="false"
                            style="border-color: var(--neutrals700)">
                            {{$data->kategori_pendaki == "wna" ? "Mancanegara" : "Nusantara"}}
                        </button>
                        <ul class="dropdown-menu" aria-labelledby="destinasi">
                            <li>
                                <a class="{{$data->id == $item->id ? 'bg-primary text-white' : ''}} dropdown-item"
                                    onclick="select(event, 'kategori_pendaki','kategori-pendaki-value', 'wna')"
                                    href="#">Mancanegara</a>
                            </li>
                            <li>
                                <a class="{{$data->id == $item->id ? 'bg-primary text-white' : ''}} dropdown-item"
                                    onclick="select(event, 'kategori_pendaki','kategori-pendaki-value', 'wni')"
                                    href="#">Nusantara</a>
                            </li>
                        </ul>
                    </div>
                </div>
                <input type="hidden" id="kategori-pendaki-value" name="kategori_pendaki"
                    value="{{$data->kategori_pendaki}}" />
                <div class="col-md-3 col-sm-6">
                    <label class="form-label p-0 m-0">Kategori Hari</label>
                    <div class="dropdown w-100">
                        <button
                            class="btn btn-outline gk-text-neutrals700 w-100 text-start d-flex justify-content-between  align-items-center dropdown-toggle"
                            type="button" id="kategori_hari" data-bs-toggle="dropdown" aria-expanded="false"
                            style="border-color: var(--neutrals700)">
                            {{$data->kategori_hari == "wd" ? "Weekday" : "Weekend"}}
                        </button>
                        <ul class="dropdown-menu" aria-labelledby="destinasi">
                            <li>
                                <a class="{{$data->id == $item->id ? 'bg-primary text-white' : ''}} dropdown-item"
                                    onclick="select(event, 'kategori_hari','kategori-hari-value', 'wd')"
                                    href="#">Weekday</a>
                            </li>
                            <li>
                                <a class="{{$data->id == $item->id ? 'bg-primary text-white' : ''}} dropdown-item"
                                    onclick="select(event, 'kategori_hari','kategori-hari-value', 'wk')"
                                    href="#">Weekend</a>
                            </li>
                        </ul>
                    </div>
                </div>
                <input type="hidden" id="kategori-hari-value" name="kategori_hari" value="{{$data->kategori_hari}}" />
                <input name="destinasi" id="destinasi-value" value="{{$data->paket_tiket->destinasi->id}}"
                    type="hidden" />
                <div class="col-md-3 col-sm-6">
                    <label class="form-label p-0 m-0">Harga Masuk</label>
                    <div style="border: 1px solid var(--neutrals500);"
                        class="rounded px-2 d-flex align-items-center gap-3">
                        <div>Rp</div>
                        <input id="harga-tiket" name="nama" value="{{$data->harga_masuk}}" class="form-control text-end"
                            min="0" style="background-color: transparent;border: none" />
                    </div>
                </div>
            </div>
            <div class="row gap-0">
                <div class="col-md-3 col-sm-6">
                    <label class="form-label p-0 m-0">Harga Kemah</label>
                    <div style="border: 1px solid var(--neutrals500);"
                        class="rounded px-2 d-flex align-items-center gap-3">
                        <div>Rp</div>
                        <input id="harga-kemah" name="harga_kemah" value="{{$data->harga_kemah}}"
                            class="form-control text-end" min="0" style="background-color: transparent;border: none" />
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <label class="form-label p-0 m-0">Harga Traking</label>
                    <div style="border: 1px solid var(--neutrals500);"
                        class="rounded px-2 d-flex align-items-center gap-3">
                        <div>Rp</div>
                        <input id="harga-traking" name="harga_traking" value="{{$data->harga_traking}}"
                            class="form-control text-end" min="0" style="background-color: transparent;border: none" />
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <label class="form-label p-0 m-0">Harga Asuransi</label>
                    <div style="border: 1px solid var(--neutrals500);"
                        class="rounded px-2 d-flex align-items-center gap-3">
                        <div>Rp</div>
                        <input id="harga-asuransi" name="harga_asuransi" value="{{$data->harga_ansuransi}}"
                            class="form-control text-end" min="0" style="background-color: transparent;border: none" />
                    </div>
                </div>
            </div>
            <div class="row gap-2 mt-3">
                <div class="col-md-3 col-sm-6">
                    <button type="submit" class="btn font-bold text-black btn-outline w-fit text-start shadow"
                        style="border-color: var(--neutrals700)">
                        <i class="ti ti-device-floppy gk-text-primary600"></i>
                        Simpan
                    </button>
                </div>
            </div>
        </form>
    </main>
    <script>
        function gantiPaket(event, callerId, inputId, item, itemChild) {

            const o = JSON.parse(item);
            const oChild = JSON.parse(itemChild)
            select(event, callerId, inputId, o.id);
            const destinasiLabel = document.getElementById("destinasi-label").textContent = oChild.nama;
            const namaTiket = document.getElementById("nama-tiket").value = o.nama;
            const keterangan = document.getElementById("keterangan-tiket").value = o.keterangan;
            const penugasan = document.getElementById("penugasan-tiket").value = o.penugasan;
            const minPendaki = document.getElementById("minimal-tiket").value = o.min_pendaki;
        }
        function select(event, callerId, inputId, value) {
            const caller = document.getElementById(callerId);
            const input = document.getElementById(inputId);

            caller.textContent = event.target.textContent;
            input.value = value;
        }
    </script>
</div>
@endsection