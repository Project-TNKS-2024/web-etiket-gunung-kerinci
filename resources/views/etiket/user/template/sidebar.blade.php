<div class="card mb-3 useradmin position-relative shadow rounded-2xl h-full"
    style="min-height: 550px;">
    <div class="position-absolute w-100 bg-linear-gradient-primary rounded-xl" style="max-height: 141px; height:100%;">
    </div>
    <div class="position-absolute w-100 h-100 d-flex flex-column align-items-center gap-2" style="top:0;left:0;">
        <div class="gk-bg-neutrals200"
            style="border-radius: 100%; width: 100px; min-height:100px; margin-top: 90px;filter: drop-shadow(0px 0px 3px var(--neutrals600))">
            <img src="{{ asset('assets/img/dashboard/Ellipse 143.png') }}" width="100" />
        </div>
        <h4 class="fw-semibold py-0 my-0">{{ auth()->user()->fullname }}</h4>
        <h6 class="fw-light py-0 my-0">{{ auth()->user()->email }}</h6>
        <div class="d-flex flex-column gap-2 px-4 w-100 h-100 pb-4 ">
            <a href="{{ route('user.dashboard') }}" id="dashboard"
                class="dashboard-sidebar-btn rounded-lg">Dashboard</a>
            <a href="{{ route('user.dashboard.profile') }}" id="dashboard-profile"
                class="dashboard-sidebar-btn rounded-lg">Profile</a>
            <a href="{{ route('user.dashboard.riwayat') }}" id="dashboard-riwayat"
                class="dashboard-sidebar-btn rounded-lg">Riwayat Booking</a>
            <a href="{{ route('user.dashboard.reset-password') }}" id="dashboard-password"
                class="dashboard-sidebar-btn rounded-lg">Ubah Password</a>

            <form action="{{ route('etiket.in.logout') }}" class="mt-auto" method="post">
                @csrf
                <button href="{{ route('user.dashboard.reset-password') }}"
                    class="btn close rounded-lg w-100 bg-linear-gradient-danger">Logout</button>
            </form>
        </div>
    </div>
</div>