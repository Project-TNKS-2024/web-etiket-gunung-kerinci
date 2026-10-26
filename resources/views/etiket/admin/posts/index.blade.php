@extends('etiket.admin.template.index')

@section('css')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>#posts-map { height: 350px; border-radius: 8px; }</style>
@endsection

@section('main')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="fw-semibold mb-0">Manajemen Pos Jalur</h4>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addPostModal" @disabled($gates->isEmpty())>Tambah Pos</button>
</div>
@if(session('success'))
<div class="alert alert-success" role="status">{{ session('success') }}</div>
@endif
@if($errors->any())
<div class="alert alert-danger" role="alert">
    <strong>Data belum disimpan.</strong>
    <ul class="mb-0">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
</div>
@endif
<div class="card"><div class="card-body">
    <form method="GET" action="{{ route('admin.posts.index') }}" class="row g-3 align-items-end">
        <div class="col-md-5">
            <label for="filter_gate" class="form-label">Jalur / Gate</label>
            <select id="filter_gate" name="gate_id" class="form-select">
                <option value="">Semua jalur</option>
                @foreach($gates as $gate)
                <option value="{{ $gate->id }}" @selected((string) request('gate_id') === (string) $gate->id)>{{ $gate->nama }} ({{ $gate->destinasi?->nama }})</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-4">
            <label for="filter_q" class="form-label">Cari nama pos</label>
            <input id="filter_q" name="q" value="{{ request('q') }}" maxlength="100" class="form-control">
        </div>
        <div class="col-md-3"><button class="btn btn-primary">Tampilkan</button> <a href="{{ route('admin.posts.index') }}" class="btn btn-outline-secondary">Reset</a></div>
    </form>
</div></div>
<div class="card"><div class="card-body"><div id="posts-map"></div></div></div>
<div class="card"><div class="card-body">
    <p class="text-muted">{{ $posts->count() }} pos. Belum lengkap berarti ada informasi yang belum diisi; kelengkapan bukan bukti verifikasi lapangan.</p>
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead><tr><th>Urutan</th><th>Nama pos</th><th>Gambar</th><th>Jalur / Gate</th><th>Ketinggian</th><th>Sumber mata air</th><th>Jarak ke puncak</th><th>Berkemah</th><th>Koordinat / Radius</th><th>QR Code</th><th>Status</th><th>Aksi</th></tr></thead>
            <tbody>
            @forelse($posts as $post)
                @php
                    $incomplete = $post->altitude === null || $post->distance_to_summit_m === null
                        || blank($post->water_source_status) || $post->water_source_status === 'Belum diketahui'
                        || blank($post->camping_status) || $post->camping_status === 'Belum diketahui';
                @endphp
                <tr>
                    <td>{{ $post->urutan }}</td>
                    <td>{{ $post->nama }}
                        @if($incomplete)<span class="badge bg-warning text-dark">Belum lengkap</span>@endif
                    </td>
                    <td>
                        @if($post->map_image_url)
                        <a href="{{ $post->map_image_url }}" target="_blank" rel="noopener" aria-label="Lihat gambar {{ $post->nama }}">
                            <img src="{{ $post->map_image_url }}" alt="Gambar {{ $post->nama }}" width="80" height="80" loading="lazy" class="rounded border" style="object-fit:contain">
                        </a>
                        @else
                        <span class="text-muted">Belum ada gambar</span>
                        @endif
                    </td>
                    <td>{{ $post->gate?->nama }} ({{ $post->gate?->destinasi?->nama }})</td>
                    <td>{{ $post->altitude !== null ? $post->altitude . ' mdpl' : 'Belum diketahui' }}</td>
                    <td>{{ $post->water_source_status ?: 'Belum diketahui' }}</td>
                    <td>{{ $post->distance_to_summit_m !== null ? $post->distance_to_summit_m . ' m' : 'Belum diketahui' }}</td>
                    <td>{{ $post->camping_status ?: 'Belum diketahui' }}</td>
                    <td><small>{{ $post->latitude }}, {{ $post->longitude }}<br>Radius {{ $post->radius_meter }} m</small></td>
                    <td><code>{{ $post->qr_code_value }}</code></td>
                    <td><span class="badge bg-{{ $post->status ? 'success' : 'secondary' }}">{{ $post->status ? 'Aktif' : 'Nonaktif' }}</span></td>
                    <td class="text-nowrap">
                        <button type="button" class="btn btn-sm btn-warning js-edit-post" data-post="{{ json_encode($post) }}" data-update-url="{{ route('admin.posts.update', $post->id) }}" data-bs-toggle="modal" data-bs-target="#editPostModal">Edit</button>
                        <form action="{{ route('admin.posts.destroy', $post->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus pos ini?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-danger">Hapus</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="12" class="text-center text-muted">Tidak ada pos untuk jalur atau pencarian ini.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div></div>

<div class="modal fade" id="addPostModal" tabindex="-1" aria-labelledby="addPostTitle">
    <div class="modal-dialog modal-lg"><form action="{{ route('admin.posts.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="_form" value="add">
        <div class="modal-content">
            <div class="modal-header"><h5 class="modal-title" id="addPostTitle">Tambah Pos</h5><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button></div>
            <div class="modal-body">@include('etiket.admin.posts.fields', ['prefix' => 'add'])</div>
            <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button><button class="btn btn-primary">Simpan</button></div>
        </div>
    </form></div>
</div>
<div class="modal fade" id="editPostModal" tabindex="-1" aria-labelledby="editPostTitle">
    <div class="modal-dialog modal-lg"><form id="editPostForm" method="POST" enctype="multipart/form-data">
        @csrf @method('PUT')
        <input type="hidden" name="_form" value="edit">
        <input type="hidden" name="_post_id" id="edit_post_id">
        <div class="modal-content">
            <div class="modal-header"><h5 class="modal-title" id="editPostTitle">Edit Pos</h5><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button></div>
            <div class="modal-body">@include('etiket.admin.posts.fields', ['prefix' => 'edit'])</div>
            <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button><button class="btn btn-primary">Simpan perubahan</button></div>
        </div>
    </form></div>
</div>
@endsection

@section('js')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
const posts = {{ Illuminate\Support\Js::from($posts) }};
if (typeof L !== 'undefined') {
const map = L.map('posts-map').setView([-1.6974, 101.2642], 13);
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { attribution: '© OpenStreetMap' }).addTo(map);
const markers = [];
posts.filter(post => post.status).forEach(post => {
    const content = document.createElement('div');
    const name = document.createElement('strong');
    name.textContent = post.nama;
    content.append(name, document.createElement('br'), `Urutan: ${post.urutan} · Radius: ${post.radius_meter} m`);
    const marker = L.marker([post.latitude, post.longitude]).addTo(map).bindPopup(content);
    markers.push(marker);
    L.circle([post.latitude, post.longitude], { radius: post.radius_meter, color: '#3388ff', fillOpacity: 0.1 }).addTo(map);
});
if (markers.length) map.fitBounds(L.featureGroup(markers).getBounds().pad(0.15), { maxZoom: 15 });
} else {
    document.getElementById('posts-map').textContent = 'Peta belum dapat dimuat. Daftar dan formulir pos tetap tersedia.';
}

const imageObjectUrls = {};
function previewPostImage(prefix, url) {
    const preview = document.getElementById(prefix + '_image_preview');
    const empty = document.getElementById(prefix + '_image_empty');
    preview.onload = () => {
        preview.classList.remove('d-none');
        empty.classList.add('d-none');
    };
    preview.onerror = () => {
        preview.classList.add('d-none');
        empty.textContent = 'Gambar tidak dapat ditampilkan.';
        empty.classList.remove('d-none');
    };
    preview.classList.add('d-none');
    empty.textContent = 'Belum ada gambar.';
    empty.classList.toggle('d-none', !!url);
    if (url) preview.src = url;
    else preview.removeAttribute('src');
}

function resetPostImage(prefix, url = '') {
    if (imageObjectUrls[prefix]) URL.revokeObjectURL(imageObjectUrls[prefix]);
    delete imageObjectUrls[prefix];
    const input = document.getElementById(prefix + '_image');
    input.value = '';
    input.setCustomValidity('');
    input.dataset.currentUrl = url;
    previewPostImage(prefix, url);
}

['add', 'edit'].forEach(prefix => {
    const input = document.getElementById(prefix + '_image');
    input.addEventListener('change', () => {
        if (imageObjectUrls[prefix]) URL.revokeObjectURL(imageObjectUrls[prefix]);
        delete imageObjectUrls[prefix];
        input.setCustomValidity('');
        const file = input.files[0];
        if (!file) return previewPostImage(prefix, input.dataset.currentUrl || '');
        if (!['image/jpeg', 'image/png', 'image/webp'].includes(file.type) || file.size > 5 * 1024 * 1024) {
            input.setCustomValidity('Pilih satu gambar JPG, PNG, atau WebP maksimal 5 MB.');
            input.reportValidity();
            return previewPostImage(prefix, input.dataset.currentUrl || '');
        }
        imageObjectUrls[prefix] = URL.createObjectURL(file);
        previewPostImage(prefix, imageObjectUrls[prefix]);
    });
    document.getElementById(prefix === 'add' ? 'addPostModal' : 'editPostModal')
        .addEventListener('hidden.bs.modal', () => resetPostImage(prefix, input.dataset.currentUrl || ''));
});

function editPost(post, url) {
    document.getElementById('editPostForm').action = url;
    document.getElementById('edit_post_id').value = post.id;
    ['id_gate', 'nama', 'urutan', 'latitude', 'longitude', 'altitude', 'radius_meter', 'detail', 'water_source_status', 'distance_to_summit_m', 'camping_status'].forEach(field => {
        const input = document.getElementById('edit_' + field);
        const fallback = ['water_source_status', 'camping_status'].includes(field) ? 'Belum diketahui' : '';
        input.value = post[field] ?? fallback;
    });
    document.getElementById('edit_status').value = post.status ? '1' : '0';
    resetPostImage('edit', post.map_image_url || '');
}
document.querySelectorAll('.js-edit-post').forEach(button => {
    button.addEventListener('click', () => editPost(JSON.parse(button.dataset.post), button.dataset.updateUrl));
});
@if($errors->any() && old('_form') === 'edit')
const previous = {{ Illuminate\Support\Js::from(old()) }};
const previousButton = [...document.querySelectorAll('.js-edit-post')].find(button => String(JSON.parse(button.dataset.post).id) === String(previous._post_id));
if (previousButton) {
    editPost({ ...JSON.parse(previousButton.dataset.post), ...previous, id: previous._post_id, status: previous.status === '1' }, previousButton.dataset.updateUrl);
    bootstrap.Modal.getOrCreateInstance(document.getElementById('editPostModal')).show();
}
@elseif($errors->any() && old('_form') === 'add')
bootstrap.Modal.getOrCreateInstance(document.getElementById('addPostModal')).show();
@endif
</script>
@endsection
