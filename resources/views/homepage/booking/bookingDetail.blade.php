@extends('homepage.template.index')


@section('css')
    <style>
        /* style untuk form detail booking */
        .table tbody tr td {
            padding: 3px 5px;
        }

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

        /* booking detail */
        #booking-detail h1 {
            font-size: 20px;
            text-align: center;
            font-weight: bold;
        }

        #booking-detail #formulir h4 {
            font-size: 16px;
            font-weight: bold;
        }

        #booking-detail h4,
        #booking-detail p {
            color: rgba(52, 64, 84, 1);
        }

        .c-blue {
            color: blue;
        }

        .c-red {
            color: red;
        }

        .c-green {
            color: #00e221;
        }

        #booking-detail #pembayaran {
            background-color: whitesmoke;
        }

        #booking-detail #pembayaran h4 {
            font-size: 16px;
            font-weight: bold;
            text-align: center;
        }

        #booking-detail #pembayaran p {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        #booking-detail p {
            margin-bottom: 8px;
        }

        #booking-detail #pembayaran .span {
            font-size: 11px;
        }

        .centered {
            display: flex;
            justify-content: center;
        }

        .pilihmetodepembayaran {
            /* margin-top: 5px; */
            /* background-color: #e0ffff; */
            padding: 5px;
            border-radius: 10px;
            /* border: 1px solid #9adbdb; */
        }

        .pilihmetodepembayaran input {
            margin-left: 5px !important;
            margin-right: 10px;
            border: 1px solid #9adbdb;
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


        <script>
            console.log("Di bawah");
            console.log(@json($hitung($pendakis[0])))
        </script>

        <div class="card border-0 shadow my-4">
            <div class="card-body px-4 px-md-5 pb-4">
                <div id="booking-detail">
                    <h1>Detail Pemesanan</h1>
                    <div class="row mt-3">
                        <div class="col-12 col-md-6" id="formulir">
                            <div class="row">
                                <h4 class="col">Nama Ketua</h4>
                                <p class="col">{{ $pendakis[0]->first_name }} {{ $pendakis[0]->last_name }}</p>
                            </div>
                            <div class="row">
                                <div class="col">
                                    <h4>Gerbang Masuk</h4>
                                    <p>{{ $booking->gateMasuk->nama }}</p>
                                    <h4>Check In</h4>
                                    <p>{{ $booking->tanggal_masuk }}</p>
                                    <h4>Jumlah Anggota</h4>
                                    <p>{{ count($pendakis) }} orang</p>
                                </div>
                                <div class="col">

                                    {{-- <h4>SIMAKSI</h4> --}}
                                    {{-- <p>
                                        @if ($booking->lampiran_simaksi == null)
                                            <span class="c-red">Tidak</span>
                                        @else
                                            <span class="c-green">Ya</span>
                                        @endif
                                    </p> --}}
                                    <h4>Gerbang Keluar</h4>
                                    <p>{{ $booking->gateKeluar->nama }}</p>
                                    <h4>Check out</h4>
                                    <p>{{ $booking->tanggal_keluar }}</p>
                                    <h4>Kewarganegaraan</h4>
                                    <div class="row">
                                        <div class="col">
                                            <p>{{ $booking->total_pendaki_wni }} WNI</p>
                                        </div>
                                        <div class="col">
                                            <p>{{ $booking->total_pendaki_wna }} WNA</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-md-6">
                            <div class="card" id="pembayaran">
                                <div class="card-body">
                                    <h4>Total Pembayaran</h4>
                                    @foreach ($pendakis as $pen)
                                        @php
                                            $tagihan = $hitung($pen)
                                        @endphp
                                        <div class="mb-2">
                                            <div class="">{{ $pen->first_name }} {{ $pen->last_name }}</div>
                                            <div class="d-flex justify-content-between">
                                                <div class="small text-muted" style="">Biaya Masuk</div>
                                                <div class="small text-muted">Rp. {{ number_format($tagihan['masuk']) }}</div>
                                            </div>
                                            <div class="d-flex justify-content-between">
                                                <div class="small text-muted" style="">Berkemah</div>
                                                <div class="small text-muted">Rp. {{ number_format($tagihan['berkemah']) }}</div>
                                            </div>
                                            <div class="d-flex justify-content-between">
                                                <div class="small text-muted" style="">Pendakian</div>
                                                <div class="small text-muted">Rp. {{ number_format($tagihan['tracking']) }}</div>
                                            </div>
                                            <div class="d-flex justify-content-between">
                                                <div class="small text-muted" style="">Asuransi</div>
                                                <div class="small text-muted">Rp. {{ number_format($tagihan['asuransi']) }}</div>
                                            </div>
                                            <div class="d-flex justify-content-between fw-bold">
                                                <div class="small text-muted" style="">Total</div>
                                                <div class="small text-muted">Rp. {{ number_format($pen->tagihan) }}</div>
                                            </div>
                                        </div>
                                    @endforeach
                                    <p class="fw-bold c-blue">Total <span class="float-right">Rp.
                                            {{ number_format($booking->total_pembayaran) }}</span></p>
                                    <p class="span">*{{ $booking->total_hari }} hari {{ $booking->total_hari - 1 }}
                                        malam
                                        ({{ $booking->total_hari }}D{{ $booking->total_hari - 1 }}M)</p>
                                </div>
                            </div>
                            <div class="text-start form-group my-2">
                                <a href="{{ route('homepage.booking.payment', ['id' => $booking->id]) }}"
                                    class="btn btn-gl-primary ">Lakukan Pembayaran</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow">
            <div class="card-body px-4 px-md-5 pb-4q">
                <div class="mt-3">
                    <h1 class="fs-5 fw-bold">Booking {{ $booking->gateMasuk->destinasi->nama }}</h1>
                    <div class="row">
                        <div class="col-12 col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td>Nama Ketua</td>
                                    <td> : </td>
                                    <td>{{ $pendakis[0]->first_name . ' ' . $pendakis[0]->last_name }}</td>
                                </tr>
                                <tr>
                                    <td>Gerbang Masuk</td>
                                    <td> : </td>
                                    <td>{{ $booking->gateMasuk->nama }}</td>
                                </tr>
                                <tr>
                                    <td>Gerbang Keluar</td>
                                    <td> : </td>
                                    <td>{{ $booking->gateKeluar->nama }}</td>
                                </tr>
                                <tr>
                                    <td>Check In</td>
                                    <td> : </td>
                                    <td>{{ \Carbon\Carbon::parse($booking->tanggal_masuk)->isoFormat('D MMMM Y') }}</td>
                                </tr>
                                <tr>
                                    <td>Check Out</td>
                                    <td> : </td>
                                    <td>{{ \Carbon\Carbon::parse($booking->tanggal_masuk)->isoFormat('D MMMM Y') }}</td>
                                </tr>
                                <tr>
                                    <td>Jumlah Pendaki</td>
                                    <td> : </td>
                                    <td>{{ $booking->total_pendaki_wni }} WNI dan {{ $booking->total_pendaki_wna }} WNA
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
                <hr>
                @foreach ($pendakis as $key => $pendaki)
                    <div class="mt-4">
                        <h1 class="fs-5 fw-bold">
                            @if ($key === 0)
                                Biodata Ketua
                            @else
                                Biodata Pendaki {{ $key }}
                            @endif
                        </h1>
                        <div class="row">
                            <div class="col-12 col-lg-6">
                                <table class="table table-borderless">
                                    <tr>
                                        <td>Nama</td>
                                        <td> : </td>
                                        <td>{{ $pendaki->first_name . ' ' . $pendaki->last_name }}</td>
                                    </tr>
                                    <tr>
                                        <td>Kewarganegaraan</td>
                                        <td> : </td>
                                        <td>
                                            @if ($pendaki->kategori_pendaki == 'wni')
                                                Warga Negara Indonesia (WNI)
                                            @else
                                                Warga Negara Asing (WNA)
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>No KTP/Pasport</td>
                                        <td> : </td>
                                        <td>{{ $pendaki->biodata->nik }}</td>
                                    </tr>
                                    <tr>
                                        <td>No Telepon</td>
                                        <td> : </td>
                                        <td>{{ $pendaki->biodata->no_hp }}</td>
                                    </tr>
                                    <tr>
                                        <td>No Telepon Darurat</td>
                                        <td> : </td>
                                        <td>{{ $pendaki->biodata->no_hp_darurat }}</td>
                                    </tr>
                                    <tr>
                                        <td>Tanggal Lahir</td>
                                        <td> : </td>
                                        <td>{{ \Carbon\Carbon::parse($pendaki->biodata->tanggal_lahir)->isoFormat('D MMMM Y') }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Usia</td>
                                        <td> : </td>
                                        <td>{{ intval(\Carbon\Carbon::parse(now())->isoFormat('Y')) - intval(\Carbon\Carbon::parse($pendaki->biodata->tanggal_lahir)->isoFormat('Y')) }}
                                        </td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-12 col-lg-6">
                                <div class="border rounded p-2" style="max-height: 300px; overflow: hidden;">
                                    @php
                                        // $extension = pathinfo($pendaki->lampiran_identitas, PATHINFO_EXTENSION);
                                        $extension = substr(
                                            $pendaki->biodata->lampiran_identitas,
                                            strrpos($pendaki->biodata->lampiran_identitas, '.') + 1,
                                        );
                                    @endphp

                                    {{-- extension: {{$extension}} --}}

                                    @if (in_array(strtolower($extension), ['jpg', 'jpeg', 'png', 'gif']))
                                        <img src="{{ asset($pendaki->biodata->lampiran_identitas) }}"
                                            alt="Lampiran Identitas" class="img-fluid"
                                            style="max-height: 280px; width: auto; display: block; margin: 0 auto;">
                                    @else
                                        <embed src="{{ asset($pendaki->biodata->lampiran_identitas) }}"
                                            type="application/pdf" width="100%" height="280px">
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
                <hr>
                <div class="mt-3">
                    <h1 class="fs-5 fw-bold">Barang Bawaan Wajib</h1>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="barangWajib[perlengkapan_gunung_standar]"
                            value="1" checked readonly>
                        <label class="form-check-label" for="perle_gunung">
                            Perlengkapan Standar Pendaki Gunung
                        </label>
                    </div>

                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="barangWajib[trash_bag]" value="1"
                            id="trash_bag" checked readonly>
                        <label class="form-check-label" for="trash_bag">
                            Trash Bag
                        </label>
                    </div>

                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="barangWajib[p3k_standart]" value="1"
                            id="p3k_standart" checked readonly>
                        <label class="form-check-label" for="p3k_standart">
                            P3K Standart
                        </label>
                    </div>

                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="barangWajib[survival_kit_standart]"
                            value="1" id="survival_kit_standart" checked readonly>
                        <label class="form-check-label" for="survival_kit_standart">
                            Survival Kit Standart
                        </label>
                    </div>

                </div>
            </div>
        </div>

        <div class="">
            <a class="btn btn-primary mt-4 me-3"
                href="{{ route('homepage.booking.formulir', ['id' => $booking->id]) }}">Formulir</a>
            <a href="{{ route('homepage.booking.payment', ['id' => $booking->id]) }}" class="btn btn-primary mt-4"
                href="#" id="pay-button">Selanjutnya</a>
        </div>
    </div>

    </div>
@endsection
@section('js')
    <script type="text/javascript" src="https://app.sandbox.midtrans.com/snap/snap.js"
        data-client-key="SB-Mid-client-8mWWkdmzeR1xRVmL"></script>
    <!-- Note: replace with src="https://app.midtrans.com/snap/snap.js" for Production environment -->
    </head>

    <script>
        console.log("The Pendakis");
        console.log(@json($pendakis[0]));
        // console.log({{ $pendakis }});
    </script>


    {{-- <script type="text/javascript">
        // For example trigger on button clicked, or any time you need
        var payButton = document.getElementById('pay-button');
        payButton.addEventListener('click', function() {
            // Trigger snap popup. @TODO: Replace TRANSACTION_TOKEN_HERE with your transaction token
            window.snap.pay('{{ $snaptoken }}', {
                onSuccess: function(result) {
                    window.location.href =
                        "{{ route('homepage.booking.payment', ['id' => $booking->id]) }}";
                },
                onPending: function(result) {
                    /* You may add your own implementation here */
                    alert("wating your payment!");
                    console.log(result);
                },
                onError: function(result) {
                    /* You may add your own implementation here */
                    alert("payment failed!");
                    console.log(result);
                },
                onClose: function() {
                    /* You may add your own implementation here */
                    alert('you closed the popup without finishing the payment');
                }
            });
            // customer will be redirected after completing payment pop-up
        });
    </script> --}}
@endsection
