@extends('etiket.admin.template.index')

@section('css')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>#posts-map { height: 350px; border-radius: 8px; }</style>
@endsection

@section('main')
<div class="row">
    <div class="col-12 mb-3">
        <div class="d-flex justify-content-between align-items-center">
            <h4 class="fw-semibold mb-0"><i class="ti ti-map-pin me-2"></i>Manajemen Pos Jalur</h4>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addPostModal">
                <i class="ti ti-plus me-1"></i> Tambah Pos
            </button>
        </div>
    </div>

    {{-- Map --}}
    <div class="col-12 mb-4">
        <div class="card">
            <div class="card-body">
                <div id="posts-map"></div>
            </div>
        </div>
    </div>

    {{-- Posts Table --}}
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Urutan</th>
                                <th>Nama</th>
                                <th>Gate</th>
                                <th>Koordinat</th>
                                <th>Altitude</th>
                                <th>Radius</th>
                                <th>QR Code</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($posts as $post)
                            <tr>
                                <td>{{ $post->urutan }}</td>
                                <td>{{ $post->nama }}</td>
                                <td>{{ $post->gate?->nama }} ({{ $post->gate?->destinasi?->nama }})</td>
                                <td><small>{{ $post->latitude }}, {{ $post->longitude }}</small></td>
                                <td>{{ $post->altitude ? $post->altitude . 'm' : '-' }}</td>
                                <td>{{ $post->radius_meter }}m</td>
                                <td><code>{{ $post->qr_code_value }}</code></td>
                                <td>
                                    <span class="badge bg-{{ $post->status ? 'success' : 'secondary' }}">
                                        {{ $post->status ? 'Aktif' : 'Nonaktif' }}
                                    </span>
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-warning" onclick="editPost({{ json_encode($post) }})" data-bs-toggle="modal" data-bs-target="#editPostModal">
                                        <i class="ti ti-edit"></i>
                                    </button>
                                    <form action="{{ route('admin.posts.destroy', $post->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus pos ini?')">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-sm btn-danger"><i class="ti ti-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="9" class="text-center text-muted">Belum ada pos</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Add Post Modal --}}
<div class="modal fade" id="addPostModal" tabindex="-1">
    <div class="modal-dialog">
        <form action="{{ route('admin.posts.store') }}" method="POST">
            @csrf
            <div class="modal-content">
                <div class="modal-header"><h5 class="modal-title">Tambah Pos Baru</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                <div class="modal-body">
                    <div class="mb-3"><label class="form-label">Gate</label><select name="id_gate" class="form-select" required>@foreach($gates as $g)<option value="{{ $g->id }}">{{ $g->nama }} ({{ $g->destinasi?->nama }})</option>@endforeach</select></div>
                    <div class="mb-3"><label class="form-label">Nama Pos</label><input type="text" name="nama" class="form-control" required></div>
                    <div class="mb-3"><label class="form-label">Urutan</label><input type="number" name="urutan" class="form-control" min="1" required></div>
                    <div class="row"><div class="col-6 mb-3"><label class="form-label">Latitude</label><input type="text" name="latitude" class="form-control" required></div><div class="col-6 mb-3"><label class="form-label">Longitude</label><input type="text" name="longitude" class="form-control" required></div></div>
                    <div class="row"><div class="col-6 mb-3"><label class="form-label">Altitude (m)</label><input type="number" name="altitude" class="form-control"></div><div class="col-6 mb-3"><label class="form-label">Radius (m)</label><input type="number" name="radius_meter" class="form-control" value="150" min="50" max="500"></div></div>
                    <div class="mb-3"><label class="form-label">Detail</label><textarea name="detail" class="form-control" rows="2"></textarea></div>
                </div>
                <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button><button type="submit" class="btn btn-primary">Simpan</button></div>
            </div>
        </form>
    </div>
</div>

{{-- Edit Post Modal --}}
<div class="modal fade" id="editPostModal" tabindex="-1">
    <div class="modal-dialog">
        <form id="editPostForm" method="POST">
            @csrf @method('PUT')
            <div class="modal-content">
                <div class="modal-header"><h5 class="modal-title">Edit Pos</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                <div class="modal-body">
                    <div class="mb-3"><label class="form-label">Gate</label><select name="id_gate" id="edit_id_gate" class="form-select" required>@foreach($gates as $g)<option value="{{ $g->id }}">{{ $g->nama }} ({{ $g->destinasi?->nama }})</option>@endforeach</select></div>
                    <div class="mb-3"><label class="form-label">Nama Pos</label><input type="text" name="nama" id="edit_nama" class="form-control" required></div>
                    <div class="mb-3"><label class="form-label">Urutan</label><input type="number" name="urutan" id="edit_urutan" class="form-control" min="1" required></div>
                    <div class="row"><div class="col-6 mb-3"><label class="form-label">Latitude</label><input type="text" name="latitude" id="edit_latitude" class="form-control" required></div><div class="col-6 mb-3"><label class="form-label">Longitude</label><input type="text" name="longitude" id="edit_longitude" class="form-control" required></div></div>
                    <div class="row"><div class="col-6 mb-3"><label class="form-label">Altitude (m)</label><input type="number" name="altitude" id="edit_altitude" class="form-control"></div><div class="col-6 mb-3"><label class="form-label">Radius (m)</label><input type="number" name="radius_meter" id="edit_radius_meter" class="form-control" min="50" max="500"></div></div>
                    <div class="mb-3"><label class="form-label">Detail</label><textarea name="detail" id="edit_detail" class="form-control" rows="2"></textarea></div>
                    <div class="mb-3"><label class="form-label">Status</label><select name="status" id="edit_status" class="form-select"><option value="1">Aktif</option><option value="0">Nonaktif</option></select></div>
                </div>
                <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button><button type="submit" class="btn btn-primary">Update</button></div>
            </div>
        </form>
    </div>
</div>
@endsection

@section('js')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
const map = L.map('posts-map').setView([-1.6974, 101.2642], 13);
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { attribution: '© OpenStreetMap' }).addTo(map);

@foreach($posts as $post)
    @if($post->status)
    L.marker([{{ $post->latitude }}, {{ $post->longitude }}])
        .addTo(map)
        .bindPopup('<b>{{ $post->nama }}</b><br>Urutan: {{ $post->urutan }}<br>Radius: {{ $post->radius_meter }}m');
    L.circle([{{ $post->latitude }}, {{ $post->longitude }}], { radius: {{ $post->radius_meter }}, color: '#3388ff', fillOpacity: 0.1 }).addTo(map);
    @endif
@endforeach

function editPost(post) {
    document.getElementById('editPostForm').action = '/admin/posts/' + post.id;
    document.getElementById('edit_id_gate').value = post.id_gate;
    document.getElementById('edit_nama').value = post.nama;
    document.getElementById('edit_urutan').value = post.urutan;
    document.getElementById('edit_latitude').value = post.latitude;
    document.getElementById('edit_longitude').value = post.longitude;
    document.getElementById('edit_altitude').value = post.altitude || '';
    document.getElementById('edit_radius_meter').value = post.radius_meter;
    document.getElementById('edit_detail').value = post.detail || '';
    document.getElementById('edit_status').value = post.status ? '1' : '0';
}
</script>
@endsection
