@extends('homepage.template.index')


@section('css')
    <style>
        .header-bg {
            position: relative;
            background: url("{{ asset('assets/img/bg/title-header-bg.png') }}") no-repeat;
            background-size: cover;
            background-position: 50% 50%;
            color: white;
        }


        .header-bg::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.6);
            /* Adjust the alpha value for the desired opacity */
            z-index: 1;
        }

        .header-content {
            position: relative;
            z-index: 2;
        }

        .border-between {
            border-top: 2px solid white;
            width: 50px;
            margin: 20px 0;
        }

        /* payment detail */
        .icon-status-payment {
            height: 100px;
            background-size: cover;
        }

        .icon-status-payment.success {
            width: 100px;
            background-image: url("{{ asset('assets/img/dashboard/Successmark.png') }}");
        }

        /* Custom style for the table */
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
    @include('homepage.template.header', [
        'title' => 'Pendakian Gunung Kerinci',
        'caption' => 'Pembayaran',
    ])

    <script></script>
    <div class="container my-5">
        <div class="flex-wrap d-flex row">
            <!-- First Column -->
            <div class="col-12 col-md-4 mb-3">
                <div class="p-3 text-center">
                    <header class="text-start">QR Pembayaran</header>
                    <div class="text-start text-muted small">**Masukkan nominal yang sesuai</div>
                    <img src="{{ asset('assets/img/qris-dummy.png') }}" class="img-fluid" alt="bukti pembayaran" />
                    <div class="mt-3">
                        <a href="{{ asset('assets/img/qris-dummy.png') }}" download class="btn btn-primary">Unduh Kode QR</a>
                    </div>
                    <h3 class="h-2 mt-3">Rp {{ number_format($booking->total_pembayaran, 0, ',', '.') }}</h3>
                </div>
            </div>
            <!-- Second Column -->
            <div class="col-12 col-md-8 mb-3">
                <div class="p-3" id="">
                    <h4>Informasi Booking</h4>
                    <div class="row">
                        <div class="col">
                            <h6 class="fw-bold my-0">Nama Ketua</h6>
                            <p>{{ $pendakis[0]->first_name }} {{ $pendakis[0]->last_name }}</p>
                            <h6 class="fw-bold my-0">Gerbang Masuk</h6>
                            <p>{{ $booking->gateMasuk->nama }}</p>
                            <h6 class="fw-bold my-0">Check In</h6>
                            <p>{{ $booking->tanggal_masuk }}</p>
                            <h6 class="fw-bold my-0">Jumlah Anggota</h6>
                            <p>5 orang</p>
                        </div>
                        <div class="col">
                            <h6 class="fw-bold my-0">SIMAKSI</h6>
                            <p>
                                @if ($booking->lampiran_simaksi == null)
                                    <span class="c-red">Tidak</span>
                                @else
                                    <span class="c-green">Ya</span>
                                @endif
                            </p>
                            <h6 class="fw-bold my-0">Gerbang Keluar</h6>
                            <p>{{ $booking->gateKeluar->nama }}</p>
                            <h6 class="fw-bold my-0">Check out</h6>
                            <p>{{ $booking->tanggal_keluar }}</p>
                            <h6 class="fw-bold my-0">Kewarganegaraan</h6>
                            <div class="d-flex gap-3">
                                <div class="">
                                    <p>{{ $booking->total_pendaki_wni }} WNI</p>
                                </div>
                                <div class="">
                                    <p>{{ $booking->total_pendaki_wna }} WNA</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="mb-5">
            <h4>Transfer antar Bank</h4>
            <div class="d-flex flex-column gap-3">
                <div class="d-flex gap-3">
                    <div class="fw-bold bg-primary text-white px-3 py-1 text-center rounded fs-5 d-flex align-items-center justify-content-center"
                        style="width: fit-content">BRI</div>
                    <div class="d-flex flex-wrap d-flex align-items-center justify-content-start gap-2">
                        <span class="fs-3">7338 0102 6542 535</span>
                        <span class="fs-5">a.n PT. Gunung Kerinci</span>
                    </div>
                </div>
                <div class="d-flex gap-3">
                    <div class="fw-bold bg-primary text-white px-3 py-1 text-center rounded fs-5 d-flex align-items-center justify-content-center"
                        style="width: fit-content">BRI</div>
                    <div class="d-flex flex-wrap d-flex align-items-center justify-content-start gap-2">
                        <span class="fs-3">7338 0102 6542 535</span>
                        <span class="fs-5">a.n PT. Gunung Kerinci</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="mb-5">
            <div class="d-block d-md-flex justify-content-between align-items-center mb-1">
                <h4>Riwayat Pengajuan Bukti Pembayaran</h4>
            </div>

            <form class="mb-3 d-block d-sm-flex gap-2"
                action="{{ route('homepage.booking.addBuktiPembayaran', $booking->id) }}" method="post"
                enctype="multipart/form-data">
                @csrf
                <input type="file" class="form-control h-100 w-100" id="bukti_pembayaran" name="bukti_pembayaran" />
                <button class="btn btn-gl-primary my-2 my-md-0" style="width: 100%" data-bs-toggle="modal"
                    data-bs-target="#addBuktiModal">
                    Tambah Bukti Pembayaran
                </button>
            </form>


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
                            @foreach ($pengajuan as $key => $item)
                                <tr>
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
                                    <td class="text-center">{{ ($item->status == "pending") ? "Menunggu Validasi" : ($item->status == "approved" && $item->keterangan == null ? "Disetujui" : $item->keterangan) }}</td>
                                    <td class="text-center">
                                        <a href="{{ asset($item->bukti_pembayaran) }}" class="btn btn-sm btn-primary" target="_blank">
                                            <i class="bi bi-eye"></i> Lihat Bukti
                                        </a>
                                    </td>
                                    <td class="text-center">
                                        <form action="{{ route('homepage.booking.payment.delete', ['id' => $booking->id, 'pengajuan_id' => $item->id]) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Apakah anda yakin ingin menghapus bukti pembayaran ini?')">
                                                <i class="bi bi-trash"></i> Hapus
                                            </button>
                                        </form>
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
@endsection
@section('js')
    <script>
        console.log(@json($pendakis));
    </script>
@endsection
