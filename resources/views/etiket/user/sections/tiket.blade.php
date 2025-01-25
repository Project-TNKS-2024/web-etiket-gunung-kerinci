<div class="card card-tiket shadow me-3" style="max-width: 400px; display: inline-block;">
    <div class="card-body">
        <div class="container">
            <div class="status-tiket d-flex gap-2 justify-content-between align-items-center">
                Status Tiket
                    @if ($booking->pembayaran->contains('status', 'approved'))
                        <span class="btn gk-bg-success200">
                            Sudah Bayar
                        </span>
                    @else
                        <span class="btn gk-bg-secondary600">
                            Belum Bayar
                        </span>
                    @endif
            </div>
            
            <label for="ipt_namaketua" class="fw-bold">Nama Ketua</label>
            <input type="text" name="ipt_namaketua"
                value="{{ isset($booking->pendakis[0]->first_name) ? $booking->pendakis[0]->first_name." ".$booking->pendakis[0]->last_name : '' }}"
                id="ipt_namaketua" class="form-control" readonly>
            <div class="row tiket-detail mt-3">
                <div class="col-6">


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
                @if ($booking->pembayaran->contains('status', 'approved'))
                <a href="#" class="btn w-100 btn-gl-primary text-white">
                    Lihat Tiket
                </a>
                @else
                    <a href="{{route('homepage.booking.payment', ['id' => $booking->id])}}" class="btn w-100 btn-gl-primary text-white">
                        Bayar
                    </a>
                @endif
            </div>
        </div>
    </div>
</div>