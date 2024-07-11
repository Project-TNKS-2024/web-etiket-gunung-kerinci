@extends('etiket.user.dashboard')

@section('sub-css')
@endsection


@section('sub-main')
    <script></script>

    <div class="col py-5 px-4 my-5 my-md-0" style="min-height: 500px; overflow-y: auto;">
        <form class="row rounded-2xl card p-3">
            {{-- JENIS KEWARGANEGARAAN --}}
            @csrf
            <div class="form-group ">
                <label class="mandatory text-base font-semibold">Jenis Kewarganageraan</label>
                <div class="form-group text-sm d-flex align-items-center gap-2 form-check row">
                    <div class="col-12 col-md-4 ">
                        <input class="form-check-input" type="radio" id="wni" name="kewarganegaraan" value="wni"
                            checked>
                        <label class="form-check-label" for="wni">Warga Negara Indonesia</label>
                    </div>
                    <div class="col-12 col-md-4 ">
                        <input class="form-check-input" type="radio" id="wna" name="kewarganegaraan" value="wna"
                            checked>
                        <label class="form-check-label" for="wna">Warga Negara Asing</label>
                    </div>
                </div>
            </div>

            <div class="row py-0 my-0">
                <div class="form-group my-3 col-12 col-sm-12 col-md-12 col-lg-6">
                    <label class="mandatory text-base font-semibold">Jenis Identitas</label>
                    <div class="dropdown">
                        <button
                            class="w-100 btn btn-outline-secondary dropdown-toggle d-flex justify-content-between align-items-center"
                            id="dropdown-identitas" href="#" role="button" data-bs-toggle="dropdown"
                            aria-expanded="false">
                            KTP
                        </button>

                        <ul class="dropdown-menu w-100 shadow">
                            <li><button class="dropdown-item" href="#">KTP</button></li>
                            <li><button class="dropdown-item" href="#">Passport</button></li>
                            <li><button class="dropdown-item" href="#">Lainnya</button></li>
                        </ul>
                    </div>
                </div>
                <div class="form-group my-3 col-12 col-sm-12 col-md-12 col-lg-6">
                    <label class="mandatory text-base font-semibold" for="id-pendaftar">NIK/Passport</label>
                    <input type="text" class="form-control border-secondary" id="id-pendaftar" name="id_pendaftar"
                        placeholder="NIK/Pasport">
                </div>
            </div>

            <div class="row my-0 py-0">
                <div class="form-group my-3 col-12 col-sm-12 col-md-12 col-lg-6">
                    <label class="mandatory text-base font-semibold">Nama Lengkap</label>
                    <input type="text" class="form-control border-secondary" id="nama-lengkap" name="nama_lengkap"
                        placeholder="Nama Lengkap">
                </div>
                <div class="form-group my-3 col-12 col-sm-12 col-md-12 col-lg-6">
                    <label class="mandatory text-base font-semibold" for="id-pendaftar">NIK/Passport</label>
                    <input class="form-control" type="file" id="formFile">
                </div>
            </div>

            <div class="row my-0 py-0">
                <div class="form-group my-3 col-12 col-sm-12 col-md-12 col-lg-6">
                    <label class="mandatory text-base font-semibold">Nomor Telepon</label>
                    <div class="d-flex gap-2">
                        <div class="dropdown custom-dropdown-item">
                            <button
                                class="h-100 btn btn-outline-secondary dropdown-toggle d-flex justify-content-between align-items-center"
                                id="dropdown-identitas" href="#" role="button" data-bs-toggle="dropdown"
                                aria-expanded="false">
                                <img src="{{ asset('assets/img/logo/id-flag.png') }}" width="20" />
                            </button>

                            <div class="dropdown-menu" aria-labelledby="dropdown-identitas">
                                <div id="telepon-item"
                                    style="overflow-y:auto; width: fit-content; max-height: 200px; overflow-x:hidden;">
                                </div>
                                <div class="position-fixed w-100 *: " style="bottom: -40px;width:fit-content;">
                                    <input type="text" class="form-control rounded-sm" id="cariTelepon"
                                        placeholder="Cari Negara">
                                </div>
                            </div>
                        </div>
                        <input type="text" class="form-control border-secondary " id="nomor-telepon" name="nomor_telepon"
                            placeholder="Nama Telepon">
                    </div>
                </div>
                <div class="form-group my-3 col-12 col-sm-12 col-md-12 col-lg-6">
                    <label class="mandatory text-base font-semibold">Nomor Telepon Darurat</label>
                    <div class="d-flex gap-2">
                        <div class="dropdown custom-dropdown-item">
                            <button
                                class="h-100 btn btn-outline-secondary dropdown-toggle d-flex justify-content-between align-items-center"
                                id="dropdown-identitas" href="#" role="button" data-bs-toggle="dropdown"
                                aria-expanded="false">
                                <img src="{{ asset('assets/img/logo/id-flag.png') }}" width="20" />
                            </button>

                            <div class="dropdown-menu" aria-labelledby="dropdown-identitas">
                                <div id="telepon-item-darurat"
                                    style="overflow-y:auto; width: fit-content; max-height: 200px; overflow-x:hidden;">
                                </div>
                                <div class="position-fixed w-100 *: " style="bottom: -40px;width:fit-content;">
                                    <input type="text" class="form-control rounded-sm" id="cariTeleponDarurat"
                                        placeholder="Cari Negara">
                                </div>
                            </div>
                        </div>
                        <input type="text" class="form-control border-secondary " id="nomor-telepon-darurat"
                            name="nomor_telepon_darurat" placeholder="Nama Telepon Darurat">
                    </div>
                </div>
                <div class="row my-0 py-0">
                    <div class="form-group col-12 col-sm-12 col-md-12 col-lg-6">
                        <div class="row">
                            <div class="col-8 my-3">
                                <label class="mandatory text-base font-semibold text-nowrap">Tanggal Lahir</label>
                                <input type="datetime-local" class="form-control border-secondary" id="tgl-lahir"
                                    name="tgl_lahir">
                            </div>
                            <div class="col-4 my-3">

                                <label class="mandatory text-base font-semibold">Usia</label>
                                <input type="text" class="form-control border-secondary " id="usia"
                                    name="usia" placeholder="Usia">
                            </div>
                        </div>
                    </div>

                    <div class="form-group col-12 col-sm-12 col-md-12 col-lg-6">
                        <div class="row">
                            <div class="col-6 my-3">
                                <label class="mandatory text-base font-semibold text-nowrap">Berat Badan</label>
                                <input type="number" class="form-control border-secondary" id="berat"
                                    name="berat" placeholder="Berat Badan">
                            </div>
                            <div class="col-6 my-3">
                                <label class="mandatory text-base font-semibold text-nowrap">Tinggi Badan</label>
                                <input type="number" class="form-control border-secondary" id="tinggi"
                                    name="tinggi" placeholder="Tinggi (cm)">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row my-0 py-0">
                    <div class="form-group my-3 col">
                        <label class="mandatory text-base font-semibold">Alamat Domisili</label>
                        <div class="row">
                            <div class="col">
                                <div class="row">
                                    <div class="col ">
                                        <label class="text-sm font-normal text-nowrap">Pilih Provinsi</label>
                                        <div class="dropdown">
                                            <button
                                                class="text-nowrap w-100 btn btn-outline-secondary dropdown-toggle d-flex justify-content-between align-items-center"
                                                id="dropdown-prov" href="#" role="button"
                                                data-bs-toggle="dropdown" aria-expanded="false">
                                                Provinsi
                                            </button>

                                            <div class="dropdown-menu ">
                                                <div class="overflow-y-scroll" id="daftar-provinsi"
                                                    style="max-height: 200px;"></div>
                                                <div class="position-fixed w-100 *: "
                                                    style="top: -40px;width:fit-content;">
                                                    <input type="text" class="shadow border form-control rounded-sm"
                                                        id="cariProvinsi" placeholder="Cari Provinsi"
                                                        oninput="cariItem('cariProvinsi', 'daftar-provinsi')">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col ">
                                        <label class="text-sm font-normal">Kabupaten/Kota</label>
                                        <div class="dropdown">
                                            <button
                                                class="w-100 btn btn-outline-secondary dropdown-toggle d-flex justify-content-between align-items-center"
                                                id="dropdown-kab" href="#" role="button"
                                                data-bs-toggle="dropdown" aria-expanded="false">
                                                Kab/Kota
                                            </button>

                                            <div class="dropdown-menu">
                                                <div class="overflow-y-auto w-100 " id="daftar-kab"
                                                    style="max-height: 200px;">
                                                    <div class="w-100 dropdown-item">Pilih Provinsi</div>
                                                </div>
                                                <div class="position-fixed w-100 *: "
                                                    style="top: -40px;width:fit-content;">
                                                    <input type="text" class="shadow border form-control rounded-sm"
                                                        id="cariKab" placeholder="Cari Kab/Kot"
                                                        oninput="cariItem('cariKab', 'daftar-kab')">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col">
                                <div class="row">
                                    <div class="col ">
                                        <label class="text-sm font-normal">Pilih Kecamatan</label>
                                        <div class="dropdown">
                                            <button
                                                class="w-100 btn btn-outline-secondary dropdown-toggle d-flex justify-content-between align-items-center"
                                                id="dropdown-kec" href="#" role="button"
                                                data-bs-toggle="dropdown" aria-expanded="false">
                                                Kecamatan
                                            </button>

                                            <div class="dropdown-menu">
                                                <div class="overflow-y-auto w-100 " id="daftar-kec"
                                                    style="max-height: 200px;">
                                                    <div class="w-100 dropdown-item">Pilih Kabupaten</div>
                                                </div>
                                                <div class="position-fixed w-100 *: "
                                                    style="top: -40px;width:fit-content;">
                                                    <input type="text" class="shadow border form-control rounded-sm"
                                                        id="cariKec" placeholder="Cari Kecamatan"
                                                        oninput="cariItem('cariKec', 'daftar-kec')">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col "
                                        style="overflow-wrap: unset; flex-wrap: nowrap; overflow:visible;">
                                        <label class="text-sm font-normal"
                                            style="white-space: nowrap;overflow-wrap: unset; flex-wrap: nowrap; overflow:visible;">Pilih
                                            Desa/Kelurahan</label>
                                        <div class="dropdown">
                                            <button
                                                class="w-100 btn btn-outline-secondary dropdown-toggle d-flex justify-content-between align-items-center"
                                                id="dropdown-kel" href="#" role="button"
                                                data-bs-toggle="dropdown" aria-expanded="false">
                                                Desa/Kel
                                            </button>

                                            <div class="dropdown-menu">
                                                <div class="overflow-y-auto w-100 " id="daftar-kel"
                                                    style="max-height: 200px;">
                                                    <div class="w-100 dropdown-item">Pilih Kecamatan</div>
                                                </div>
                                                <div class="position-fixed w-100 *: "
                                                    style="top: -40px;width:fit-content;">
                                                    <input type="text" class="shadow border form-control rounded-sm"
                                                        id="cariKel" placeholder="Cari Des/Kel"
                                                        oninput="cariItem('cariKel', 'daftar-kel')">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
        </form>
    </div>
    <script>
        let countriesData = [];
        const sidebarMenu = document.querySelectorAll(".dashboard-sidebar-btn");
        sidebarMenu.forEach((o, i) => {
            sidebarMenu[i].classList.remove("active");
        });
        const profile = document.querySelector("#dashboard-profile");
        profile.classList.add("active");
    </script>

    @include('etiket.user.sections.profile.flags-script')
    @include('etiket.user.sections.profile.wilayah')
@endsection
