@extends('homepage.template.index')

@section('css')
    <style>
        .dashboard-sidebar.accessories {
            height: 141px;
        }


        @media (max-width: 768px) {
            .dashboard-sidebar {
                height: 500px;
            }
        }

        label.mandatory::after {
            content: " *";
            color: red;
        }

        .custom-dropdown-item {
            width: fit-content;
            /* Set your desired width here */
        }

        .border-secondary {
            border-color: var(--neutrals500)
        }
    </style>
@endsection

@section('main')
    <main class="container content my-4">
        <div class="container-fluid p-0">

            <div class="row " style="min-height: 500px;height:100%;">
                <div class="col col-md-5 col-xl-4 dashboard-sidebar">
                    @include('etiket.user.template.sidebar')
                </div>

                <div class="col-md-7 col-xl-8 my-5 my-sm-5 my-md-0 my-lg-0" style="min-height: 500px;">
                    <form class=" row form rounded-xl shadow py-3 px-2">
                        {{-- JENIS KEWARGANEGARAAN --}}
                        <div class="form-group">
                            <label class="mandatory text-base font-semibold">Jenis Kewarganageraan</label>
                            <div class="form-group text-sm d-flex align-items-center gap-2 form-check row">
                                <div class="col-12 col-md-4 ">
                                    <input class="form-check-input" type="radio" id="wni" name="kewarganegaraan"
                                        value="wni" checked>
                                    <label class="form-check-label" for="wni">Warga Negara Indonesia</label>
                                </div>
                                <div class="col-12 col-md-4 ">
                                    <input class="form-check-input" type="radio" id="wna" name="kewarganegaraan"
                                        value="wna" checked>
                                    <label class="form-check-label" for="wna">Warga Negara Asing</label>
                                </div>
                            </div>
                        </div>

                        {{-- SISI 1 --}}
                        <div class="container col-lg-6 col-md-12 col-sm-12" style="">
                            {{-- JENIS IDENTITAS --}}
                            <div class="form-group my-4">
                                <label class="mandatory text-base font-semibold">Jenis Identitas</label>
                                <div class="dropdown">
                                    <button
                                        class="w-100 btn btn-outline-secondary dropdown-toggle d-flex justify-content-between align-items-center"
                                        id="dropdown-identitas" href="#" role="button" data-bs-toggle="dropdown"
                                        aria-expanded="false">
                                        KTP
                                    </button>

                                    <ul class="dropdown-menu w-100">
                                        <li><button class="dropdown-item" href="#">KTP</button></li>
                                        <li><button class="dropdown-item" href="#">Passport</button></li>
                                        <li><button class="dropdown-item" href="#">Lainnya</button></li>
                                    </ul>
                                </div>
                            </div>
                            {{-- NAMA LENGKAP --}}
                            <div class="form-group my-4">
                                <label class="mandatory text-base font-semibold">Nama Lengkap</label>
                                <input type="text" class="form-control border-secondary" id="nama-lengkap"
                                    name="nama_lengkap" placeholder="Nama Lengkap">
                            </div>

                            {{-- nomor telepon --}}
                            <div class="form-group my-4">
                                <label class="mandatory text-base font-semibold">Nomor Telepon</label>
                                <div class="d-flex gap-2">
                                    <div class="dropdown custom-dropdown-item">
                                        <button
                                            class="h-100 btn btn-outline-secondary dropdown-toggle d-flex justify-content-between align-items-center"
                                            id="dropdown-identitas" href="#" role="button" data-bs-toggle="dropdown"
                                            aria-expanded="false">
                                            <img src="{{ asset('assets/img/logo/id-flag.png') }}" width="20" />
                                        </button>

                                        <div class="dropdown-menu" style="width: 20px;"
                                            aria-labelledby="dropdown-identitas">
                                            <button class="dropdown-item"><img
                                                    src="{{ asset('assets/img/logo/id-flag.png') }}" width="20" />
                                                ID (+61)</button>
                                            <button class="dropdown-item"><img
                                                    src="{{ asset('assets/img/logo/id-flag.png') }}" width="20" />
                                                ID (+62)</button>
                                            <button class="dropdown-item"><img
                                                    src="{{ asset('assets/img/logo/id-flag.png') }}" width="20" />
                                                ID (+63)</button>
                                        </div>
                                    </div>
                                    <input type="text" class="form-control border-secondary " id="nomor-telepon"
                                        name="nomor_telepon" placeholder="Nama Telepon">
                                </div>
                            </div>

                            {{-- Tanggal Lahir --}}
                            <div class="form-group my-4">
                                <div class="row">
                                    <div class="col">
                                        <label class="mandatory text-base font-semibold">Tanggal Lahir</label>
                                        <input type="datetime-local" class="form-control border-secondary" id="tgl-lahir"
                                            name="tgl_lahir">
                                    </div>
                                    <div class="col">

                                        <label class="mandatory text-base font-semibold">Usia</label>
                                        <input type="text" class="form-control border-secondary " id="usia"
                                            name="usia" placeholder="Usia">
                                    </div>
                                </div>
                            </div>

                            {{-- Alamat Domisili --}}
                            <div class="form-group my-4">
                                <label class="mandatory text-base font-semibold">Alamat Domisili</label>
                                <div class="row">
                                    <div class="col">
                                        <label class="text-sm font-normal">Pilih Provinsi</label>
                                        <div class="dropdown">
                                            <button
                                                class="w-100 btn btn-outline-secondary dropdown-toggle d-flex justify-content-between align-items-center"
                                                id="dropdown-identitas" href="#" role="button"
                                                data-bs-toggle="dropdown" aria-expanded="false">
                                                Pilih Provinsi
                                            </button>

                                            <div class="dropdown-menu">
                                                <button class="dropdown-item">x</button>
                                                <button class="dropdown-item">y</button>
                                                <button class="dropdown-item">z</button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col">
                                        <label class="text-sm font-normal">Kabupaten/Kota</label>
                                        <div class="dropdown">
                                            <button
                                                class="w-100 btn btn-outline-secondary dropdown-toggle d-flex justify-content-between align-items-center"
                                                id="dropdown-identitas" href="#" role="button"
                                                data-bs-toggle="dropdown" aria-expanded="false">
                                                Pilih Kab/Kota
                                            </button>

                                            <div class="dropdown-menu">
                                                <button class="dropdown-item">x</button>
                                                <button class="dropdown-item">y</button>
                                                <button class="dropdown-item">z</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- SISI 2 --}}
                        <div class="col-lg-6 col-md-12 col-sm-12" style="">
                            <div class="form-group my-4">
                                <label class="mandatory text-base font-semibold" for="id-pendaftar">NIK/Passport</label>
                                <input type="text" class="form-control border-secondary" id="id-pendaftar"
                                    name="id_pendaftar" placeholder="NIK/Pasport">
                            </div>
                            <div class="form-group my-4">
                                <label class="mandatory text-base font-semibold" for="id-pendaftar">NIK/Passport</label>
                                <input class="form-control" type="file" id="formFile">
                            </div>

                            <div class="form-group my-4">
                                <label class="mandatory text-base font-semibold">Nomor Telepon Darurat</label>
                                <div class="d-flex gap-2">
                                    <div class="dropdown custom-dropdown-item">
                                        <button
                                            class="h-100 btn btn-outline-secondary dropdown-toggle d-flex justify-content-between align-items-center"
                                            id="dropdown-identitas" href="#" role="button"
                                            data-bs-toggle="dropdown" aria-expanded="false">
                                            <img src="{{ asset('assets/img/logo/id-flag.png') }}" width="20" />
                                        </button>

                                        <div class="dropdown-menu" style="width: 20px;"
                                            aria-labelledby="dropdown-identitas">
                                            <button class="dropdown-item"><img
                                                    src="{{ asset('assets/img/logo/id-flag.png') }}" width="20" />
                                                ID (+61)</button>
                                            <button class="dropdown-item"><img
                                                    src="{{ asset('assets/img/logo/id-flag.png') }}" width="20" />
                                                ID (+62)</button>
                                            <button class="dropdown-item"><img
                                                    src="{{ asset('assets/img/logo/id-flag.png') }}" width="20" />
                                                ID (+63)</button>
                                        </div>
                                    </div>
                                    <input type="text" class="form-control border-secondary " id="nomor-telepon"
                                        name="nomor_telepon" placeholder="Nama Telepon">
                                </div>
                            </div>

                            <div class="form-group my-4">
                                <div class="row">
                                    <div class="col">
                                        <label class="text-sm font-normal">Kecamatan</label>
                                        <input type="text" class="form-control border-secondary " id="nomor-telepon"
                                            name="nomor_telepon" placeholder="Nama Telepon">
                                    </div>
                                    <div class="col">
                                        <label class="text-sm font-normal">Desa/Kelurahan </label>
                                        <input type="text" class="form-control border-secondary " id="nomor-telepon"
                                            name="nomor_telepon" placeholder="Nama Telepon">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </main>

    </main>
@endsection

@section('js')
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.7/dist/umd/popper.min.js"></script>
    <script src="{{ asset('bootstrap-5.3.3-dist/js/bootstrap.min.js') }}"></script>


    <script>
        $(document).ready(function() {
            $('#identitas-dropdown').dropdown();
        });
    </script>
@endsection
