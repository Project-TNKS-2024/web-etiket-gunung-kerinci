@extends('homepage.template.index')


@section('css')
<style>
    .table tbody tr td {
        padding: 1px 5px;
    }

    .table {
        --bs-table-bg: transparent;
    }

    .table-des td:first-child {
        margin-right: 10px;
        /* Menambahkan margin kanan */
        padding-right: 10px;
        /* Mengimbangi ruang dengan padding */
    }

    .bank-info {
        border: 1px solid #e0e0e0;
        background: #f9f9f9;
    }

    .bank-info div {
        white-space: nowrap;
    }

    .bank-info .bg-primary {
        background-color: #0056b3 !important;
        /* Warna biru lembut */
        border-radius: 20px;
        padding: 5px 15px;
    }

    .sticky-deskripsi {
        position: sticky;
        top: 20px;
        z-index: 10;
    }
</style>
@endsection


@section('main')
@include('homepage.template.header', [
'title' => 'Pendakian Gunung Kerinci',
'caption' => 'Detail Booking',
])

<div class="container my-5">
    @include('homepage.booking.booking-nav', ['step' => 2])

    <div id="booking-detail">
        <p class=" text-center">
            <span class="fs-4 fw-bold">
                Pembayaran Pemesanan
            </span>

        </p>


        <div class="row mt-3">
            <div class="col-12 col-md-6">
                <h1 class="fs-5 fw-bold">Metode Pembayaran</h1>

                <!-- Pilihan Metode Pembayaran -->
                <div class="accordion mb-2" id="paymentMethod">
                    <!-- Transfer Antar Bank -->
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="bankTransferHeading">
                            <button class="accordion-button fw-semibold" type="button" data-bs-toggle="collapse"
                                data-bs-target="#bankTransferCollapse" aria-expanded="true"
                                aria-controls="bankTransferCollapse">
                                Transfer Antar Bank
                            </button>
                        </h2>
                        <div id="bankTransferCollapse" class="accordion-collapse collapse show"
                            aria-labelledby="bankTransferHeading" data-bs-parent="#paymentMethod">
                            <div class="accordion-body">
                                <div class="bank-info mb-4 d-flex align-items-center justify-content-between bg-light p-3 rounded-3 shadow-sm">
                                    <!-- Informasi Rekening -->
                                    <div class="d-flex flex-column">
                                        <span class="fs-5 fw-semibold text-dark">7338 0102 6542 535</span>
                                        <span class="fs-6 text-secondary">a.n PT. Gunung Kerinci</span>
                                    </div>
                                    <!-- Nama Bank -->
                                    <div
                                        class="fw-bold gk-bg-primary700 text-white px-3 py-1 text-center rounded-pill fs-6 d-flex align-items-center">
                                        <i class="bi bi-bank me-2"></i>BRI
                                    </div>
                                </div>
                                <p class="mb-0">Cara Pembayaran</p>
                                <ul>
                                    <li>Datangi ATM terdekat</li>
                                    <li>Masukkan kartu ATM BRI</li>
                                    <li>Pilih bahasa selama transaksi</li>
                                    <li>Masukkan pin kartu ATM</li>
                                    <li>Pilih menu Transfer Lainnya dan klik Tranfer</li>
                                    <li>Masukkan kode bank BRI 002 diikuti nomor rekening BRI yang dituju, misal: 002(4455XXXXXXXX)</li>
                                    <li>Masukkan nominal yang ingin ditransfer lalu klik Benar atau Ya</li>
                                    <li>Lanjut pilih jenis rekening Tabungan atau Giro</li>
                                    <li>Transaksi segera diproses</li>
                                    <li>Keluar struk atau info di layar jika transfer berhasil.</li>
                                    <li>Foto bukti pembayaran dan upload bukti pembayaran di bawah.</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- Scan QR -->
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="qrScanHeading">
                            <button class="accordion-button collapsed fw-semibold" type="button"
                                data-bs-toggle="collapse" data-bs-target="#qrScanCollapse" aria-expanded="false"
                                aria-controls="qrScanCollapse">
                                Scan QR
                            </button>
                        </h2>
                        <div id="qrScanCollapse" class="accordion-collapse collapse" aria-labelledby="qrScanHeading"
                            data-bs-parent="#paymentMethod">
                            <div class="accordion-body">
                                <div class="qr-container bg-light p-4 rounded-3 text-center mb-2">
                                    <img src="{{ asset($qris) }}" class="img-fluid shadow-sm rounded-3"
                                        alt="bukti pembayaran" style="max-width: 100%" />
                                </div>
                                <p class="mb-0">Cara Pembayaran</p>
                                <ul>
                                    <li>lorem</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                @if ($booking->status_booking ==3)
                <h1 class="fs-5 fw-bold">Upload Bukti Pembayaran</h1>
                <form class="mb-3 d-block d-sm-flex gap-2" action="{{ route('homepage.booking.addBuktiPembayaran') }}"
                    method="post" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="id" value="{{ $booking->id }}">

                    <select class="form-select" name="metode">
                        <option selected disabled>Metode</option>
                        <option value="transfer">Transfer Bank</option>
                        <option value="scan">Scan QR</option>
                    </select>

                    <div class="input-group flex-nowrap">
                        <input class="form-control h-100" type="file" name="bukti_pembayaran"
                            id="bukti_pembayaran" accept="image/*,.pdf">
                        <button class="input-group-text d-none " type="button" data-id-target="bukti_pembayaran">
                            <i class=" fa-regular fa-eye"></i>
                        </button>
                    </div>

                    <button class="btn btn-primary my-2 my-md-0" data-bs-toggle="modal"
                        data-bs-target="#addBuktiModal">
                        Upload
                    </button>
                </form>
                @endif

                <h1 class="fs-5 fw-bold">Riwayat Pembayaran</h1>
                @if (count($pembayaran) > 0)
                <div class="table-responsive">
                    <table class="table table-bordered table-hover align-middle bg-white">
                        <thead class="text-center bg-primary-subtle text-white">
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">Tanggal</th>
                                <th scope="col">Status</th>
                                <th scope="col">Keterangan</th>
                                <th scope="col">Bukti</th>
                                <th scope="col">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($pembayaran as $key => $item)
                            <tr>
                                <td class="text-center fw-bold">{{ $key + 1 }}</td>
                                <td>{{ $item->created_at->format('d M Y H:i') }}</td>
                                <td class="text-center">
                                    @if ($item->status == 'pending')
                                    <span class="badge bg-warning text-dark px-3 py-2">Pending</span>
                                    @elseif($item->status == 'success')
                                    <span class="badge bg-success px-3 py-2">Disetujui</span>
                                    @else
                                    <span class="badge bg-danger px-3 py-2">Ditolak</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    {{ $item->status == 'pending' ? 'Menunggu Validasi' : ($item->status == 'approved' && $item->keterangan == null ? 'Disetujui' : $item->keterangan) }}
                                </td>
                                <td class="text-center">

                                    <input class="d-none" type="file" name="bukti_upload" id="bukti_upload_{{ $key + 1 }}">
                                    <input type="hidden" value="{{ asset($item->bukti_pembayaran) }}"
                                        id="bukti_upload_{{ $key + 1 }}_existing">
                                    <button class="btn btn-sm btn-outline-primary" type="button"
                                        data-id-target="bukti_upload_{{ $key + 1 }}">
                                        <i class=" fa-regular fa-eye"></i>
                                    </button>
                                </td>
                                @if ($booking->status_booking <=3)
                                    <td class="text-center">
                                    <form action="{{ route('homepage.booking.payment.delete') }}" method="POST"
                                        class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <input type="hidden" name="id" value="{{ $booking->id }}">
                                        <input type="hidden" name="id_pembayaran"
                                            value="{{ $item->id }}">
                                        <button type="submit" class="btn btn-sm btn-outline-danger"
                                            onclick="return confirm('Apakah anda yakin ingin menghapus bukti pembayaran ini?')">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                    </td>
                                    @endif
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <p class="text-muted">Belum ada riwayat pengajuan</p>
                @endif
            </div>

            <!-- deskripsi tagihan -->
            <div class="col-12 col-md-6">
                <h1 class="fs-5 fw-bold">Detail Tagihan</h1>
                <div class=" sticky-deskripsi">
                    <div class="card" id="pembayaran">
                        <div class="card-body">
                            <p>Status :
                                @if ($booking->status_pembayaran == 1)
                                <span class="badge text-bg-success ">Sukses</span>
                                @else
                                <span class="badge text-bg-warning ">Belum Bayar</span>
                                @endif
                            </p>

                            <h1 class="fs-6 fw-bold ">Data Pemesan:</h1>
                            <p class="mb-0">Email : {{ $booking->user->email }}</p>
                            <p class="mb-0">Nama : {{ $booking->user->biodata->fullName }}</p>


                            <h1 class="fs-6 fw-bold ">Detail Pemesanan Tiket</h1>
                            <table class="table table-borderless table-des">
                                <tr>
                                    <td>Destinasi</td>
                                    <td> : </td>
                                    <td>{{ $booking->gateMasuk->destinasi->nama }}
                                    </td>
                                </tr>
                                <tr>
                                    <td>Gate Masuk</td>
                                    <td> : </td>
                                    <td>{{ $booking->gateMasuk->nama }} </td>
                                </tr>
                                <tr>
                                    <td>Gate Keluar</td>
                                    <td> : </td>
                                    <td>{{ $booking->gateKeluar->nama }} </td>
                                </tr>
                                <tr>
                                    <td>Tanggal Pendakian</td>
                                    <td> : </td>
                                    <td>{{ $booking->tanggal_masuk }} </td>
                                </tr>
                                <tr>
                                    <td>Tanggal Keluar</td>
                                    <td> : </td>
                                    <td>{{ $booking->tanggal_keluar }}</td>
                                </tr>
                                <tr>
                                    <td>Total Pendaki</td>
                                    <td> : </td>
                                    <td>{{ $booking->total_pendaki_wni }} WNI dan {{ $booking->total_pendaki_wna }} WNA
                                    </td>
                                </tr>
                            </table>

                            <h1 class="fs-6 fw-bold ">List Tagihan Tiket</h1>
                            <table class="table mb-0 bg-transparent">
                                <tr class="fw-semibold">
                                    <td>Nama Pendaki</td>
                                    <td class="text-end">Tagihan</td>
                                </tr>
                                @foreach ($booking->pendakis as $pendaki)
                                <tr>
                                    <td>
                                        <div>{{ $pendaki->first_name . ' ' . $pendaki->last_name }}</div>
                                    </td>
                                    <td class="text-end">
                                        <div>Rp.
                                            {{ number_format($pendaki->tagihan) }}
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                                <tr>
                                    <td class="fw-semibold text-end">Total : </td>
                                    <td class="text-end">Rp. {{ number_format($booking->total_pembayaran, 0, ',', '.') }}
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    <a href="{{route('homepage.booking.struk', ['id' => $booking->id] )}}" class="btn btn-primary d-block mt-3 w-0">Lihat Detail Pemesanan</a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12 col-md-4">
            @if ($booking->status_booking <=3)
                <a type="submit" class="btn btn-warning w-100 fw-bold mt-3" href="{{ route('homepage.booking.cancel', ['id' => $booking->id]) }}">Batalkan Pembayaran</a>
                @endif
        </div>
        <div class="col-12 col-md-4"></div>
        <div class="col-12 col-md-4 text-end">
            @if ($booking->status_booking >= 4)
            <a type="submit" class="btn btn-success w-100 fw-bold mt-3" href="{{ route('dashboard.tiket', ['id' => $booking->id]) }}">Lihat Tiket</a>
            @endif
        </div>
    </div>
</div>

@endsection
@section('js')
<!-- script modal show file -->
@include('homepage.template.modal-prefiewFile')
@endsection