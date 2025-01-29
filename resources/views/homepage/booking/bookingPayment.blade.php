@extends('homepage.template.index')


@section('css')
<style>
    .table tbody tr td {
        padding: 3px 5px;
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
        /* Jarak dari atas layar */
        z-index: 10;
        /* Pastikan elemen tetap di atas elemen lain jika diperlukan */
        background-color: #fff;
        /* Untuk menjaga warna latar belakang */
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        /* Menambahkan bayangan untuk estetika */
        border-radius: 0.5rem;
        /* Opsional: untuk estetika */
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
            @if ($booking->status_pembayaran == 1)
            <span class="badge text-bg-success ">Sukses</span>
            @else
            <span class="badge text-bg-warning ">Belum Bayar</span>
            @endif
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
                                <div
                                    class="bank-info mb-4 d-flex align-items-center justify-content-between bg-light p-3 rounded-3 shadow-sm">
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
                                <div class="qr-container bg-light p-4 rounded-3 text-center">
                                    <img src="{{ asset($qris) }}" class="img-fluid shadow-sm rounded-3"
                                        alt="bukti pembayaran" style="max-width: 100%" />
                                </div>
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

                    <div class="input-group flex-nowrap">
                        <input class="form-control h-100 w-100 " type="file" name="bukti_pembayaran"
                            id="bukti_pembayaran" accept="image/*,.pdf">
                        <button class="input-group-text d-none " type="button" data-id-target="bukti_pembayaran">
                            <i class=" fa-regular fa-eye"></i>
                        </button>
                    </div>

                    <button class="btn btn-primary gk-bg-primary700 my-2 my-md-0" data-bs-toggle="modal"
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

                                    <input class="d-none" type="file" name="bukti_upload" id="bukti_upload">
                                    <input type="hidden" value="{{ asset($item->bukti_pembayaran) }}"
                                        id="bukti_upload_existing">
                                    <button class="btn btn-sm btn-outline-primary" type="button"
                                        data-id-target="bukti_upload">
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
                <div class="card sticky-deskripsi" id="pembayaran">
                    <div class="card-body">
                        <h1 class="fs-5 fw-bold text-center">Tagihan</h1>

                        <h1 class="fs-6 fw-bold ">Tagian Ke:</h1>
                        <p>Email : {{ $booking->user->email }}</p>

                        <h1 class="fs-6 fw-bold ">Deskripsi Pendakian</h1>
                        <table class="table table-borderless table-des">
                            <tr>
                                <td>Nama Ketua</td>
                                <td> : </td>
                                <td>{{ $booking->pendakis[0]->first_name . ' ' . $booking->pendakis[0]->last_name }}
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
                            @php
                            $tagihan = $hitung($pendaki);
                            @endphp
                            <tr>
                                <td>
                                    <div>{{ $pendaki->first_name . ' ' . $pendaki->last_name }}</div>
                                    <div class="d-flex justify-content-between">
                                        <div class="small text-muted">Biaya Masuk</div>
                                    </div>
                                    <div class="d-flex justify-content-between">
                                        <div class="small text-muted">Berkemah</div>
                                    </div>
                                    <div class="d-flex justify-content-between">
                                        <div class="small text-muted">Pendakian</div>
                                    </div>
                                    <div class="d-flex justify-content-between">
                                        <div class="small text-muted">Asuransi</div>
                                    </div>
                                    <div class="d-flex justify-content-between fw-bold">
                                        <div class="small text-muted">Total</div>
                                    </div>
                                </td>
                                <td class="text-end">
                                    <div class="mb-2 align-items-end d-flex" style="flex-direction: column;">
                                        <div class="">
                                            <p></p>
                                        </div>
                                        <div class="d-flex justify-content-between">
                                            <div class="small text-muted">Rp.
                                                {{ number_format($tagihan['masuk']) }}
                                            </div>
                                        </div>
                                        <div class="d-flex justify-content-between">
                                            <div class="small text-muted">Rp.
                                                {{ number_format($tagihan['berkemah']) }}
                                            </div>
                                        </div>
                                        <div class="d-flex justify-content-between">
                                            <div class="small text-muted">Rp.
                                                {{ number_format($tagihan['tracking']) }}
                                            </div>
                                        </div>
                                        <div class="d-flex justify-content-between">
                                            <div class="small text-muted">Rp.
                                                {{ number_format($tagihan['asuransi']) }}
                                            </div>
                                        </div>
                                        <div class="d-flex justify-content-between fw-bold">
                                            <div class="small text-muted">Rp.
                                                {{ number_format($pendaki->tagihan) }}
                                            </div>
                                        </div>
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