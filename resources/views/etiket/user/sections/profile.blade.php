@extends('etiket.user.template.index')

@section('sub-css')

<style>
    .form-group {
        margin-bottom: 10px;
    }

    .input-none input,
    .input-none select,
    .input-none .dropdown-notelp {
        pointer-events: none;
        background-color: #e9ecef;
        opacity: 1;
    }

    .input-none .iptFile-label {
        display: block;
    }

    .iptFile-label {
        display: none;
    }

    .input-none .iptFile-input {
        display: none;
    }
</style>
@endsection


@section('sub-main')

<div class="card shadow">
    <div class="card-body">
        <div class="container-fluid">
            <h4 class="font-semibold">Profile</h4>
            <!-- biodata->verified = unverified, peding, verified -->
            <form action="{{ route('user.dashboard.action') }}" method="post" id="form-profile" enctype="multipart/form-data" class="{{ isset($user->biodata) && $user->biodata->verified !== 'unverified' ? 'input-none' : '' }}">

                @if (isset($user->biodata)and $user->biodata->verified == 'pending')
                <div class="alert alert-warning" role="alert">
                    Profil anda sedang dalam verifikasi admin. verifikasi setiap hari pada pukul 08.00 sampai 16.00 WIB pada hari kerja.
                </div>
                @endif
                @if (isset($user->biodata)and $user->biodata->verified == 'unverified')
                <div class="alert alert-danger" role="alert">
                    Profile anda tidak valid
                </div>
                <div class="alert alert-danger" role="alert">
                    Admin : {{$user->biodata->keterangan}}
                </div>
                @csrf
                @else

                @csrf
                @endif
                <div class="row">
                    <!-- First Name -->
                    <div class="form-group col-12 col-md-6">
                        <label class="mandatory font-semibold">Nama Depan</label>
                        <input value="{{ old('firstName', isset($user->biodata->first_name) ? $user->biodata->first_name : null) }}" type="text" class="form-control border-secondary" id="nama-depan" name="firstName" placeholder="Nama Depan">
                    </div>

                    <!-- Last Name -->
                    <div class="form-group col-12 col-md-6">
                        <label class="mandatory font-semibold">Nama Belakang</label>
                        <input value="{{ old('lastName', isset($user->biodata->last_name) ? $user->biodata->last_name : null) }}" type="text" class="form-control border-secondary" id="nama-belakang" name="lastName" placeholder="Nama Belakang">
                    </div>

                    <div class="form-group col-12 iptFile-input">
                        <label for="lampiran_identitas" class="w-100 fw-bold mandatory">Lampiran Identitas</label>
                        <div class="input-group flex-nowrap">
                            <input class="form-control border-secondary" type="file" name="lampiran_identitas" id="lampiran_identitas" accept="image/*,.pdf">
                            @if (isset($user->biodata) && isset($user->biodata->lampiran_identitas) && file_exists(public_path($user->biodata->lampiran_identitas)))
                            <input type="hidden" value="{{asset( $user->biodata->lampiran_identitas)}}" id="lampiran_identitas_existing">
                            @endif
                            <button class="input-group-text d-none border-secondary" type="button" data-id-target="lampiran_identitas">
                                <i class=" fa-regular fa-eye"></i>
                            </button>
                        </div>
                        <span class="keterangan" style="font-size: 12px;">Lampiran KTP, Max 500kb</span>
                    </div>
                    <div class="form-group col-12 iptFile-label">
                        <label for="lampiran_identitas" class="w-100 fw-bold mandatory">Lampiran Identitas</label>
                        <input value="{{ $user->biodata->lampiran_identitas ? $user->biodata->lampiran_identitas : null }}" type="text" class="form-control border-secondary">
                        <span class="keterangan" style="font-size: 12px;">Lampiran KTP, Max 500kb</span>
                    </div>

                    <!-- Nationality -->
                    <div class="form-group col-12 col-md-6">
                        <label class="mandatory font-semibold">Kewarganegaraan</label>
                        <select class="form-control border-secondary" name="kewarganegaraan" id="kewarganegaraan">
                            <option value="wni" {{ old('kewarganegaraan', isset($user->biodata->kenegaraan) && $user->biodata->kenegaraan == 'wni' ? 'selected' : '') }}>Warga Negara Indonesia</option>
                            <option value="wna" {{ old('kewarganegaraan', isset($user->biodata->kenegaraan) && $user->biodata->kenegaraan == 'wna' ? 'selected' : '') }}>Warga Negara Asing</option>
                        </select>
                    </div>

                    <!-- NIK/Passport -->
                    <div class="form-group col-12 col-md-6">
                        <label class="mandatory font-semibold" for="id-pendaftar">NIK/Passport</label>
                        <input value="{{ old('nik', isset($user->biodata->nik) ? $user->biodata->nik : null) }}" type="text" class="form-control border-secondary" id="id-pendaftar" name="nik" placeholder="NIK/Pasport">
                    </div>

                    <!-- Email -->
                    <div class="form-group col-12 col-md-6">
                        <label class="mandatory font-semibold">Email</label>
                        <fieldset disabled>
                            <input value="{{ old('email', isset($user->email) ? $user->email : null) }}" type="text" class="form-control border-secondary" id="email" name="email" readonly>
                        </fieldset>
                    </div>

                    <!-- Phone Number -->
                    <div class="form-group col-12 col-md-6">
                        <label class="mandatory font-semibold">Nomor Telepon</label>
                        <div class="d-flex gap-2">
                            <div class="dropdown custom-dropdown-item dropdown-notelp">
                                <button class="h-100 btn btn-outline-secondary dropdown-toggle d-flex justify-content-between align-items-center" id="dropdown-country" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    {{ old('telp_country', isset($user->biodata->telp_country) ? $user->biodata->telp_country : null) }}
                                </button>

                                <div class="dropdown-menu">
                                    <div class="position-fixed w-100" style="width:fit-content;">
                                        <input type="text" class="form-control rounded-sm" id="cariTelepon" placeholder="Cari Negara">
                                    </div>
                                    <div id="telepon-item" style="overflow-y:auto; max-height: 200px; margin-top:40px;"></div>
                                </div>
                            </div>
                            <input value="{{ old('nomor_telepon', isset($user->biodata->no_hp) ? $user->biodata->no_hp : null) }}" type="text" class="form-control border-secondary" id="nomor-telepon" name="nomor_telepon" placeholder="Nomor Telepon">
                            <input type="hidden" name="telp_country" value="{{ old('telp_country', isset($user->biodata->telp_country) ? $user->biodata->telp_country : null) }}" id="telp_country">
                        </div>
                    </div>

                    <!-- Gender -->
                    <div class="form-group col-12 col-md-6">
                        <label class="font-semibold mandatory">Jenis Kelamin</label>
                        <select class="form-control form-control border-secondary" name="jenis_kelamin" id="jenis_kelamin">
                            <option value="" disabled selected> -- Jenis Kelamin -- </option>
                            <option value="l" {{ old('jenis_kelamin', isset($user->biodata->jenis_kelamin) && $user->biodata->jenis_kelamin == 'l' ? 'selected' : '') }}>Laki-Laki</option>
                            <option value="p" {{ old('jenis_kelamin', isset($user->biodata->jenis_kelamin) && $user->biodata->jenis_kelamin == 'p' ? 'selected' : '') }}>Perempuan</option>
                        </select>
                    </div>

                    <!-- Date of Birth -->
                    <div class="form-group col-12 col-md-6">
                        <label for="tanggal_lahir" class="font-semibold">Tanggal Lahir</label>
                        <input type="date" class="form-control border-secondary" name="tanggal_lahir" id="tanggal_lahir" value="{{ old('tanggal_lahir', isset($user->biodata->tanggal_lahir) ? Carbon\Carbon::parse($user->biodata->tanggal_lahir)->format('Y-m-d') : null) }}">
                    </div>
                </div>

                <div class="row">
                    <label class="font-semibold mandatory">Alamat Domisili</label>
                </div>

                <div class="row mb-3">
                    <!-- Province and City -->
                    <div class="col-12 col-md-6">
                        <div class="row">
                            <div class="form-group col-12 col-md-6">
                                <label for="provinsi" class="w-100">Provinsi</label>
                                <select class="form-control ipt-provinsi border-secondary" name="provinsi" id="provinsi" data-index="1">
                                    <option value="{{ old('provinsi', isset($user->biodata->provinsi) ? $user->biodata->provinsi : 0) }}" selected>Pilih Provinsi</option>
                                </select>
                            </div>
                            <div class="form-group col-12 col-md-6">
                                <label for="kabupaten_kota" class="w-100">Kabupaten/Kota</label>
                                <select class="form-control border-secondary ipt-kabupaten-kota" name="kabupaten_kota" id="kabupaten_kota" data-index="1">
                                    <option value="{{ old('kabupaten_kota', isset($user->biodata->kabupaten) ? $user->biodata->kabupaten : 0) }}" selected>Pilih Kabupaten/Kota</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Subdistrict and Village -->
                    <div class="col-12 col-md-6">
                        <div class="row">
                            <div class="form-group col-12 col-md-6">
                                <label for="kecamatan" class="w-100">Kecamatan</label>
                                <select class="form-control border-secondary ipt-kecamatan" name="kecamatan" id="kecamatan" data-index="1">
                                    <option value="{{ old('kecamatan', isset($user->biodata->kec) ? $user->biodata->kec : 0) }}" selected disabled>Pilih Kecamatan</option>
                                </select>
                            </div>
                            <div class="form-group col-12 col-md-6">
                                <label for="desa_kelurahan" class="w-100">Desa/Kelurahan</label>
                                <select class="form-control border-secondary ipt-desa-kelurahan" name="desa_kelurahan" id="desa_kelurahan" data-index="1">
                                    <option value="{{ old('desa_kelurahan', isset($user->biodata->desa) ? $user->biodata->desa : 0) }}" selected>Pilih Desa/Kelurahan</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Submit Button -->
                <div class="row mb-2">
                    <div class="col d-flex justify-content-end">
                        @if ((!isset($user->biodata))or (isset($user->biodata) and ($user->biodata->verified == 'unverified')) )
                        <button type="submit" class="btn border-0 bg-linear-gradient-primary" name="action" value="verifikasi">Verifikasi Profile</button>
                        @elseif(isset($user->biodata) and $user->biodata->verified == 'verified')
                        <button type="button" class="btn border-0 bg-linear-gradient-primary" name="action" onclick="updateProfile(this)">Update Profile</button>
                        <div id="button-update" class="d-none">
                            <button type="submit" class="btn border-0 bg-linear-gradient-primary" name="action" value="update">Verifikasi Profile</button>
                            <button type="button" id="button-cancel" class="btn border-0 bg-linear-gradient-danger" name="action">Batal</button>
                        </div>
                        @endif
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>


<!-- Modal -->
<div class="modal fade" id="ModalShowFile" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5 my-0" id="exampleModalLabel">File Preview</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <embed id="filePreview" src="" type="application/pdf" style="width: 100%;" />
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('js')
<!-- script dropdown no telp -->
<script>
    function cariTelepon(e) {
        const teleponItem = document.querySelectorAll("#telepon-item .dropdown-item");
        teleponItem.forEach((item) => {
            const textContent = item.textContent.toLowerCase();
            const searchValue = e.target.value.toLowerCase();
            item.style.display = textContent.includes(searchValue) ? "block" : "none";
        });
    }

    inputDropdownCountry = document.getElementById('telp_country');
    btnDropdownCountry = document.getElementById('dropdown-country');

    function selectCountry(img, code) {

        // Set country code value in the hidden input
        inputDropdownCountry.value = code;

        // Update dropdown button with the selected flag
        btnDropdownCountry.textContent = "";
        btnDropdownCountry.appendChild(img.cloneNode(true));
        btnDropdownCountry.appendChild(document.createTextNode(code));
    }

    document.addEventListener('DOMContentLoaded', function() {
        document.getElementById('cariTelepon').addEventListener('input', cariTelepon);
        fetch('/assets/js/telepon.json')
            .then(response => response.json())
            .then(countriesData => {
                const defaultCountryCode = inputDropdownCountry.value;
                const teleponItem = document.querySelector('#telepon-item');

                countriesData.forEach((country, index) => {
                    if (country.idd.root) {
                        // cek jika no id tarok di depan prepend()
                        if (country.idd.root + country.idd.suffixes[0] === '+62') {
                            teleponItem.prepend(createCountryButton(country));
                        } else {
                            teleponItem.appendChild(createCountryButton(country));
                        }

                        if (country.idd.root + country.idd.suffixes[0] === defaultCountryCode) {
                            console.log(country.idd);

                            const img = document.createElement('img');
                            img.src = country.flags.png;
                            img.width = 20;
                            img.style.marginRight = '5px';

                            selectCountry(img, defaultCountryCode);
                        }
                    }
                });

            })
            .catch(error => console.error('Error fetching countries:', error));

        function createCountryButton(country) {
            const button = document.createElement('a');
            button.classList.add('dropdown-item');
            button.classList.add('w-100');

            const img = document.createElement('img');
            img.src = country.flags.png;
            img.width = 20;
            img.style.marginRight = '5px';
            button.appendChild(img);
            button.href = "#";

            // Get country code and name
            const code = country.idd.suffixes ? `${country.idd.root}${country.idd.suffixes[0]}` : country.idd.root;
            const text = document.createTextNode(` ${country.cca2} (${code})`);
            button.appendChild(text);

            // Add event listener for selecting country
            button.addEventListener('click', () => selectCountry(img, code));
            return button;
        }

    });
</script>

<!-- script tombol update profile -->
<script>
    function updateProfile(btn) {
        const form = document.getElementById('form-profile');
        const bupdate = document.getElementById('button-update');
        const bcancel = document.getElementById('button-cancel');

        form.classList.remove('input-none');
        bupdate.classList.remove('d-none');
        btn.classList.add('d-none');

        bcancel.addEventListener('click', function() {
            form.classList.add('input-none');
            bupdate.classList.add('d-none');
            btn.classList.remove('d-none');
        });
    }
</script>

<!-- scipt refresh option select domisili -->
@include('homepage.template.script-selectDomisili')


<!-- script modal show file -->
@include('homepage.template.modal-prefiewFile')

@endsection