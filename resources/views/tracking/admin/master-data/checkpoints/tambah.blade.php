@extends('etiket.admin.template.index')

@section('link_head')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
@endsection

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
        <a class="btn btn-primary w-fit text-start" href="{{ route('admin.checkpoint.daftar') }}">
            < Kembali </a>
                <header class="text-2xl font-bold gk-text-base-black">Tambah Checkpoint</header>
                <form id="checkpoint-form" action="{{ route('admin.checkpoint.tambahAction') }}" method="post" enctype="multipart/form-data">
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
                    <div class="row">
                        <div class="col-md-6 col-sm-12">
                            <label class="form-label">Nama Checkpoint</label>
                            <input class="form-control borderx bg-white" name="nama" id="lokasi-nama" value="" placeholder="Nama Checkpoint" required />
                        </div>
                        <div class="col-md-6 col-sm-12">
                            <label class="form-label">Deskripsi</label>
                            <textarea name="deskripsi" id="lokasi-deskripsi" class="form-control bg-white borderx" style="min-height: 75px;" placeholder="Deskripsi Checkpoint"></textarea>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4 col-sm-6">
                            <label class="form-label" for="latitude">Latitude</label>
                            <input class="form-control borderx bg-white" name="latitude" id="latitude" type="text" value="" placeholder="Latitude" required />
                        </div>
                        <div class="col-md-4 col-sm-6">
                            <label class="form-label" for="longitude">Longitude</label>
                            <input class="form-control borderx bg-white" name="longitude" id="longitude" type="text" value="" placeholder="Longitude" required />
                        </div>
                        <div class="col-md-4 col-sm-6">
                            <label class="form-label" for="urutan">Urutan Checkpoint</label>
                            <select class="form-control borderx bg-white" name="urutan" id="urutan" required>
                                <option value="1">Checkpoint pertama</option>
                                @foreach($checkpoints as $checkpoint)
                                    <option value="{{ $checkpoint->urutan + 1 }}">Setelah {{ $checkpoint->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="row mt-3 justify-content-end">
                        <div class="col-md-4 col-sm-12 mb-2">
                            <button type="button" onclick="showMap()" class="btn font-bold btn-success gk-bg-success700 w-fit text-start shadow d-flex align-items-center gap-1 px-3 ms-auto" style="border-color: var(--neutrals700)">
                                Tampilkan Peta
                            </button>
                        </div>
                        <div class="col-md-2 col-sm-12">
                            <button type="submit" class="btn font-bold btn-primary gk-bg-primary700 w-fit text-start shadow d-flex align-items-center gap-1 px-3 ms-auto" style="border-color: var(--neutrals700)">
                                <img width="20" src="{{asset('assets/icon/tnks/save-light.svg')}}" class="gk-bg-primary700" />
                                Simpan
                            </button>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <div id="map" class="mt-4" style="height: 400px; display: none;"></div>
                        </div>
                    </div>
                </form>
    </main>
</div>

<script>
    // untuk validasi pengisian latitude dan longitude
    function validateLatLong(input) {
        input.addEventListener('input', function () {
            let value = input.value;

            // Allow only one minus sign at the beginning
            let sanitized = value.replace(/(?!^-)[^0-9.]/g, '');  // Remove non-numeric characters except '-'
            
            // Allow only one minus at the start and one period anywhere
            if (sanitized.indexOf('-') > 0) {
                sanitized = sanitized.replace('-', ''); // Remove '-' if not at start
            }

            let parts = sanitized.split('.');
            if (parts.length > 2) {
                sanitized = parts[0] + '.' + parts.slice(1).join(''); // Allow only one '.'
            }

            input.value = sanitized;
        });
    }

    const latitudeInput = document.getElementById('latitude');
    const longitudeInput = document.getElementById('longitude');

    validateLatLong(latitudeInput);
    validateLatLong(longitudeInput);

    // untuk menampilkan map
    function showMap() {
        var latitude = parseFloat(document.getElementById('latitude').value);
        var longitude = parseFloat(document.getElementById('longitude').value);

        if (isNaN(latitude) || isNaN(longitude)) {
            alert('Masukkan latitude dan longitude yang valid.');
            return;
        }

        // Tampilkan elemen peta
        document.getElementById('map').style.display = 'block';

        // Hapus instance map sebelumnya, jika ada
        if (window.currentMap) {
            window.currentMap.remove();
        }

        // Buat instance baru peta
        window.currentMap = L.map('map').setView([latitude, longitude], 13);

        // Tambahkan tile layer dari OpenStreetMap
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 20,
            attribution: '© OpenStreetMap'
        }).addTo(window.currentMap);

        // Tambahkan marker pada lokasi
        L.marker([latitude, longitude]).addTo(window.currentMap)
            .bindPopup('Lokasi CheckPoint')
            .openPopup();
    }

</script>
@endsection