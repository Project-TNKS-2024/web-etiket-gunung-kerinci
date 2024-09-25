@extends('etiket.user.template.index')

@section('sub-css')
@endsection


@section('sub-main')

<div class="card shadow">
    <div class="card-body">
        <div class="container-fluid">
            <h4 class="font-semibold">Profile</h4>
            <form action="{{route('user.dashboard.action', ['id' => $user->id])}}" method="post" action="#">
                @csrf
                <div class="row">
                    <div class="form-group my-3 col-12 col-sm-12 col-md-12 col-lg-6">
                        <label class="mandatory text-base font-semibold">Nama Depan</label>
                        <input value="{{ $user->nama_depan }}" type="text" class="form-control border-secondary"
                            id="nama-depan" name="nama_depan" placeholder="Nama Depan">
                    </div>
                    <div class="form-group my-3 col-12 col-sm-12 col-md-12 col-lg-6">
                        <label class="mandatory text-base font-semibold">Nama Belakang</label>
                        <input value="{{ $user->nama_belakang }}" type="text" class="form-control border-secondary"
                            id="nama-belakang" name="nama_belakang" placeholder="Nama Belakang">
                    </div>
                </div>
                <div class="row">
                    <div class="form-group my-3 col-12 col-sm-12 col-md-12 col-lg-6">
                        <label class="mandatory text-base font-semibold">Kewarganegaraan</label>
                        <div class="dropdown">
                            <button
                                class="w-100 btn btn-outline-secondary dropdown-toggle d-flex justify-content-between align-items-center"
                                id="dropdown-kewarganegaraan" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                WNI
                            </button>

                            <ul class="dropdown-menu w-100 shadow">
                                <li><a onclick="select(event, 'dropdown-kewarganegaraan','kewarganegaraan', 'WNI')" class="dropdown-item" href="#">WNA</a></li>
                                <li><a onclick="select(event, 'dropdown-kewarganegaraan','kewarganegaraan', 'WNA')" class="dropdown-item" href="#">WNI</a></li>
                            </ul>
                        </div>
                        <input name="kewarganegaraan" id="kewarganegaraan" value="" type="hidden" />
                    </div>
                    <div class="form-group my-3 col-12 col-sm-12 col-md-12 col-lg-6">
                        <label class="mandatory text-base font-semibold" for="id-pendaftar">NIK/Passport</label>
                        <input value="{{ $user->nik }}" type="text" class="form-control border-secondary" id="id-pendaftar"
                            name="nik" placeholder="NIK/Pasport">
                    </div>
                </div>
                <div class="row">
                    <div class="form-group my-3 col-12 col-sm-12 col-md-12 col-lg-6">
                        <label class="mandatory text-base font-semibold">Email</label>
                        <fieldset disabled>
                            <input value="{{ $user->email }}" type="text" class="form-control border-secondary"
                                id="email" name="email" readonly>
                        </fieldset>
                    </div>
                    <div class="form-group my-3 col-12 col-sm-12 col-md-12 col-lg-6">
                        <label class="mandatory text-base font-semibold">Nomor Telepon</label>
                        <div class="d-flex gap-2">
                            <div class="dropdown custom-dropdown-item">
                                <button type="button"
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
                </div>


                <!-- --------------------------------------------------------------- -->
                <div class="row mb-2">
                    <div class="col d-flex justify-content-end">

                        <button type=" submit" class="btn shadow bg-linear-gradient-primary">Simpan</button>
                    </div>
                </div>

            </form>
        </div>
    </div>
</div>
@include('etiket.user.template.profile.flags-script')

@endsection




@section('js')

<!-- script dropdown kewarganegaraan -->
<script>
    function select(event, callerId, inputId, value) {
        const caller = document.getElementById(callerId);
        const input = document.getElementById(inputId);

        caller.textContent = event.target.textContent;
        input.value = value;
    }
</script>

<script>
    let countriesData = [];
    const sidebarMenu = document.querySelectorAll(".dashboard-sidebar-btn");
    sidebarMenu.forEach((o, i) => {
        sidebarMenu[i].classList.remove("active");
    });
    const profile = document.querySelector("#dashboard-profile");
    profile.classList.add("active");
</script>
@endsection