@extends('etiket.user.template.index')

@section('sub-css')
@endsection


@section('sub-main')
    <script></script>

    <div class="col py-5 px-4 my-5 my-md-0" style="min-height: 500px; overflow-y: auto;">
        <form class="row rounded-2xl card p-3" method="post" action="#">
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

            @include('etiket.user.sections.profile.form.identitas', [
                'user' => $user,
            ])

            @include('etiket.user.sections.profile.form.telepon', [
                'user' => $user,
            ])

            @include('etiket.user.sections.profile.form.datadiri', [
                'user' => $user,
            ])

            @include('etiket.user.sections.profile.form.alamat', [
                'user' => $user,
            ])

            <div class="w-100 d-flex justify-content-end">
                <button type="submit" class="btn shadow bg-linear-gradient-primary">Simpan</button>
            </div>
        </form>
    </div>

    <script>
        console.log('users: ', @json($user));
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


@push('scripts')
    <!-- Popper.js -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/2.11.7/umd/popper.min.js"
        integrity="sha384-oBqDVmMz4fnFO9gybA6zjzLIpKflFsFVxYycpV5x9W4FUIsxAB7BJ4Vf8pZFOFpg" crossorigin="anonymous">
    </script>
    <!-- Bootstrap JS -->
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/5.1.3/js/bootstrap.min.js"
        integrity="sha384-QJHtvGhmr9b0Lr1Bt6f3B6md62z0vT4Xdb1BzVg20r13a6c4a21fF3+k9J7l6+dB" crossorigin="anonymous">
    </script>
@endpush

@section('js')
@endsection
