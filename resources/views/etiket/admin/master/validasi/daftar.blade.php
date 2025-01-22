@extends('etiket.admin.template.index')

@section('css')
    <style>
        .table-no-side-border {
            border-left: none;
            border-right: none;
        }

        .table-no-side-border th,
        .table-no-side-border td {
            border-left: none;
            border-right: none;
        }
    </style>
@endsection

@section('main')
    <div class="card">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <label class="text-2xl font-bold gk-text-base-black mb-2">Validasi Pembayaran</label>
            </div>
            <div class="d-flex mb-3 align-items-end align-items-start gap-3 row">
                <!-- Dropdown for Status -->
                <div class="dropdown col-12 col-md-4">
                    <button class="btn border border-2 dropdown-toggle" type="button" id="dropdownStatus"
                        data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="ti ti-filter"></i>
                        @if ($status == 'all')
                            Semua Status
                        @elseif($status == 'approved')
                            Disetujui
                        @elseif($status == 'rejected')
                            Ditolak
                        @elseif($status == 'pending')
                            Belum Divalidasi
                        @endif
                    </button>
                    <ul class="dropdown-menu" aria-labelledby="dropdownStatus">
                        <li><a href="{{ route('admin.master.validasi.daftar.filtered', ['start_date' => $start_date ?? 'all', 'end_date' => $end_date ?? 'all', 'status' => 'all']) }}"
                                class="dropdown-item" data-status="all">Semua Status</a></li>
                        <li><a href="{{ route('admin.master.validasi.daftar.filtered', ['start_date' => $start_date ?? 'all', 'end_date' => $end_date ?? 'all', 'status' => 'approved']) }}"
                                class="dropdown-item" data-status="approved">Disetujui</a></li>
                        <li><a href="{{ route('admin.master.validasi.daftar.filtered', ['start_date' => $start_date ?? 'all', 'end_date' => $end_date ?? 'all', 'status' => 'rejected']) }}"
                                class="dropdown-item" data-status="rejected">Ditolak</a></li>
                        <li><a href="{{ route('admin.master.validasi.daftar.filtered', ['start_date' => $start_date ?? 'all', 'end_date' => $end_date ?? 'all', 'status' => 'pending']) }}"
                                class="dropdown-item" data-status="pending">Belum Divalidasi</a></li>
                    </ul>
                </div>

                <!-- Date Range Filter Form -->
                <div class="col-12 col-md-7 justify-content-start">
                    <form class="d-flex gap-2 row" action="{{ route('admin.master.validasi.daftar.filtered') }}" method="get">
                        <div class="form-group col">
                            <label for="startDate">Dari Tanggal</label>
                            <input type="datetime-local" class="form-control" id="startDate" name="start_date"
                                value="{{ $start_date != 'all' && $end_date != null ? Carbon\Carbon::parse($start_date)->format('Y-m-d\TH:i') : Carbon\Carbon::parse(now())->format('Y-m-d 00:00:00') }}">
                        </div>
                        <div class="form-group col">
                            <label for="endDate">Sampai Tanggal</label>
                            <input type="datetime-local" class="form-control" id="endDate" name="end_date"
                                value="{{ $end_date != 'all' && $end_date != null ? Carbon\Carbon::parse($end_date)->format('Y-m-d\TH:i') : Carbon\Carbon::parse(now())->format('Y-m-d 23:59') }}">
                        </div>
                        <div class="form-group d-flex align-items-end col">
                            <button class="btn btn-primary " id="btnFilter">
                                <i class="ti ti-filter "></i>
                                <span class="" style="">Filter</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            @if (count($pembayaran) > 0)
                <div class="table-responsive my-4">
                    <table class="table table-bordered table-hover align-middle table-no-side-border">
                        <thead class="text-center ">
                            <tr>
                                <th scope="col">No</th>
                                <th scope="col">Tanggal</th>
                                <th scope="col">Status</th>
                                <th scope="col">Keterangan</th>
                                <th scope="col">Bukti</th>
                                <th scope="col">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($pembayaran as $key => $item)
                                <tr class="{{ $loop->odd ? 'gk-bg-base-white' : '' }}">
                                    <td class="text-center">{{ $key + 1 }}</td>
                                    <td class="">{{ $item->created_at->format('d M Y H:i') }}</td>
                                    <td class="text-center ">
                                        @if ($item->status == 'pending')
                                            <span class="badge bg-warning text-dark">Pending</span>
                                        @elseif($item->status == 'approved')
                                            <span class="badge bg-success">Disetujui</span>
                                        @else
                                            <span class="badge bg-danger">Ditolak</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        {{ $item->status == 'pending' ? 'Menunggu Validasi' : $item->keterangan }}</td>
                                    <td class="text-center">
                                        <a href="{{ asset($item->bukti) }}" class="btn btn-sm btn-primary" target="_blank">
                                            <i class="bi bi-eye"></i> Lihat Bukti
                                        </a>
                                    </td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-sm btn-primary btnValidasi"
                                            data-id="{{ $item->id }}" data-bs-toggle="modal"
                                            data-img="{{ $item->bukti_pembayaran }}" data-bs-target="#modalValidasi"
                                            onclick="document.getElementById('pengajuanId').value = {{ $item->id }}; document.getElementById('modal-img').src = '{{ asset($item->bukti) }}';">
                                            <img src="{{ asset('assets/icon/tnks/edit_fill-light.svg') }}"
                                                width="20" />
                                            Validasi
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="text-muted">Belum ada riwayat pengajuan</p>
            @endif
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="modalValidasi" tabindex="-1" aria-labelledby="modalValidasiLabel" aria-hidden="true">
        <div class="modal-dialog w-100 align-items-center justify-content-center m-auto p-5"
            style="display: flex; position: static; ">
            <div class="modal-content " style="width: fit-content;">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalValidasiLabel">Validasi Pengajuan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body d-block d-md-flex" style="width: fit-content;">
                    <form id="formValidasi" class="" method="post" style="width: 1500px; max-width: 300px;"
                        action='{{ route('admin.master.validasi.updateAction') }}'>
                        @csrf
                        <input type="hidden" id="pengajuanId" name="pengajuanId">
                        <div class="form-group mb-3">
                            <label for="status">Status</label>
                            <div class="dropdown">
                                <button class="btn btn-primary dropdown-toggle" type="button" id="statusDropdown"
                                    data-bs-toggle="dropdown" aria-expanded="false">
                                    Pilih Status
                                </button>
                                <ul class="dropdown-menu border col-12" aria-labelledby="statusDropdown">
                                    <li><a class="dropdown-item" href="#" data-value="approved"
                                            onclick="selectStatus(this)">Setujui</a></li>
                                    <li><a class="dropdown-item" href="#" data-value="rejected"
                                            onclick="selectStatus(this)">Tolak</a></li>
                                </ul>
                                <input type="hidden" id="status" name="status" required>
                                <input type="hidden" id="selectedText" name="status">
                                <script>
                                    function selectStatus(element) {
                                        const value = element.getAttribute('data-value');
                                        const text = element.textContent;
                                        document.getElementById('statusDropdown').textContent = text;
                                        document.getElementById('status').value = value;
                                        document.getElementById('selectedText').value = value;
                                    }
                                </script>
                            </div>
                        </div>
                        <div class="form-group mb-3">
                            <label for="keterangan">Keterangan</label>
                            <textarea class="form-control" id="keterangan" name="keterangan" rows="3"
                                placeholder="Masukkan keterangan..."></textarea>
                        </div>
                    </form>
                    <div class="d-flex justify-content-center " style="width: fit-content;">
                        <img id="modal-img w-100" class="" style="max-width:400px"
                            src="{{ asset('assets/img/qris-dummy.png') }}" />
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary" form="formValidasi">Simpan</button>
                </div>
            </div>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const modalValidasi = new bootstrap.Modal(document.getElementById('modalValidasi'));
            const btnValidasiElements = document.querySelectorAll('.btnValidasi');

            btnValidasiElements.forEach(btn => {
                btn.addEventListener('click', function() {
                    const pengajuanId = this.getAttribute('data-id');
                    const pengajuanImg = this.getAttribute('data-img');
                    document.getElementById('pengajuanId').value = pengajuanId;
                    document.getElementById('modal-img').src = "{{ url('/') }}/" +
                        pengajuanImg;
                    modalValidasi.show();
                });
            });
        });
    </script>
@endsection

@section('js')
@endsection
