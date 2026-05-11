@extends('etiket.admin.template.index')

@section('css')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>
    #emergency-map { height: 400px; border-radius: 8px; }
    .severity-low { color: #ffc107; }
    .severity-medium { color: #fd7e14; }
    .severity-high { color: #dc3545; }
    .severity-critical { color: #6f42c1; }
    .badge-active { background-color: #dc3545; }
    .badge-acknowledged { background-color: #ffc107; color: #000; }
    .badge-resolved { background-color: #198754; }
</style>
@endsection

@section('main')
<div class="row">
    {{-- Header --}}
    <div class="col-12 mb-3">
        <div class="d-flex justify-content-between align-items-center">
            <h4 class="fw-semibold mb-0">
                <i class="ti ti-alert-triangle me-2"></i>Emergency Dashboard
                @if($activeCount > 0)
                    <span class="badge bg-danger ms-2">{{ $activeCount }} Aktif</span>
                @endif
            </h4>
            <button class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#broadcastModal">
                <i class="ti ti-broadcast me-1"></i> Broadcast Darurat
            </button>
        </div>
    </div>

    {{-- Map --}}
    <div class="col-12 mb-4">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Peta Darurat</h5>
                <div id="emergency-map"></div>
            </div>
        </div>
    </div>

    {{-- Emergency List --}}
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Daftar Pesan Darurat</h5>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Status</th>
                                <th>Severity</th>
                                <th>Tipe</th>
                                <th>Judul</th>
                                <th>Pengirim</th>
                                <th>Destinasi</th>
                                <th>Waktu</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($emergencies as $e)
                            <tr>
                                <td>
                                    <span class="badge badge-{{ $e->status }}">{{ ucfirst($e->status) }}</span>
                                </td>
                                <td>
                                    <span class="severity-{{ $e->severity }} fw-bold">{{ strtoupper($e->severity) }}</span>
                                </td>
                                <td>{{ $e->type === 'hiker_alert' ? 'Pendaki' : 'Admin' }}</td>
                                <td>{{ $e->title }}</td>
                                <td>
                                    @if($e->type === 'hiker_alert' && $e->pendaki)
                                        {{ $e->pendaki->biodata?->first_name ?? '-' }}
                                    @else
                                        {{ $e->user?->biodata?->first_name ?? 'Admin' }}
                                    @endif
                                </td>
                                <td>{{ $e->destinasi?->nama ?? '-' }}</td>
                                <td>{{ $e->created_at->format('d/m/Y H:i') }}</td>
                                <td>
                                    @if($e->status === 'active')
                                        <form action="{{ route('admin.emergency.acknowledge', $e->id) }}" method="POST" class="d-inline">
                                            @csrf @method('PUT')
                                            <button class="btn btn-sm btn-warning">Acknowledge</button>
                                        </form>
                                    @endif
                                    @if($e->status !== 'resolved')
                                        <form action="{{ route('admin.emergency.resolve', $e->id) }}" method="POST" class="d-inline">
                                            @csrf @method('PUT')
                                            <button class="btn btn-sm btn-success">Resolve</button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="8" class="text-center text-muted">Tidak ada pesan darurat</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                {{ $emergencies->links() }}
            </div>
        </div>
    </div>
</div>

{{-- Broadcast Modal --}}
<div class="modal fade" id="broadcastModal" tabindex="-1">
    <div class="modal-dialog">
        <form action="{{ route('admin.emergency.broadcast') }}" method="POST">
            @csrf
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title">Broadcast Pesan Darurat</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Destinasi</label>
                        <select name="id_destinasi" class="form-select" required>
                            @foreach($destinasis as $d)
                                <option value="{{ $d->id }}">{{ $d->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Severity</label>
                        <select name="severity" class="form-select" required>
                            <option value="low">Low</option>
                            <option value="medium">Medium</option>
                            <option value="high" selected>High</option>
                            <option value="critical">Critical</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Judul</label>
                        <input type="text" name="title" class="form-control" required maxlength="255">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Deskripsi</label>
                        <textarea name="description" class="form-control" rows="4" required maxlength="2000"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger">Kirim Broadcast</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@section('js')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    // Initialize map centered on Gunung Kerinci
    const map = L.map('emergency-map').setView([-1.6974, 101.2642], 13);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap'
    }).addTo(map);

    // Plot active emergencies with coordinates
    @foreach($emergencies as $e)
        @if($e->status === 'active' && $e->latitude && $e->longitude)
            L.marker([{{ $e->latitude }}, {{ $e->longitude }}], {
                icon: L.divIcon({
                    className: 'severity-{{ $e->severity }}',
                    html: '<i class="ti ti-alert-triangle" style="font-size:24px;"></i>',
                    iconSize: [24, 24]
                })
            }).addTo(map).bindPopup('<b>{{ $e->title }}</b><br>{{ $e->severity }} - {{ $e->created_at->format("H:i") }}');
        @endif
    @endforeach
</script>
@endsection
