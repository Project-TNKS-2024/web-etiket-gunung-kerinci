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
                        <input value="{{ $user->firstName }}" type="text" class="form-control border-secondary"
                            id="nama-depan" name="firstName" placeholder="Nama Depan">
                    </div>
                    <div class="form-group my-3 col-12 col-sm-12 col-md-12 col-lg-6">
                        <label class="mandatory text-base font-semibold">Nama Belakang</label>
                        <input value="{{ $user->lastName }}" type="text" class="form-control border-secondary"
                            id="nama-belakang" name="lastName" placeholder="Nama Belakang">
                    </div>
                </div>
                <div class="row">
                    <div class="form-group my-3 col-12 col-sm-12 col-md-12 col-lg-6">
                        <label class="mandatory text-base font-semibold">Kewarganegaraan</label>
                        <div class="dropdown">
                            <button
                                class="w-100 btn btn-outline-secondary dropdown-toggle d-flex justify-content-between align-items-center"
                                id="dropdown-kewarganegaraan" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                {{$user->kewarganegaraan}}
                            </button>

                            <ul class="dropdown-menu w-100 shadow">
                                <li><a onclick="selectKwn(event, 'dropdown-kewarganegaraan','kewarganegaraan', 'WNA')" class="dropdown-item" href="#">Warga Negara Indonesia (WNA)</a></li>
                                <li><a onclick="selectKwn(event, 'dropdown-kewarganegaraan','kewarganegaraan', 'WNI')" class="dropdown-item" href="#">Warga Negara Asing WNI</a></li>
                            </ul>
                        </div>
                        <input name="kewarganegaraan" id="kewarganegaraan" value="{{$user->kewarganegaraan}}" type="hidden" />
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
                                <button
                                    class="h-100 btn btn-outline-secondary dropdown-toggle d-flex justify-content-between align-items-center"
                                    id="dropdown-country" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">

                                    <!-- <img src="{{ asset('assets/img/logo/id-flag.png') }}" width="20" /> -->
                                    {{$user->telp_country}}
                                </button>

                                <div class="dropdown-menu" aria-labelledby="dropdown-identitas">
                                    <div class="position-fixed w-100 *: " style="width:fit-content;">
                                        <input type="text" class="form-control rounded-sm" id="cariTelepon"
                                            placeholder="Cari Negara">
                                    </div>
                                    <div id="telepon-item"
                                        style="overflow-y:auto; width: fit-content; max-height: 200px; overflow-x:hidden; margin-top:40px;">
                                    </div>
                                </div>
                            </div>
                            <input value="{{ $user->no_hp }}" type="text" class="form-control border-secondary "
                                id="nomor-telepon" name="nomor_telepon" placeholder="Nama Telepon">
                            <input type="hidden" name="telp_country" value="{{$user->telp_country}}" id="telp_country">
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

@endsection




@section('js')
<!-- script dropdown no telp -->
@include('etiket.user.template.profile.flags-script')

<!-- script dropdown kewarganegaraan -->
<script>
    function selectKwn(event, callerId, inputId, value) {
        const caller = document.getElementById(callerId);
        const input = document.getElementById(inputId);

        caller.textContent = event.target.textContent;
        input.value = value;
    }
</script>

@endsection