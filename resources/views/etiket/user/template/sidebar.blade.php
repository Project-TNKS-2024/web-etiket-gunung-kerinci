
<div class="card mb-3 useradmin overflow-hidden py-0 position-relative shadow rounded-2xl h-full"
    style="min-height: 500px;">
    <div class="position-absolute w-100 bg-linear-gradient-primary rounded-xl" style="max-height: 141px; height:100%;">
    </div>
    <div class="position-absolute w-100 h-100 d-flex flex-column align-items-center gap-2" style="top:0;left:0;">
        <div class="gk-bg-neutrals200"
            style="border-radius: 100%;width: 100px; height:100px;margin-top: 90px;filter: drop-shadow(0px 0px 3px var(--neutrals600))">
            <img src="{{ asset('assets/img/dashboard/Ellipse 143.png') }}" width="100" />
        </div>
        <h4 class="fw-semibold py-0 my-0">Pendaki Handal</h4>
        <h6 class="fw-light py-0 my-0">pendakikerinci@gmail.com</h6>
        <div class="d-flex flex-column gap-2 px-4 w-100">
            <a href="{{ route('user.dashboard') }}" id="dashboard-profile"
                class="dashboard-sidebar-btn rounded-lg">Profile</a>
            <a href="{{ route('user.dashboard.riwayat') }}" id="dashboard-riwayat"
                class="dashboard-sidebar-btn rounded-lg">Riwayat Booking</a>
            <a href="{{ route('user.dashboard.reset-password') }}" id="dashboard-password"
                class="dashboard-sidebar-btn rounded-lg">Ubah Password</a>
        </div>
    </div>
</div>

