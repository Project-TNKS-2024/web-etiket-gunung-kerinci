<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <title>{{ config('app.name', 'Laravel') }}</title>

    <link rel="stylesheet" href="{{ asset('bootstrap-5.3.3-dist/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('bootstrap-5.3.3-dist/font/bootstrap-icons.min.css') }}">
    <link rel="stylesheet" href="{{ asset('componen/colorplate.css') }}">
    <style>
        .nav-logo img {
            width: 50px;
        }

        .nav-logo {
            font-weight: bold;
            text-align: center;
            color: black;
        }

        body {
            /* height: 100vh; */
            background-image: url("{{ asset('assets/img/bg/bg login.png') }}");
            background-size: cover;
        }

        a {
            text-decoration: none;
        }
    </style>
    @yield('css')
</head>

<body>
    <div style="width: 100%;
   margin-top:30px;
    position: fixed;">
        <a class="mx-auto nav-logo d-block mx-auto" href="{{ route('homepage.beranda') }}">
            <img src="{{ asset('assets/img/logo/logo bulat.png') }}" alt="logo">
            Taman nasional Kerinci Seblat
        </a>
    </div>
    <div class="container" style="
    height: 100vh;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;">
        <div class="row justify-content-center w-100">
            <div class=" col-md-6">
                <div class="card border border-0 shadow-lg px-4 py-2">
                    @yield('main')
                </div>
            </div>
        </div>
    </div>

    @include('homepage.template.modal-notif')

    @yield('js')
    <script src="{{ asset('bootstrap-5.3.3-dist/js/bootstrap.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.7/dist/umd/popper.min.js"></script>
</body>

</html>