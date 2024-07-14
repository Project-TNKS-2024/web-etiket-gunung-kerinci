<div class="row my-0 py-0">
    <div class="form-group my-3 col-12 col-sm-12 col-md-12 col-lg-6">
        <label class="mandatory text-base font-semibold">Nomor Telepon</label>
        <div class="d-flex gap-2">
            <div class="dropdown custom-dropdown-item">
                <button
                    class="h-100 btn btn-outline-secondary dropdown-toggle d-flex justify-content-between align-items-center"
                    id="dropdown-identitas" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
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
            <input value="{{ auth()->user()->no_hp }}" type="text" class="form-control border-secondary "
                id="nomor-telepon" name="nomor_telepon" placeholder="Nama Telepon">
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
</div>
