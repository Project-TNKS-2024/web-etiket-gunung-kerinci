@php
$destinasi = App\Models\destinasi::all();
$bookingRoute = '';

foreach ($destinasi as $d) {
$bookingRoute .= route('homepage.booking.destinasi.paket', ['id' => $d->id]).", ";
$bookingRoute .= route('homepage.booking.destinasi.paket.tiket', ['id' => $d->id]).", ";
}
@endphp



<nav class="navbar navbar-expand-sm gk-bg-primary700 index-navbar py-0" style="font-size: 14px">
    <div class="w-100 index-nav-ats gk-bg-neutrals-base-white py-2">
        <div class="container d-flex justify-content-between align-items-center">
            <a href="{{ route('homepage.beranda') }}" class="d-flex align-items-center" style="gap: 10px;">
                <img src="{{ asset('assets/img/logo/logo bulat.png') }}" alt="">
                <div>Taman Nasional Kerinci Seblat</div>
            </a>
            <!-- <div class="d-none d-sm-flex align-items-center ">
                <select class="form-select border-0 form-select-sm w-100" style="background-color: transparent"
                    aria-label="Small select example">
                    <option value="id" selected>Pilih Bahasa : Indonesia</option>
                    <option value="en">Select Language: English</option>
                </select>
            </div> -->
        </div>
    </div>
    <div class="container">
        <div></div>
        <button class="navbar-toggler index-nav-toggle" type="button" data-bs-toggle="collapse"
            data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false"
            aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse py-2" id="navbarSupportedContent">

            <ul class="navbar-nav me-auto mb-2 mb-sm-0 d-flex align-items-center align-items-md-center">

                <li class="nav-item ">
                    <a href="{{ route('homepage.beranda') }}" id="navigasi0"
                        class="nav-link text-center text-white rounded-4 py-2 py-md-0 px-2 px-md-2 px-sm-1 mx-2"
                        style="cursor: pointer; font-size: 14px;" aria-current="page" data-route="[{{ route('homepage.beranda') }}]">
                        Beranda
                    </a>
                </li>
                <li class="nav-item ">
                    <a href="{{ route('homepage.sop') }}" id="navigasi1"
                        class="nav-link text-center text-white rounded-4 py-2 py-md-0 px-2 px-md-2 px-sm-1 mx-2"
                        style="cursor: pointer; font-size: 14px;" aria-current="page" data-route="[{{ route('homepage.sop') }}]">
                        SOP Pendakian
                    </a>
                </li>
                <li class="nav-item ">
                    <a href="{{ route('homepage.panduan') }}" id="navigasi2"
                        class="nav-link text-center text-white rounded-4 py-2 py-md-0 px-2 px-md-2 px-sm-1 mx-2"
                        style="cursor: pointer; font-size: 14px;" aria-current="page" data-route="[{{ route('homepage.panduan') }}]">
                        Panduan Booking
                    </a>
                </li>
                <li class="nav-item ">
                    <a href="{{ route('homepage.booking.destinasi.list') }}" id="navigasi3"
                        class="nav-link text-center text-white rounded-4 py-2 py-md-0 px-2 px-md-2 px-sm-1 mx-0 mx-md-2"
                        style="cursor: pointer; font-size: 14px;" aria-current="page" data-route="[{{ route('homepage.booking.destinasi.list') }}, {{$bookingRoute}}]">
                        Booking Online
                    </a>
                </li>


            </ul>

            <ul class="navbar-nav mb-2 mb-sm-0 d-flex align-items-center ">
                @guest
                <!-- If the user is not logged in -->
                <li class="nav-item" style="height: fit-content">
                    <a class="nav-link text-white py-2 py-md-0 gk-bg-primary600 rounded-4 px-3 my-1 my-md-0"
                        style="background: rgba(255, 255, 255, 0.16); font-size: 14px;" aria-current="page"
                        href="{{ route('register') }}" style="height: fit-content;">Register</a>
                </li>
                <li class="nav-item ">
                    <a class="nav-link text-white py-2 py-md-0 gk-bg-primary600 rounded-4 px-3 ms-0 ms-md-3 my-1 my-md-0"
                        style="background: rgba(255, 255, 255, 0.16); font-size: 14px;" aria-current="page"
                        href="{{ route('login') }}">Login</a>
                </li>
                @endguest

                @auth
                <!-- If the user is logged in with the role 'user' -->
                @if (Auth::user()->role == 'user')
                <li class="nav-item ">
                    <a class="nav-link text-white rounded-4 py-2 py-md-0 px-2 px-md-2 px-sm-1 mx-2"
                        aria-current="page" href="{{ route('user.dashboard') }}">Dashboard</a>
                </li>
                @endif
                @if (Auth::user()->role == 'admin')
                <li class="nav-item ">
                    <a class="nav-link text-white rounded-4 py-2 py-md-0 px-2 px-md-2 px-sm-1 mx-2"
                        aria-current="page" href="{{ route('admin.dashboard') }}">Dashboard Admin</a>
                </li>
                @endif
                <div class="dropdown">
                    <span class="profile-container rounded-pill d-flex align-items-center" style="user-select: none; cursor: pointer;" data-bs-toggle="dropdown" aria-expanded="false">
                        <img src="{{ asset('assets/img/dashboard/Ellipse 143.png') }}" style="height: 30px;" />
                    </span>

                    <ul class="dropdown-menu dropdown-menu-end">
                        <!-- Dashboard -->
                        <li>
                            <form action="{{ route('etiket.auth.logout') }}" method="post" class="m-0">
                                @csrf
                                <button class="dropdown-item d-flex align-items-center text-danger" type="submit">
                                    <i class="me-2 bi bi-box-arrow-right"></i> Logout
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>


                @endauth
            </ul>


            <!-- <div class="d-block d-sm-none my-2">
                <select class="form-select form-select-sm" aria-label="Small select example">
                    <option value="id" selected>Pilih Bahasa : Indonesia</option>
                    <option value="en">Select Language: English</option>
                </select>
            </div> -->
        </div>
    </div>
</nav>