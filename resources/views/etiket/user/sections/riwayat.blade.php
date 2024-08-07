@extends('etiket.user.template.index')

@section('sub-css')
<style>
    .custom-scrollbar::-webkit-scrollbar {
        width: 12px;
    }

    .custom-scrollbar::-webkit-scrollbar-track {
        background: #f1f1f1;
    }

    .custom-scrollbar::-webkit-scrollbar-thumb {
        background-color: #888;
        border-radius: 10px;
        border: 3px solid #f1f1f1;
    }

    .custom-scrollbar::-webkit-scrollbar-thumb:hover {
        background: #555;
    }

    /* Custom scrollbar styles for Firefox */
    .custom-scrollbar {
        scrollbar-width: thin;
        scrollbar-color: #888 #f1f1f1;
    }

    .ticket {
        max-width: 971px;
    }

    .ticket-content {
        display: flex;
    }
</style>
@endsection


@section('sub-main')
<div class="col py-5 px-4 my-5 my-md-0 " style="min-height: 500px; max-height: 100%; overflow-y: auto;">
    <script>
        console.log(@json($booking))
        const sidebarMenu = document.querySelectorAll(".dashboard-sidebar-btn");
        sidebarMenu.forEach((o, i) => {
            sidebarMenu[i].classList.remove("active");
        });
        const riwayat = document.querySelector("#dashboard-riwayat");
        riwayat.classList.add("active");
    </script>


    <div class="custom-scrollbar d-flex gap-2 flex-column">
        @foreach ($booking as $book)
        <div class="row rounded-2xl card ticket px-4 p-sm-4 p-md-5 pt-md-3 pt-3" style="padding-bottom: 30px; padding-right: 30px;">
            <div class="{{$book->status_booking ? "gk-bg-success200" : "gk-bg-error200"}} rounded-md px-4 py-2 font-bold" style="width: fit-content">Aktif</div>
                <div class="row ticket-content  mx-0">
                    <div class="col-sm-12 col-md-12 col-lg-8">
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
                    <div class="col-12 col-sm-12 col-md-12 col-lg-4 text-center font-semibold ">
                        <img src="{{ asset('assets/img/sampel/QR.png') }}" style="max-width: 150px;" width="100%" />
                        <div class="text-center w-100 text-sm mt-2">Kode Booking</div>
                        <div class="text-center w-100 text-lg">{{$book->unique_code}}</div>
                        <a href="{{route('homepage.booking-fp', ['id' => $book->id])}}" class="w-fit btn btn-warning  d-block mx-auto p-1 px-3 rounded shadow " style="cursor: pointer;">
                            <img src="{{asset('assets/icon/tnks/search_alt-dark.svg')}}" /> Detail
                        </a>
                    </div>
                </div>

            </div>
        @endforeach

    </div>
</div>
@endsection
