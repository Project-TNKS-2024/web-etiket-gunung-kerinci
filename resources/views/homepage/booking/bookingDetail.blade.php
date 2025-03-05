@extends('homepage.template.index')


@section('css')
<style>
    .table tbody tr td {
        padding: 3px 5px;
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

    .table td:first-child {
        width: 200px;
    }
</style>
@endsection


@section('main')
@include('homepage.template.header', [
'title' => 'Pendakian Gunung Kerinci',
'caption' => 'Detail Booking',
])
<div class="container my-5">
    @include('homepage.booking.booking-nav', ['step' => 3])

    <div class="card border-0 shadow">
        <div class="card-body px-4 px-md-5 pb-4">
            <div class="mt-3">
                <h1 class="fs-5 fw-bold">Booking {{ $booking->gateMasuk->destinasi->kategori }}</h1>
                <div class="row">
                    <div class="col-12 col-md-6">
                        <table class="table table-borderless ">
                            <tr>
                                <td>Nama Ketua</td>
                                <td> : </td>
                                <td>{{ $formulirPendakis[0]->first_name . ' ' . $formulirPendakis[0]->last_name }}</td>
                            </tr>
                            <tr>
                                <td>Tanggal Masuk</td>
                                <td> : </td>
                                <td>{{ \Carbon\Carbon::parse($booking->tanggal_masuk)->isoFormat('D MMMM Y') }}</td>
                            </tr>
                            <tr>
                                <td>Tanggal Keluar</td>
                                <td> : </td>
                                <td>{{ \Carbon\Carbon::parse($booking->tanggal_keluar)->isoFormat('D MMMM Y') }}</td>
                            </tr>
                            <tr>
                                <td>Jumlah Pendaki</td>
                                <td> : </td>
                                <td>{{ $booking->total_pendaki_wni }} WNI dan {{ $booking->total_pendaki_wna }} WNA</td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-12 col-md-6">
                        <table class="table table-borderless ">
                            <tr>
                                <td>Nama Destinasi</td>
                                <td> : </td>
                                <td>{{ $booking->gatemasuk->destinasi->nama }}</td>
                            </tr>
                            <tr>
                                <td>Paket Pendakian</td>
                                <td> : </td>
                                <td>{{ $booking->gkTiket->nama }}</td>
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
                        </table>
                    </div>
                </div>
            </div>
            <hr>

            @foreach ($formulirPendakis as $key => $pendaki)
            <div class=" mt-4">
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
                                <td>{{$pendaki->first_name . ' ' . $pendaki->last_name}}</td>
                            </tr>
                            <tr>
                                <td>Kewarganegaraan</td>
                                <td> : </td>
                                <td>
                                    @if($pendaki->biodata->kenegaraan == 'ID')
                                    (WNI) {{$pendaki->biodata->Datanegara->name}}
                                    @else
                                    (WNA) {{$pendaki->biodata->Datanegara->name}}
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td>Jenis Kelamin</td>
                                <td> : </td>
                                <td>{{$pendaki->biodata->jenis_kelamin == 'l' ? 'Laki-laki' : 'Perempuan'}}</td>
                            </tr>
                            <tr>
                                <td>No. Telepon</td>
                                <td> : </td>
                                <td>{{$pendaki->biodata->no_hp}}</td>
                            </tr>
                            <tr>
                                <td>No. Telepon Darurat</td>
                                <td> : </td>
                                <td>{{$pendaki->biodata->no_hp_darurat}}</td>
                            </tr>
                            <tr>
                                <td>Tanggal Lahir</td>
                                <td> : </td>
                                <td>{{ \Carbon\Carbon::parse($pendaki->biodata->tanggal_lahir)->isoFormat('D MMMM Y') }}</td>
                            </tr>
                            <tr>
                                <td>Usia</td>
                                <td> : </td>
                                <td>{{$pendaki->usia}} tahun</td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-12 col-lg-6">
                        @if ($pendaki->lampiran_surat_izin_ortu)
                        <div class="border rounded p-2" style="max-height: 300px; overflow: hidden;">
                            @php
                            $extension = pathinfo($pendaki->lampiran_surat_izin_ortu, PATHINFO_EXTENSION);
                            @endphp

                            @if(in_array(strtolower($extension), ['jpg', 'jpeg', 'png', 'gif']))
                            <img src="{{asset($pendaki->lampiran_surat_izin_ortu)}}" alt="Lampiran Identitas" class="img-fluid" style="max-height: 280px; width: auto; display: block; margin: 0 auto;">
                            @else
                            <embed src="{{asset($pendaki->lampiran_surat_izin_ortu)}}" type="application/pdf" width="100%" height="280px">
                            @endif
                        </div>

                        @endif

                    </div>
                </div>
            </div>
            @endforeach
            <hr>
            <div class="mt-3">
                <h1 class="fs-5 fw-bold">Barang Bawaan Wajib</h1>
                <fieldset disabled>
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
                </fieldset>

            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12 col-md-4">
            @if ($booking->status_booking <=3)
                <a type="submit" class="btn btn-danger w-100 fw-bold mt-3" href="{{ route('homepage.booking.cancel', ['id' => $booking->id]) }}" onclick="openswal(event, this)">Batalkan Booking</a>
                @endif
        </div>
        <div class="col-12 col-md-4">
            <a type="submit" class="btn btn-info w-100 fw-bold mt-3"
                href="{{ route('homepage.booking.edit', ['id' => $booking->id]) }}">Kembali</a>
        </div>
        <div class="col-12 col-md-4 text-end">
            <a type="submit" class="btn btn-primary w-100 fw-bold mt-3"
                href="{{ route('homepage.booking.payment', ['id' => $booking->id]) }}">Selanjutnya</a>
        </div>
    </div>
</div>
@endsection
@section('js')

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function openswal(event, button) {
        event.preventDefault(); // Mencegah navigasi otomatis

        Swal.fire({
            title: "Apakah Anda yakin?",
            text: "Data yang dihapus tidak dapat dikembalikan!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#d33",
            cancelButtonColor: "#3085d6",
            confirmButtonText: "Ya, hapus!",
            cancelButtonText: "Batal"
        }).then((result) => {
            if (result.isConfirmed) {
                // Redirect ke halaman pembatalan booking
                window.location.href = button.getAttribute("href");
            }
        });
    }
</script>

@endsection