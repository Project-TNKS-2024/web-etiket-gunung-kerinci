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
    <link rel="stylesheet" href="{{ asset('componen/tailwind-classes.css') }}">
    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap"
        rel="stylesheet">

    <style>
        * {
            font-family: "Poppins";
        }

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
    <div style="width: 100%; margin-top: 30px;">
        <a class="mx-auto nav-logo d-block" href="{{ route('homepage.beranda') }}">
            <img src="{{ asset('assets/img/logo/logo bulat.png') }}" alt="logo">
            Taman Nasional Kerinci Seblat
        </a>
    </div>
    @yield('main')

    @yield('js')
    <script src="{{ asset('bootstrap-5.3.3-dist/js/bootstrap.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.7/dist/umd/popper.min.js"></script>
</body>

</html>
