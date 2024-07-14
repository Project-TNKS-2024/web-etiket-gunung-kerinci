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
                                id="dropdown-prov" href="#" role="button" data-bs-toggle="dropdown"
                                aria-expanded="false">
                                Provinsi
                            </button>

                            <div class="dropdown-menu ">
                                <div class="overflow-y-scroll" id="daftar-provinsi" style="max-height: 200px;">
                                </div>
                                <div class="position-fixed w-100 *: " style="top: -40px;width:fit-content;">
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
                                id="dropdown-kab" href="#" role="button" data-bs-toggle="dropdown"
                                aria-expanded="false">
                                Kab/Kota
                            </button>

                            <div class="dropdown-menu">
                                <div class="overflow-y-auto w-100 " id="daftar-kab" style="max-height: 200px;">
                                    <div class="w-100 dropdown-item">Pilih Provinsi</div>
                                </div>
                                <div class="position-fixed w-100 *: " style="top: -40px;width:fit-content;">
                                    <input type="text" class="shadow border form-control rounded-sm" id="cariKab"
                                        placeholder="Cari Kab/Kot" oninput="cariItem('cariKab', 'daftar-kab')">
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
                                id="dropdown-kec" href="#" role="button" data-bs-toggle="dropdown"
                                aria-expanded="false">
                                Kecamatan
                            </button>

                            <div class="dropdown-menu">
                                <div class="overflow-y-auto w-100 " id="daftar-kec" style="max-height: 200px;">
                                    <div class="w-100 dropdown-item">Pilih Kabupaten</div>
                                </div>
                                <div class="position-fixed w-100 *: " style="top: -40px;width:fit-content;">
                                    <input type="text" class="shadow border form-control rounded-sm" id="cariKec"
                                        placeholder="Cari Kecamatan" oninput="cariItem('cariKec', 'daftar-kec')">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col " style="overflow-wrap: unset; flex-wrap: nowrap; overflow:visible;">
                        <label class="text-sm font-normal"
                            style="white-space: nowrap;overflow-wrap: unset; flex-wrap: nowrap; overflow:visible;">Pilih
                            Desa/Kelurahan</label>
                        <div class="dropdown">
                            <button
                                class="w-100 btn btn-outline-secondary dropdown-toggle d-flex justify-content-between align-items-center"
                                id="dropdown-kel" href="#" role="button" data-bs-toggle="dropdown"
                                aria-expanded="false">
                                Desa/Kel
                            </button>

                            <div class="dropdown-menu">
                                <div class="overflow-y-auto w-100 " id="daftar-kel" style="max-height: 200px;">
                                    <div class="w-100 dropdown-item">Pilih Kecamatan</div>
                                </div>
                                <div class="position-fixed w-100 *: " style="top: -40px;width:fit-content;">
                                    <input type="text" class="shadow border form-control rounded-sm" id="cariKel"
                                        placeholder="Cari Des/Kel" oninput="cariItem('cariKel', 'daftar-kel')">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
