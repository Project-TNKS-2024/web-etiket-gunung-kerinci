@extends('etiket.user.dashboard')

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
    </style>
@endsection


@section('sub-main')
    <script>
        const sidebarMenu = document.querySelectorAll(".dashboard-sidebar-btn");
        sidebarMenu.forEach((o, i) => {
            sidebarMenu[i].classList.remove("active");
        });
        const riwayat = document.querySelector("#dashboard-riwayat");
        riwayat.classList.add("active");
    </script>


    <div class="custom-scrollbar " style="">
        <div class="row rounded-2xl shadow card pt-3 px-5" style="padding-bottom: 30px;">
            <div class="gk-bg-success200 rounded-md px-4 py-2 font-bold" style="width: fit-content">Aktif</div>
            <div class="row" style="">
                <div class="col-8 ">
                    <div class="row my-2">
                        <div class="col">
                            <div class="font-semibold">Nama Ketua</div>
                            <div class="">Pendaki Handal</div>
                        </div>
                        <div class="col">
                            <div class="font-semibold">SIMAKSI</div>
                            <div class="" style="color: red;">Tidak</div>
                        </div>
                    </div>
                    <div class="row my-2">
                        <div class="col">
                            <div class="font-semibold">Gerbang Masuk</div>
                            <div class="">Kersik Tuo</div>
                        </div>
                        <div class="col">
                            <div class="font-semibold">Gerbang Keluar</div>
                            <div class="">Kersik Tuo</div>
                        </div>
                    </div>

                    <div class="row my-2">
                        <div class="col">
                            <div class="font-semibold">Check In</div>
                            <div class="">14 Agustus 2024</div>
                        </div>
                        <div class="col">
                            <div class="font-semibold">Check Out</div>
                            <div class="">15 Agustus 2024</div>
                        </div>
                    </div>

                    <div class="row my-2">
                        <div class="col">
                            <div class="font-semibold">Jumlah Anggota</div>
                            <div class="">5 Orang</div>
                        </div>
                        <div class="col" style="width: fit-content">
                            <div class="font-semibold">Kewarganegaraan</div>
                            <div class="row">
                                <div class="col">3 WNI</div>
                                <div class="col">2 WNA</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="font-semibold col-3 d-flex align-items-center justify-content-center flex-column">
                    <img src="{{ asset('assets/img/sampel/QR.png') }}" width="150" />
                    <div class="text-sm mt-2">Kode Booking</div>
                    <div class="text-lg">A7XSFOLD</div>
                </div>
            </div>

        </div>

        <div class="row rounded-2xl shadow card pt-3 px-5 my-4" style="padding-bottom: 30px;">
            <div class="gk-bg-error200 rounded-md px-4 py-2 font-bold" style="width: fit-content">Tidak Aktif</div>
            <div class="row" style="">
                <div class="col-8 ">
                    <div class="row my-2">
                        <div class="col">
                            <div class="font-semibold">Nama Ketua</div>
                            <div class="">Pendaki Handal</div>
                        </div>
                        <div class="col">
                            <div class="font-semibold">SIMAKSI</div>
                            <div class="" style="color: red;">Tidak</div>
                        </div>
                    </div>
                    <div class="row my-2">
                        <div class="col">
                            <div class="font-semibold">Gerbang Masuk</div>
                            <div class="">Kersik Tuo</div>
                        </div>
                        <div class="col">
                            <div class="font-semibold">Gerbang Keluar</div>
                            <div class="">Kersik Tuo</div>
                        </div>
                    </div>

                    <div class="row my-2">
                        <div class="col">
                            <div class="font-semibold">Check In</div>
                            <div class="">14 Agustus 2024</div>
                        </div>
                        <div class="col">
                            <div class="font-semibold">Check Out</div>
                            <div class="">15 Agustus 2024</div>
                        </div>
                    </div>

                    <div class="row my-2">
                        <div class="col">
                            <div class="font-semibold">Jumlah Anggota</div>
                            <div class="">5 Orang</div>
                        </div>
                        <div class="col" style="width: fit-content">
                            <div class="font-semibold">Kewarganegaraan</div>
                            <div class="row">
                                <div class="col">3 WNI</div>
                                <div class="col">2 WNA</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="font-semibold col-3 d-flex align-items-center justify-content-center flex-column">
                    <img src="{{ asset('assets/img/sampel/QR.png') }}" width="150" />
                    <div class="text-sm mt-2">Kode Booking</div>
                    <div class="text-lg">A7XSFOLD</div>
                </div>
            </div>
        </div>

        <div class="row rounded-2xl shadow card pt-3 px-5 my-4" style="padding-bottom: 30px;">
            <div class="gk-bg-error200 rounded-md px-4 py-2 font-bold" style="width: fit-content">Tidak Aktif</div>
            <div class="row" style="">
                <div class="col-8 ">
                    <div class="row my-2">
                        <div class="col">
                            <div class="font-semibold">Nama Ketua</div>
                            <div class="">Pendaki Handal</div>
                        </div>
                        <div class="col">
                            <div class="font-semibold">SIMAKSI</div>
                            <div class="" style="color: red;">Tidak</div>
                        </div>
                    </div>
                    <div class="row my-2">
                        <div class="col">
                            <div class="font-semibold">Gerbang Masuk</div>
                            <div class="">Kersik Tuo</div>
                        </div>
                        <div class="col">
                            <div class="font-semibold">Gerbang Keluar</div>
                            <div class="">Kersik Tuo</div>
                        </div>
                    </div>

                    <div class="row my-2">
                        <div class="col">
                            <div class="font-semibold">Check In</div>
                            <div class="">14 Agustus 2024</div>
                        </div>
                        <div class="col">
                            <div class="font-semibold">Check Out</div>
                            <div class="">15 Agustus 2024</div>
                        </div>
                    </div>

                    <div class="row my-2">
                        <div class="col">
                            <div class="font-semibold">Jumlah Anggota</div>
                            <div class="">5 Orang</div>
                        </div>
                        <div class="col" style="width: fit-content">
                            <div class="font-semibold">Kewarganegaraan</div>
                            <div class="row">
                                <div class="col">3 WNI</div>
                                <div class="col">2 WNA</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="font-semibold col-3 d-flex align-items-center justify-content-center flex-column">
                    <img src="{{ asset('assets/img/sampel/QR.png') }}" width="150" />
                    <div class="text-sm mt-2">Kode Booking</div>
                    <div class="text-lg">A7XSFOLD</div>
                </div>
            </div>
        </div>
    </div>
@endsection
