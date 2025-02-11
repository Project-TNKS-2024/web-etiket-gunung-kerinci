@php
$destinasi = App\Models\Destinasi::all();
$bookingRoutes = [];

foreach ($destinasi as $d) {
$bookingRoutes[] = route('homepage.booking.destinasi.paket', ['id' => $d->id]);
$bookingRoutes[] = route('homepage.booking.destinasi.paket.tiket', ['id' => $d->id]);
}
@endphp

<nav class="navbar navbar-expand-sm gk-bg-primary700 index-navbar py-0" style="font-size: 14px">
    <div class="w-100 index-nav-ats gk-bg-neutrals-base-white py-2">
        <div class="container d-flex justify-content-between align-items-center">
            <a href="{{ route('homepage.beranda') }}" class="d-flex align-items-center gap-2">
                <img src="{{ asset('assets/icon/tnks.png') }}" alt="Logo TNKS">
                <div>Taman Nasional Kerinci Seblat</div>
            </a>
        </div>
    </div>

    <div class="container py-2 py-sm-0">
        <button class="navbar-toggler index-nav-toggle" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <!-- <span class="navbar-toggler-icon"></span> -->
            <span><i class="fa-solid fa-bars"></i></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav me-auto mb-2 py-2 mb-sm-0 d-flex align-items-center">
                <li class="nav-item">
                    <a href="{{ route('homepage.beranda') }}" class="nav-link py-2 py-sm-0 px-2 text-white rounded-4 mx-2">Beranda</a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('homepage.panduan') }}" class="nav-link py-2 py-sm-0 px-2 text-white rounded-4 mx-2">Panduan Booking</a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('homepage.booking.destinasi.list') }}" class="nav-link py-2 py-sm-0 px-2 text-white rounded-4 mx-2" data-route="[{{ implode(',', $bookingRoutes) }}]">Booking Online</a>
                </li>
            </ul>

            <ul class="navbar-nav d-flex align-items-center">
                @guest
                <li class="nav-item">
                    <a class="nav-link py-2 py-sm-0 px-2 text-white gk-bg-primary600 rounded-4 px-3" href="{{ route('register') }}">Register</a>
                </li>
                <li class="nav-item ms-md-3">
                    <a class="nav-link py-2 py-sm-0 px-2 text-white gk-bg-primary600 rounded-4 px-3" href="{{ route('login') }}">Login</a>
                </li>
                @endguest

                @auth
                <li class="nav-item">
                    @if (Auth::user()->role == 'user')
                    <a class="nav-link py-2 py-sm-0 px-2 text-white rounded-4 mx-2" href="{{ route('user.dashboard') }}">Dashboard</a>
                    @elseif (Auth::user()->role == 'admin')
                    <a class="nav-link py-2 py-sm-0 px-2 text-white rounded-4 mx-2" href="{{ route('admin.dashboard') }}">Dashboard Admin</a>
                    @endif
                </li>
                <form action="{{ route('etiket.auth.logout') }}" method="post" class="mb-0 w-100 px-3">
                    @csrf
                    <button class="nav-link text-white py-2 bg-danger align-items-center w-100" type="submit">
                        Logout
                        <i class="fa-solid fa-arrow-right-from-bracket"></i>
                    </button>
                </form>


                @endauth
            </ul>
        </div>
    </div>
</nav>