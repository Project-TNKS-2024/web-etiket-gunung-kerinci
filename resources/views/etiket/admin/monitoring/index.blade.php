@extends('etiket.admin.template.index')

@section('css')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>
    #monitoring-map { height: calc(100vh - 250px); min-height: 500px; border-radius: 8px; }
    .hiker-sidebar { max-height: calc(100vh - 250px); overflow-y: auto; }
    .hiker-card { padding: 10px; border-radius: 8px; margin-bottom: 8px; cursor: pointer; background: #fff; }
    .hiker-card:hover { background: #f0f0f0; }
    .hiker-card.sos { border-left: 4px solid #dc3545 !important; background: #fff5f5; }
    .hiker-card.normal { border-left: 4px solid #198754 !important; }
    .hiker-card.stale { border-left: 4px solid #ffc107 !important; }
    .stat-badge { display: inline-flex; gap: 4px; padding: 4px 10px; border-radius: 20px; font-size: 13px; font-weight: 600; }
</style>
@endsection

@section('main')
<div class="row">
    <div class="col-12 mb-3">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
            <h4 class="fw-semibold mb-0"><i class="ti ti-radar-2 me-2"></i>Live Monitoring</h4>
            <div class="d-flex gap-2 flex-wrap">
                <span id="hiker-count" class="stat-badge bg-success-subtle text-success"></span>
                <span id="sos-count" class="stat-badge bg-danger-subtle text-danger"></span>
                <span id="emergency-count" class="stat-badge bg-warning-subtle text-warning"></span>
            </div>
        </div>
        <small id="monitoring-status" class="text-muted" role="status" aria-live="polite">Memuat posisi…</small>
    </div>
    <div class="col-md-9 mb-4">
        <div class="card h-100"><div class="card-body p-2"><div id="monitoring-map"></div></div></div>
    </div>
    <div class="col-md-3 mb-4">
        <div class="card h-100"><div class="card-body">
            <h6 class="fw-semibold mb-3">Pendaki Aktif</h6>
            <div id="hiker-sidebar" class="hiker-sidebar"></div>
        </div></div>
    </div>
</div>
<script id="monitoring-data" type="application/json">@json($snapshot)</script>
@endsection

@section('js')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="{{ asset('assets/js/admin-monitoring.js') }}?v={{ filemtime(public_path('assets/js/admin-monitoring.js')) }}"></script>
@endsection
