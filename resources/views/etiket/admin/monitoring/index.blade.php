@extends('etiket.admin.template.index')

@section('css')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>
    #monitoring-map { height: calc(100vh - 250px); min-height: 500px; border-radius: 8px; }
    .hiker-sidebar { max-height: calc(100vh - 250px); overflow-y: auto; }
    .hiker-card { padding: 10px; border-radius: 8px; margin-bottom: 8px; cursor: pointer; transition: background 0.2s; }
    .hiker-card:hover { background: #f0f0f0; }
    .hiker-card.sos { border-left: 4px solid #dc3545; background: #fff5f5; }
    .hiker-card.normal { border-left: 4px solid #198754; }
    .hiker-card.stale { border-left: 4px solid #ffc107; }
    .stat-badge { display: inline-flex; align-items: center; gap: 4px; padding: 4px 10px; border-radius: 20px; font-size: 13px; font-weight: 600; }
</style>
@endsection

@section('main')
<div class="row">
    {{-- Header Stats --}}
    <div class="col-12 mb-3">
        <div class="d-flex justify-content-between align-items-center">
            <h4 class="fw-semibold mb-0"><i class="ti ti-radar-2 me-2"></i>Live Monitoring</h4>
            <div class="d-flex gap-2">
                <span class="stat-badge bg-success-subtle text-success">
                    <i class="ti ti-walk"></i> {{ $activeBookings->sum(fn($b) => $b->pendakis->count()) }} Pendaki Aktif
                </span>
                @if($activeSos->count() > 0)
                <span class="stat-badge bg-danger-subtle text-danger">
                    <i class="ti ti-urgent"></i> {{ $activeSos->count() }} SOS
                </span>
                @endif
                @if($activeEmergencies > 0)
                <span class="stat-badge bg-warning-subtle text-warning">
                    <i class="ti ti-alert-triangle"></i> {{ $activeEmergencies }} Emergency
                </span>
                @endif
            </div>
        </div>
    </div>

    {{-- Map --}}
    <div class="col-md-9 mb-4">
        <div class="card h-100">
            <div class="card-body p-2">
                <div id="monitoring-map"></div>
            </div>
        </div>
    </div>

    {{-- Sidebar: Active Hikers --}}
    <div class="col-md-3 mb-4">
        <div class="card h-100">
            <div class="card-body">
                <h6 class="fw-semibold mb-3">Pendaki Aktif</h6>
                <div class="hiker-sidebar">
                    @forelse($activeBookings as $booking)
                        @foreach($booking->pendakis as $pendaki)
                            @php
                                $pos = $lastPositions->get($pendaki->id);
                                $hasSos = $activeSos->where('id_pendaki', $pendaki->id)->count() > 0;
                                $isStale = $pos && $pos->recorded_at->diffInMinutes(now()) > 30;
                                $cardClass = $hasSos ? 'sos' : ($isStale ? 'stale' : 'normal');
                            @endphp
                            <div class="hiker-card {{ $cardClass }}" onclick="focusHiker('{{ $pendaki->id }}')">
                                <div class="fw-semibold">{{ $pendaki->fullName ?? 'Pendaki' }}</div>
                                <small class="text-muted">
                                    {{ $booking->gateMasuk?->nama ?? '' }}
                                    @if($pos)
                                        · {{ $pos->recorded_at->diffForHumans() }}
                                    @else
                                        · Belum ada GPS
                                    @endif
                                </small>
                                @if($hasSos)
                                    <span class="badge bg-danger ms-1">SOS</span>
                                @endif
                                @if($pos && $pos->battery_level)
                                    <small class="text-muted d-block">🔋 {{ $pos->battery_level }}%</small>
                                @endif
                            </div>
                        @endforeach
                    @empty
                        <p class="text-muted text-center">Tidak ada pendaki aktif</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
const map = L.map('monitoring-map').setView([-1.6974, 101.2642], 13);
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { attribution: '© OpenStreetMap' }).addTo(map);

const hikerMarkers = {};

// Plot trail posts
@foreach($posts as $post)
L.circle([{{ $post->latitude }}, {{ $post->longitude }}], {
    radius: {{ $post->radius_meter }}, color: '#6c757d', fillOpacity: 0.05, weight: 1
}).addTo(map);
L.marker([{{ $post->latitude }}, {{ $post->longitude }}], {
    icon: L.divIcon({ className: '', html: '<i class="ti ti-flag" style="font-size:16px;color:#6c757d;"></i>', iconSize: [16, 16] })
}).addTo(map).bindPopup('<b>{{ $post->nama }}</b>');
@endforeach

// Plot active hikers
@foreach($activeBookings as $booking)
    @foreach($booking->pendakis as $pendaki)
        @php $pos = $lastPositions->get($pendaki->id); $hasSos = $activeSos->where('id_pendaki', $pendaki->id)->count() > 0; @endphp
        @if($pos)
        hikerMarkers['{{ $pendaki->id }}'] = L.marker([{{ $pos->latitude }}, {{ $pos->longitude }}], {
            icon: L.divIcon({
                className: '',
                html: '<div style="width:14px;height:14px;border-radius:50%;background:{{ $hasSos ? "#dc3545" : "#198754" }};border:2px solid #fff;box-shadow:0 0 4px rgba(0,0,0,0.3);"></div>',
                iconSize: [14, 14]
            })
        }).addTo(map).bindPopup('<b>{{ $pendaki->fullName }}</b><br>{{ $pos->recorded_at->format("H:i") }}<br>🔋 {{ $pos->battery_level ?? "?" }}%');
        @endif
    @endforeach
@endforeach

// Plot SOS markers
@foreach($activeSos as $sos)
L.marker([{{ $sos->latitude }}, {{ $sos->longitude }}], {
    icon: L.divIcon({ className: '', html: '<i class="ti ti-urgent" style="font-size:24px;color:#dc3545;"></i>', iconSize: [24, 24] })
}).addTo(map).bindPopup('<b>SOS - {{ $sos->severity }}</b><br>{{ $sos->pendaki?->fullName }}<br><a href="/admin/sos/{{ $sos->id }}">Detail</a>');
@endforeach

function focusHiker(pendakiId) {
    const marker = hikerMarkers[pendakiId];
    if (marker) {
        map.setView(marker.getLatLng(), 15);
        marker.openPopup();
    }
}
</script>
@endsection
