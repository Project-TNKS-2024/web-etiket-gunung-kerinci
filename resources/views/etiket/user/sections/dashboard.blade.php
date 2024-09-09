@extends('etiket.user.template.index')

@section('sub-css')
@endsection


@section('sub-main')
<script></script>

<div class="col py-5 px-4 my-5 my-md-0" style="min-height: 700px; overflow-y: auto;">
    <h3 class="font-semibold">Selamat datang di website E-Tiket TNKS, {{auth()->user()->fullname}}</h3>
    <div class="row gap-3 px-2">
        <div class="col-sm-12 col-md-12 col-lg-5 col-xl-3 col-12 text-sm p-2 rounded py-3 d-flex gap-3" style="border: 1px solid var(--neutrals300)">
            <div><img src="{{asset('assets/icon/tnks/compass-dark.svg')}}" width="40"/></div>
            <div>
                <div>Explorasi Selesasi</div>
                <div>1 Explorasi</div>
            </div>
        </div>
        <div class="col-sm-12 col-md-12 col-lg-5 col-xl-3 col-12 text-sm p-2 rounded py-3 d-flex gap-3" style="border: 1px solid var(--neutrals300)">
            <div><img src="{{asset('assets/icon/tnks/compass-dark.svg')}}" width="40"/></div>
            <div>
                <div>Status Pendaftaran</div>
                <div>1 Explorasi</div>
            </div>
        </div>
        <div class="col-sm-12 col-md-12 col-lg-5 col-xl-3 col-12 text-sm p-2 rounded py-3 d-flex gap-3" style="border: 1px solid var(--neutrals300)">
            <div><img src="{{asset('assets/icon/tnks/compass-dark.svg')}}" width="40"/></div>
            <div>
                <div>Hitung Mundur Check-In</div>
                <div>1 Explorasi</div>
            </div>
        </div>
    </div>
    <h4 class="font-semibold mt-4 px-3">Riwayat Booking</h4>
    <div class="d-flex gap-5 px-3" style="flex-wrap: wrap;">
        @foreach ($booking as $book)
            <div class="row col-5 col-sm-12 col-md-12 col-lg-12 col-xl-6 rounded-2xl card ticket px-4 p-sm-4 p-md-5 pt-md-3 pt-3" style="padding-bottom: 30px; padding-right: 30px;">
                <div class="row align-items-center gap-2 font-semibold">Status <div class="{{$book->status_pembayaran ? "gk-bg-success200" : "gk-bg-warning200"}} rounded-md px-4 py-2 font-bold" style="width: fit-content">{{$book->status_pembayaran ? "Aktif" : "Belum Bayar"}}</div></div>
                <div class="row ticket-content  mx-0">
                    <div class="col-12">
                        <div class="row my-2 ">
                            <div class="col-6 ">
                                <div class="font-semibold">Nama Ketua</div>
                                <div class="">{{auth()->user()->fullname}}</div>
                            </div>
                            <div class="col-6 ">
                                <div class="font-semibold">SIMAKSI</div>
                                <div class="" style="color: red;">Tidak</div>
                            </div>
                        </div>
                        <div class="row my-2">
                            <div class="col-6">
                                <div class="font-semibold">Gerbang Masuk</div>
                                <div class="">{{$book->gateMasuk->nama}}</div>
                            </div>
                            <div class="col-6">
                                <div class="font-semibold">Gerbang Keluar</div>
                                <div class="">{{$book->gateKeluar->nama}}</div>
                            </div>
                        </div>

                        <div class="row my-2">
                            <div class="col-6">
                                <div class="font-semibold">Check In</div>
                                <div class="">{{$book->tanggal_masuk}}</div>
                            </div>
                            <div class="col-6">
                                <div class="font-semibold">Check Out</div>
                                <div class="">{{$book->tanggal_keluar}}</div>
                            </div>
                        </div>

                        <div class="row my-2 ">
                            <div class="col-6">
                                <div class="font-semibold text-nowrap">Jumlah Anggota</div>
                                <div class="">{{$book->total_pendaki_wni+$book->total_pendaki_wna}} Orang</div>
                            </div>
                            <div class="col-6" style="width: fit-content">
                                <div class="font-semibold">Kewarganegaraan</div>
                                <div class="row">
                                    <div class="col">{{$book->total_pendaki_wni}} WNI</div>
                                    <div class="col">{{$book->total_pendaki_wna}} WNA</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 text-center font-semibold ">
                        <img src="{{ asset('assets/img/sampel/QR.png') }}" style="max-width: 150px;" width="100%" />
                        <div class="text-center w-100 text-sm mt-2">Kode Booking</div>
                        <div class="text-center w-100 text-lg">{{$book->unique_code}}</div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

</div>

    <script>
        console.log('users: ', @json($user));
        let countriesData = [];

        const sidebarMenu = document.querySelectorAll(".dashboard-sidebar-btn");
        sidebarMenu.forEach((o, i) => {
            sidebarMenu[i].classList.remove("active");
        });
        const profile = document.querySelector("#dashboard");
        profile.classList.add("active");
    </script>
@endsection

@push('scripts')
<!-- Popper.js -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/2.11.7/umd/popper.min.js" integrity="sha384-oBqDVmMz4fnFO9gybA6zjzLIpKflFsFVxYycpV5x9W4FUIsxAB7BJ4Vf8pZFOFpg" crossorigin="anonymous">
</script>
<!-- Bootstrap JS -->
<script src="https://stackpath.bootstrapcdn.com/bootstrap/5.1.3/js/bootstrap.min.js" integrity="sha384-QJHtvGhmr9b0Lr1Bt6f3B6md62z0vT4Xdb1BzVg20r13a6c4a21fF3+k9J7l6+dB" crossorigin="anonymous">
</script>
@endpush

@section('js')
@endsection
