<div class="row py-0 my-0">
    <div class="form-group my-3 col-12 col-sm-12 col-md-12 col-lg-6">
        <label class="mandatory text-base font-semibold">Jenis Identitas</label>
        <div class="dropdown">
            <button
                class="w-100 btn btn-outline-secondary dropdown-toggle d-flex justify-content-between align-items-center"
                id="dropdown-identitas" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                KTP
            </button>

            <ul class="dropdown-menu w-100 shadow">
                <li><a onclick="select(event, 'dropdown-identitas','jenis-identitas', '1')" class="dropdown-item" href="#">KTP</a></li>
                <li><a onclick="select(event, 'dropdown-identitas','jenis-identitas', '2')" class="dropdown-item" href="#">Passport</a></li>
                <li><a onclick="select(event, 'dropdown-identitas','jenis-identitas', '3')" class="dropdown-item" href="#">Lainnya</a></li>
            </ul>
        </div>
        <input name="jenis_identitas" id="jenis-identitas" value="" type="hidden"/>
    </div>
    <div class="form-group my-3 col-12 col-sm-12 col-md-12 col-lg-6">
        <label class="mandatory text-base font-semibold" for="id-pendaftar">NIK/Passport</label>
        <input value="{{ auth()->user()->nik }}" type="text" class="form-control border-secondary" id="id-pendaftar"
            name="id_pendaftar" placeholder="NIK/Pasport">
    </div>
</div>

<div class="row my-0 py-0">
    <div class="form-group my-3 col-12 col-sm-12 col-md-12 col-lg-6">
        <label class="mandatory text-base font-semibold">Nama Lengkap</label>
        <input value="{{ auth()->user()->fullname }}" type="text" class="form-control border-secondary"
            id="nama-lengkap" name="nama_lengkap" placeholder="Nama Lengkap">
    </div>
    <div class="form-group my-3 col-12 col-sm-12 col-md-12 col-lg-6">
        <label class="mandatory text-base font-semibold" for="id-pendaftar">Lampiran</label>
        <input class="form-control" type="file" id="formFile" disabled>
    </div>
</div>


<script>
    function select(event, callerId, inputId, value) {
        const caller = document.getElementById(callerId);
        const input = document.getElementById(inputId);

        caller.textContent = event.target.textContent;
        input.value = value;
    }
</script>
