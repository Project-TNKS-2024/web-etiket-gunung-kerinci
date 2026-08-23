@extends('etiket.admin.template.index')

@section('css')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>
    #sos-map { height: 300px; border-radius: 8px; }
    .chat-container { max-height: 400px; overflow-y: auto; padding: 16px; background: #f8f9fa; border-radius: 8px; }
    .chat-msg { margin-bottom: 12px; max-width: 75%; }
    .chat-msg.hiker { margin-right: auto; }
    .chat-msg.admin { margin-left: auto; }
    .chat-bubble { padding: 10px 14px; border-radius: 12px; }
    .chat-msg.hiker .chat-bubble { background: #fff; border: 1px solid #dee2e6; }
    .chat-msg.admin .chat-bubble { background: #0d6efd; color: #fff; }
    .chat-meta { font-size: 11px; color: #6c757d; margin-top: 4px; }
</style>
@endsection

@section('main')
<div class="row">
    {{-- Header --}}
    <div class="col-12 mb-3">
        <div class="d-flex justify-content-between align-items-center">
            <h4 class="fw-semibold mb-0">
                <a href="{{ route('admin.sos.index') }}" class="text-muted me-2"><i class="ti ti-arrow-left"></i></a>
                SOS #{{ $sos->id }} — <span class="text-danger">{{ strtoupper($sos->severity) }}</span>
            </h4>
            <span class="badge bg-{{ $sos->status === 'active' ? 'danger' : ($sos->status === 'resolved' ? 'success' : 'warning') }} fs-6">
                {{ ucfirst($sos->status) }}
            </span>
        </div>
    </div>

    {{-- Info + Map --}}
    <div class="col-md-6 mb-4">
        <div class="card h-100">
            <div class="card-body">
                <h6 class="fw-semibold">Informasi Pendaki</h6>
                <table class="table table-sm">
                    <tr><td>Nama</td><td>{{ $sos->pendaki?->biodata?->first_name }} {{ $sos->pendaki?->biodata?->last_name }}</td></tr>
                    <tr><td>Booking</td><td><code>{{ $sos->id_booking }}</code></td></tr>
                    <tr><td>Destinasi</td><td>{{ $sos->destinasi?->nama }}</td></tr>
                    <tr><td>Pesan</td><td>{{ $sos->message ?? '-' }}</td></tr>
                    <tr><td>Koordinat</td><td>{{ $sos->latitude }}, {{ $sos->longitude }}</td></tr>
                    <tr><td>Waktu</td><td>{{ $sos->created_at->format('d/m/Y H:i:s') }}</td></tr>
                </table>

                {{-- Status Actions --}}
                @if($sos->status !== 'resolved' && $sos->status !== 'false_alarm')
                <div class="d-flex gap-2 mt-3">
                    @if($sos->status === 'active')
                    <form action="{{ route('admin.sos.updateStatus', $sos->id) }}" method="POST">
                        @csrf @method('PUT')
                        <input type="hidden" name="status" value="acknowledged">
                        <button class="btn btn-warning btn-sm">Acknowledge</button>
                    </form>
                    @endif
                    <form action="{{ route('admin.sos.updateStatus', $sos->id) }}" method="POST">
                        @csrf @method('PUT')
                        <input type="hidden" name="status" value="dispatched">
                        <button class="btn btn-primary btn-sm">Dispatch Rescue</button>
                    </form>
                    <form action="{{ route('admin.sos.updateStatus', $sos->id) }}" method="POST">
                        @csrf @method('PUT')
                        <input type="hidden" name="status" value="resolved">
                        <button class="btn btn-success btn-sm">Resolve</button>
                    </form>
                    <form action="{{ route('admin.sos.updateStatus', $sos->id) }}" method="POST">
                        @csrf @method('PUT')
                        <input type="hidden" name="status" value="false_alarm">
                        <button class="btn btn-secondary btn-sm">False Alarm</button>
                    </form>
                </div>
                @endif
            </div>
        </div>
    </div>

    <div class="col-md-6 mb-4">
        <div class="card h-100">
            <div class="card-body">
                <h6 class="fw-semibold">Lokasi SOS</h6>
                <div id="sos-map"></div>
            </div>
        </div>
    </div>

    {{-- Chat --}}
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <h6 class="fw-semibold">Chat SOS</h6>
                <div class="chat-container" id="chatContainer">
                    @forelse($messages as $msg)
                    <div class="chat-msg {{ $msg['sender_type'] }}">
                        <div class="chat-bubble">
                            @if($msg['type'] === 'image')
                                <img src="{{ $msg['content'] }}" alt="Image" style="max-width:200px; border-radius:8px;">
                            @else
                                {{ $msg['content'] }}
                            @endif
                        </div>
                        <div class="chat-meta {{ $msg['sender_type'] === 'admin' ? 'text-end' : '' }}">
                            {{ $msg['sender_name'] }} · {{ $msg['created_at'] }}
                        </div>
                    </div>
                    @empty
                    <p class="text-muted text-center">Belum ada pesan</p>
                    @endforelse
                </div>

                {{-- Send Message Form --}}
                @if(!in_array($sos->status, ['resolved', 'false_alarm']))
                <form action="{{ url('/api/sos/chat/' . $sos->id . '/send') }}" method="POST" enctype="multipart/form-data" class="mt-3">
                    @csrf
                    <input type="hidden" name="type" value="text" id="msgType">
                    <div class="input-group">
                        <input type="text" name="content" class="form-control" placeholder="Ketik pesan..." id="msgContent">
                        <button type="submit" class="btn btn-primary"><i class="ti ti-send"></i></button>
                    </div>
                </form>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
const map = L.map('sos-map').setView([{{ $sos->latitude }}, {{ $sos->longitude }}], 15);
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { attribution: '© OpenStreetMap' }).addTo(map);
L.marker([{{ $sos->latitude }}, {{ $sos->longitude }}], {
    icon: L.divIcon({ className: '', html: '<i class="ti ti-urgent" style="font-size:28px;color:#dc3545;"></i>', iconSize: [28, 28] })
}).addTo(map).bindPopup('SOS - {{ $sos->pendaki?->fullName }}').openPopup();

// Auto-scroll chat to bottom
document.getElementById('chatContainer').scrollTop = document.getElementById('chatContainer').scrollHeight;
</script>
@endsection
