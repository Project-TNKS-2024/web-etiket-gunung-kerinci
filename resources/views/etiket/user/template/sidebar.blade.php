<div class="card mb-3 useradmin shadow rounded-2xl h-full" style="min-height: 550px;">
    <div class="w-100 bg-linear-gradient-primary rounded-xl" style="max-height: 141px; height: 100%;"></div>

    <!-- Bagian Profile Picture dan Nama -->
    <div class="d-flex flex-column align-items-center gap-2" style="margin-top: -50px;">
        <div class="gk-bg-neutrals200 position-relative"
            style="border-radius: 100%; width: 100px; height: 100px; filter: drop-shadow(0px 0px 3px var(--neutrals600));">
            <img src="{{ auth()->user()->avatar == null ? asset('assets/icon/user.svg') : asset(auth()->user()->avatar) }}"
                width="100" height="100" style="object-fit: cover;" class="rounded-pill" />

            <!-- Pencil icon for editing -->
            <form action="{{ route('user.dashboard.akun.action') }}" method="post" id="form-profile-sidebar"
                enctype="multipart/form-data"
                class="{{ isset($user->biodata) && $user->biodata->verified !== 'unverified' ? 'input-none' : '' }}">
                @csrf

                <label href="#" for="avatar-sidebar"
                    class="btn btn-primary border-0 bg-white  position-absolute bottom-0 end-0 rounded-circle p-1 "
                    style=" display: flex; align-items: center; justify-content: center; box-shadow: 0 0 5px rgba(0,0,0,0.2); ">
                    <i class="fas fa-pencil-alt"
                        style="cursor: pointer; color: var(--primary700);font-size: 20px; padding: 5px;"></i>
                </label>

                <input class="form-control border-secondary d-none" type="file" name="avatar" id="avatar-sidebar"
                    accept="image/*">
            </form>
        </div>

        <div class="py-0 my-0 px-4 w-100 text-center">
            @if (isset(auth()->user()->biodata) and auth()->user()->biodata->verified == 'verified')
                <h5 class="fw-semibold ">{{ auth()->user()->biodata->first_name }}</h5>
                <h6 class="fw-light ">Id : {{ auth()->user()->biodata->id }}</h6>
            @else
                <h6 class="fw-light ">{{ auth()->user()->email }}</h6>
            @endif
        </div>
    </div>

    <!-- Bagian Link Navigasi -->
    <div class="d-flex flex-column gap-2 px-4 w-100 h-100 pb-4 mt-3">
        <a href="{{ route('user.dashboard') }}" id="dashboard" class="dashboard-sidebar-btn rounded-lg">Dashboard</a>
        <a href="{{ route('user.dashboard.profile') }}" id="dashboard-profile"
            class="dashboard-sidebar-btn rounded-lg">Profile</a>
        <a href="{{ route('user.dashboard.reiwayat') }}" id="dashboard-profile"
            class="dashboard-sidebar-btn rounded-lg">Riwayat Pemesanan</a>
        <a href="{{ route('user.dashboard.akun') }}" id="dashboard-profile"
            class="dashboard-sidebar-btn rounded-lg">Akun</a>

        @if (auth()->user()->gauth_type == 'manual')
            <a href="{{ route('user.dashboard.reset-password') }}" id="dashboard-password"
                class="dashboard-sidebar-btn rounded-lg">Ubah Kata Sandi</a>
        @endif

        <!-- Bagian Logout -->
        <form action="{{ route('etiket.auth.logout') }}" class="mt-auto" method="post">
            @csrf
            <button type="submit" class="btn close rounded-lg w-100 bg-linear-gradient-danger py-2">Keluar</button>
        </form>
    </div>
</div>
