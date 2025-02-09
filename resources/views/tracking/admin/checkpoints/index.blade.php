@extends('etiket.admin.template.index')

@section('link_head')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
@endsection

@section('css')

<style>
    .tiket-row:nth-child(odd) {
        background-color: rgb(230, 230, 230);
    }
    #map {
        position: relative;
        z-index: 1; /* Pastikan z-index lebih rendah */
    }

    .content {
        position: relative;
        z-index: 2; /* Elemen lainnya memiliki z-index lebih tinggi */
    }
</style>

@endsection

@section('main')

<div style="min-height: 80vh;" >
    <h3 class="font-bold mb-3 gk-text-base-black">Kelola Chekpoint Tracking</h3>

    <div class="d-flex align-items-center gap-3 mb-3 overflow-visible"> <!-- Tambahkan 'd-flex align-items-center gap-3' -->
        <a class="text-start text-black font-bold d-flex align-items-center gap-2 w-fit border-neutrals500 border-4 btn shadow gk-bg-base-white" href="{{route('admin.checkpoint.tambah')}}" style="border: 1px solid var(--neutrals500);">
            <img src="{{asset('assets/icon/tnks-plus.svg')}}" />
            Tambah Checkpoint
        </a>
        <button type="button" onclick="showMap()" class="btn btn-primary">
            map
        </button>
    </div>

    <div style="overflow: visible;">
        <div class="col-12 p-0 shadow rounded" style="overflow:auto;">
            <div id="map" class="mt-4" style="height: 400px; display: none;"></div>
            @include('tracking.admin.master-data.checkpoints.daftar', [
            "headers" => ["Nama Chekpoint", "Deskripsi Naik", "Deskripsi Turun", "Logitude", "Latitude", "QR", "Urutan", "Aksi"],
            "checkpoints" => $checkpoints,
            ])
        </div>
    </div>
</div>

<script>
    var map; // Variable untuk menyimpan instance peta
    var mapVisible = false; // Status apakah peta sedang terlihat atau tidak

    function showMap() {
        var mapContainer = document.getElementById('map');
        
        // Jika peta sedang terlihat, sembunyikan dan hapus peta
        if (mapVisible) {
            mapContainer.style.display = 'none';
            map.remove(); // Menghapus instance peta dari Leaflet
            mapVisible = false; // Ubah status menjadi tidak terlihat
            return;
        }

        // Cek jika ada data checkpoints
        var checkpoints = @json($checkpoints); // Mengambil data dari PHP ke JavaScript

        if (checkpoints.length === 0) {
            alert('Tidak ada checkpoint yang tersedia.');
            return;
        }

        mapContainer.style.display = 'block';

        // Ambil latitude dan longitude dari checkpoint tengah untuk center peta
        var middleIndex = Math.floor(checkpoints.length / 2); // Menghitung indeks tengah
        var middleCheckpoint = checkpoints[middleIndex]; // Checkpoint yang berada di tengah

        map = L.map('map').setView([middleCheckpoint.latitude, middleCheckpoint.longitude], 13);

        // Tambahkan layer peta citra satelit
        L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
            maxZoom: 19,
            attribution: 'Tiles © Esri — Source: Esri, DeLorme, NAVTEQ'
        }).addTo(map);

        // L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        //     maxZoom: 19,
        //     attribution: '© OpenStreetMap'
        // }).addTo(map);

        // Loop untuk menambahkan semua marker checkpoint
        checkpoints.forEach(function(checkpoint) {
            L.marker([checkpoint.latitude, checkpoint.longitude]).addTo(map)
                .bindPopup(checkpoint.nama)  // Menampilkan nama checkpoint di popup
                .openPopup();
        });

        mapVisible = true; // Ubah status menjadi terlihat
    }
</script>

@endsection