<div class="card mb-3 useradmin shadow rounded-2xl h-full" style="min-height: 550px;">
    <div class="w-100 bg-linear-gradient-primary rounded-xl" style="max-height: 141px; height: 100%;"></div>

    <!-- Bagian Profile Picture dan Nama -->
    <div class="d-flex flex-column align-items-center gap-2" style="margin-top: -50px;">
        <div class="gk-bg-neutrals200"
            style="border-radius: 100%; width: 100px; height: 100px; filter: drop-shadow(0px 0px 3px var(--neutrals600));">
            <img src="{{ asset('assets/img/dashboard/Ellipse 143.png') }}" width="100" />
        </div>
        <div class="py-0 my-0 px-4 w-100 text-center">
            <h4 class="fw-semibold ">{{ auth()->user()->fullname }}</h4>
            <h6 class="fw-light ">{{ auth()->user()->email }}</h6>
        </div>
    </div>

    <!-- Bagian Link Navigasi -->
    <div class="d-flex flex-column gap-2 px-4 w-100 h-100 pb-4 mt-3">
        <a href="{{ route('user.dashboard') }}" id="dashboard"
            class="dashboard-sidebar-btn rounded-lg">Dashboard</a>
        <a href="{{ route('user.dashboard.profile') }}" id="dashboard-profile"
            class="dashboard-sidebar-btn rounded-lg">Profile</a>
        <a href="{{ route('user.dashboard.reiwayat') }}" id="dashboard-profile"
            class="dashboard-sidebar-btn rounded-lg">Riwayat Booking</a>

        <a href="{{ route('user.dashboard.reset-password') }}" id="dashboard-password"
            class="dashboard-sidebar-btn rounded-lg">Ubah Password</a>

        <!-- Bagian Logout -->
        <form action="{{ route('etiket.auth.logout') }}" class="mt-auto" method="post">
            @csrf
            <button type="submit"
                class="btn close rounded-lg w-100 bg-linear-gradient-danger py-2">Logout</button>
        </form>
    </div>
</div>