@extends('etiket.user.template.index')

@section('sub-css')
    <style>
        .card-tiket .status-tiket {
            font-weight: semibold;
        }

        .card-tiket .status-tiket span {
            font-weight: semibold;
        }

        .card-tiket .tiket-detail input {
            border: none;
            padding-left: 0px;
        }

        .card-tiket .tiket-qr img {
            margin: auto;
        }
    </style>
@endsection


@section('sub-main')
    <div class="card shadow">
        <div class="card-body">
            <div class="container-fluid">
                <h4 class="font-semibold">Riwayat Booking</h4>
                <div class="card-container mb-3" style="overflow-x: auto; white-space: nowrap;">

                    @foreach ($bookings as $booking)
                        <div class="card card-tiket shadow me-3" style="max-width: 400px; display: inline-block;">
                            <div class="card-body">
                                <div class="container">
                                    <div class="status-tiket d-flex gap-2 justify-content-between align-items-center">
                                        Status Tiket
                                            @if ($booking->status_pembayaran)
                                                <span class="btn gk-bg-success200">
                                                    Sudah Bayar
                                                </span>
                                            @else
                                                <span class="btn gk-bg-secondary600">
                                                    Belum Bayar
                                                </span>
                                            @endif
                                    </div>
                                    <div class="row tiket-detail mt-3">
                                        <div class="col-6">
                                            <label for="ipt_namaketua" class="fw-bold">Nama Ketua</label>
                                            <input type="text" name="ipt_namaketua"
                                                value="{{ isset($booking->pendakis[0]->first_name) ? $booking->pendakis[0]->first_name." ".$booking->pendakis[0]->last_name : '' }}"
                                                id="ipt_namaketua" class="form-control" readonly>

                                            <label for="ipt_gerbangmasuk" class="fw-bold">Gerbang Masuk</label>
                                            <input type="text" name="ipt_gerbangmasuk"
                                                value="{{ $booking->gateMasuk->nama }}" id="ipt_gerbangmasuk"
                                                class="form-control" readonly>

                                            <label for="ipt_cekin" class="fw-bold">Cek In</label>
                                            <input type="text" name="ipt_cekin" value="{{ $booking->tanggal_masuk }}"
                                                id="ipt_cekin" class="form-control" readonly>

                                            <label for="ipt_jumlahanggota" class="fw-bold">Jumlah Anggota</label>
                                            <input type="text" name="ipt_jumlahanggota"
                                                value="{{ $booking->total_pendaki_wni + $booking->total_pendaki_wna }}"
                                                id="ipt_jumlahanggota" class="form-control" readonly>
                                        </div>
                                        <div class="col-6">
                                            <label for="ipt_simaksi" class="fw-bold">Simaksi</label>
                                            @if ($booking->simaksi)
                                                <input type="text" name="ipt_simaksi" value="Ada"
                                                    style="color: green;" id="ipt_simaksi" class="form-control" readonly>
                                            @else
                                                <input type="text" name="ipt_simaksi" value="Tidak" style="color: red;"
                                                    id="ipt_simaksi" class="form-control" readonly>
                                            @endif

                                            <label for="ipt_gerbangkeluar" class="fw-bold">Gerbang Keluar</label>
                                            <input type="text" name="ipt_gerbangkeluar"
                                                value="{{ $booking->gateKeluar->nama }}" id="ipt_gerbangkeluar"
                                                class="form-control" readonly>

                                            <label for="ipt_cekout" class="fw-bold">Cek Out</label>
                                            <input type="text" name="ipt_cekout" value="{{ $booking->tanggal_keluar }}"
                                                id="ipt_cekout" class="form-control" readonly>

                                            <label for="ipt_kewarganegaraan" class="fw-bold">Kewarganegaraan</label>
                                            <div class="row">
                                                <div class="col-6">
                                                    <input type="text" name="ipt_kewarganegaraanwni"
                                                        value="{{ $booking->total_pendaki_wni }} WNI"
                                                        id="ipt_kewarganegaraanwni" class="form-control" readonly>
                                                </div>
                                                <div class="col-6">
                                                    <input type="text" name="ipt_kewarganegaraanwna"
                                                        value="{{ $booking->total_pendaki_wna }} WNA"
                                                        id="ipt_kewarganegaraanwna" class="form-control" readonly>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="text-center tiket-qr mt-3">
                                        <div class="qrcode_kodebooking mx-auto"
                                            data-qr="{{ $booking->unique_code ?? '___ ___' }}"></div>
                                        <p class="mb-0 mt-1">Kode Boooking</p>
                                        <h4>{{ $booking->unique_code ?? '___ ___' }}</h4>
                                        @if ($booking->status_pembayaran == false)
                                            <a href="{{route('homepage.booking.payment', ['id' => $booking->id])}}" class="btn w-100 btn-gl-primary text-white">
                                                Bayar
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                    @if (count($bookings) == 0)
                        <div>
                            <p class="gk-text-neutrals700 text-center">Tidak ada tiket yang aktif</p>
                        </div>
                    @endif

                </div>
            </div>
        </div>
    </div>
@endsection




@section('js')
    <script src="https://cdn.rawgit.com/davidshimjs/qrcodejs/gh-pages/qrcode.min.js"></script>
    <script>
        const qrcodes = document.querySelectorAll('.qrcode_kodebooking');
        qrcodes.forEach(e => {
            qr = e.dataset['qr'];
            new QRCode(e, {
                text: qr,
                width: 200,
                height: 200
            });
        });
    </script>
@endsection
