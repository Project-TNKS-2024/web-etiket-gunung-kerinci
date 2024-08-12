@extends('etiket.user.template.index')

@section('sub-css')
<style>
    #tiket-booking input {
        border: none;
        background: none;
    }

    #tiket-booking textarea {
        border: none;
        background: none;
    }

    #qrcode_kodebooking img {
        width: -webkit-fill-available !important;
    }
</style>
@endsection


@section('sub-main')
<div class="col py-5 px-4 my-5 my-md-0" style="min-height: 500px; overflow-y: auto;">
    <div class="custom-scrollbar d-flex gap-2 flex-column" id="tiket-booking">
        @foreach ($bookings as $booking)
        <div class="card m-auto" style="max-width: 900px;">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <div class="qrcode_kodebooking" data-qr="{{ $booking->unique_code }}"></div>
                        <!-- <label for="ipt_kodebooking" class="w-100 text-center">Kode Booking</label> -->
                        <input type="text" name="ipt_kodebooking" value="{{ $booking->unique_code }}" id="ipt_kodebooking" class="form-control text-center fw-bold" readonly style="font-size: 18px;">

                        <a href="{{route('homepage.booking.tiket', ['id' => $booking->id])}}" class="d-flex align-items-center gap-2 w-fit btn btn-warning  d-block mx-auto p-1 px-3 rounded shadow " style="cursor: pointer;">
                            <img src="{{asset('assets/icon/tnks/search_alt-dark.svg')}}" /> Detail
                        </a>
                    </div>
                    <div class="col-md-8">
                        <div class="row">
                            <div class="col-12">
                                <span class="badge text-bg-primary" id="ipt_statusbooking" style="margin-left: auto; display: block; width: min-content;">
                                    {{-- Status booking --}}
                                    @if($booking->status_booking == 4)
                                    Sukses
                                    @elseif($booking->status_booking < 4)
                                        Sedang booking
                                        @else
                                        Menunggu Pembayaran
                                        @endif
                                        </span>
                            </div>
                            <div class="col-6">
                                <label for="ipt_namaketua" class="fw-bold">Nama Ketua</label>
                                <input type="text" name="ipt_namaketua" value="{{ $booking->pendakis[0]->nama }}" id="ipt_namaketua" class="form-control" readonly>

                                <label for="ipt_gerbangmasuk" class="fw-bold">Gerbang Masuk</label>
                                <input type="text" name="ipt_gerbangmasuk" value="{{ $booking->gateMasuk->nama }}" id="ipt_gerbangmasuk" class="form-control" readonly>

                                <label for="ipt_cekin" class="fw-bold">Cek In</label>
                                <input type="text" name="ipt_cekin" value="{{ $booking->tanggal_masuk }}" id="ipt_cekin" class="form-control" readonly>

                                <label for="ipt_jumlahanggota" class="fw-bold">Jumlah Anggota</label>
                                <input type="text" name="ipt_jumlahanggota" value="{{ $booking->total_pendaki_wni + $booking->total_pendaki_wna }}" id="ipt_jumlahanggota" class="form-control" readonly>
                            </div>
                            <div class="col-6">
                                <label for="ipt_simaksi" class="fw-bold">Simaksi</label>
                                @if ($booking->simaksi)
                                <input type="text" name="ipt_simaksi" value="Ada" style="color: green;" id="ipt_simaksi" class="form-control" readonly>
                                @else
                                <input type="text" name="ipt_simaksi" value="Tidak" style="color: red;" id="ipt_simaksi" class="form-control" readonly>
                                @endif

                                <label for="ipt_gerbangkeluar" class="fw-bold">Gerbang Keluar</label>
                                <input type="text" name="ipt_gerbangkeluar" value="{{ $booking->gateKeluar->nama }}" id="ipt_gerbangkeluar" class="form-control" readonly>

                                <label for="ipt_cekout" class="fw-bold">Cek Out</label>
                                <input type="text" name="ipt_cekout" value="{{ $booking->tanggal_keluar }}" id="ipt_cekout" class="form-control" readonly>

                                <label for="ipt_kewarganegaraan" class="fw-bold">Kewarganegaraan</label>
                                <div class="row">
                                    <div class="col-6">
                                        <input type="text" name="ipt_kewarganegaraanwni" value="{{ $booking->total_pendaki_wni }} WNI" id="ipt_kewarganegaraanwni" class="form-control" readonly>
                                    </div>
                                    <div class="col-6">
                                        <input type="text" name="ipt_kewarganegaraanwna" value="{{ $booking->total_pendaki_wna }} WNA" id="ipt_kewarganegaraanwna" class="form-control" readonly>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
        @if (count($bookings) == 0)
        <p class="text-center">Belum ada Booking</p>
        @endif
    </div>
</div>
@endsection

@section('js')
<script>
    const sidebarMenu = document.querySelectorAll(".dashboard-sidebar-btn");
    sidebarMenu.forEach((o, i) => {
        sidebarMenu[i].classList.remove("active");
    });
    const password = document.querySelector("#dashboard-riwayat");
    password.classList.add("active");
</script>
<script src="https://cdn.rawgit.com/davidshimjs/qrcodejs/gh-pages/qrcode.min.js"></script>
<script>
    const div_qr = document.querySelectorAll('.qrcode_kodebooking');

    div_qr.forEach(div => {
        // ambl data
        qr = div.dataset['qr'];
        new QRCode(div, {
            text: qr,
            width: 200,
            height: 200
        });
    });
</script>
@endsection