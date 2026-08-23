@extends('etiket.admin.template.index')

@section('css')
<style>
    .sos-active { border-left: 4px solid #dc3545; }
    .sos-acknowledged { border-left: 4px solid #ffc107; }
    .sos-dispatched { border-left: 4px solid #0d6efd; }
    .severity-high { color: #dc3545; font-weight: bold; }
    .severity-medium { color: #fd7e14; font-weight: bold; }
    .severity-low { color: #ffc107; font-weight: bold; }
</style>
@endsection

@section('main')
<div class="row">
    <div class="col-12 mb-3">
        <h4 class="fw-semibold">
            <i class="ti ti-urgent me-2"></i>SOS Management
            @if($activeCount > 0)
                <span class="badge bg-danger ms-2">{{ $activeCount }} Aktif</span>
            @endif
        </h4>
    </div>

    {{-- Active SOS List --}}
    <div class="col-12 mb-4">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Daftar SOS</h5>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Status</th>
                                <th>Severity</th>
                                <th>Pendaki</th>
                                <th>Pesan</th>
                                <th>Lokasi</th>
                                <th>Waktu</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($sosList as $sos)
                            <tr class="sos-{{ $sos->status }}">
                                <td><span class="badge bg-{{ $sos->status === 'active' ? 'danger' : ($sos->status === 'acknowledged' ? 'warning' : ($sos->status === 'dispatched' ? 'primary' : 'success')) }}">{{ ucfirst($sos->status) }}</span></td>
                                <td><span class="severity-{{ $sos->severity }}">{{ strtoupper($sos->severity) }}</span></td>
                                <td>{{ $sos->pendaki?->biodata?->first_name ?? '-' }}</td>
                                <td>{{ Str::limit($sos->message, 50) ?? '-' }}</td>
                                <td><small>{{ $sos->latitude }}, {{ $sos->longitude }}</small></td>
                                <td>{{ $sos->created_at->format('d/m H:i') }}</td>
                                <td>
                                    <a href="{{ route('admin.sos.detail', $sos->id) }}" class="btn btn-sm btn-info"><i class="ti ti-eye"></i></a>
                                    @if($sos->status === 'active')
                                    <form action="{{ route('admin.sos.updateStatus', $sos->id) }}" method="POST" class="d-inline">
                                        @csrf @method('PUT')
                                        <input type="hidden" name="status" value="acknowledged">
                                        <button class="btn btn-sm btn-warning">ACK</button>
                                    </form>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="7" class="text-center text-muted">Tidak ada SOS</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                {{ $sosList->links() }}
            </div>
        </div>
    </div>

    {{-- Pending Disaster Reports --}}
    @if($disasterReports->count() > 0)
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Laporan Bencana (Pending)</h5>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr><th>Potensi</th><th>Lokasi</th><th>Pelapor</th><th>Waktu</th><th>Aksi</th></tr>
                        </thead>
                        <tbody>
                            @foreach($disasterReports as $report)
                            <tr>
                                <td>{{ $report->potensi_bencana }}</td>
                                <td>{{ $report->lokasi }}</td>
                                <td>{{ $report->user?->biodata?->first_name ?? '-' }}</td>
                                <td>{{ $report->created_at->format('d/m H:i') }}</td>
                                <td>
                                    <form action="{{ route('admin.disaster-report.verify', $report->id) }}" method="POST" class="d-inline">
                                        @csrf @method('PUT')
                                        <input type="hidden" name="status" value="verified">
                                        <button class="btn btn-sm btn-success">Verifikasi</button>
                                    </form>
                                    <form action="{{ route('admin.disaster-report.verify', $report->id) }}" method="POST" class="d-inline">
                                        @csrf @method('PUT')
                                        <input type="hidden" name="status" value="rejected">
                                        <button class="btn btn-sm btn-secondary">Tolak</button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection
