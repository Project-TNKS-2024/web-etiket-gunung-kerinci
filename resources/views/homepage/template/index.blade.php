<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <title>{{ config('app.name', 'Laravel') }}</title>

    <link rel="stylesheet" href="{{ asset('bootstrap-5.3.3-dist/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('bootstrap-5.3.3-dist/font/bootstrap-icons.min.css') }}">
    <link rel="stylesheet" href="{{ asset('componen/colorplate.css') }}">

    <style>
        .index-navbar {
            flex-wrap: wrap !important;
        }

        .index-nav-ats img {
            width: 50px;
        }

        .index-nav-ats a {
            text-decoration: none;
            color: black;
            font-size: 18px;
            font-weight: bold;
        }

        .index-nav-toggle {
            border: none;
            box-shadow: none !important;
        }

        .index-footer {
            box-shadow: 0 -4px 6px rgba(0, 0, 0, 0.1);
        }

        .index-footer img {
            height: 100px;
        }

        .index-footer h5 {
            font-size: 18px;
            font-weight: bold;
        }

        .index-footer h4 {
            font-weight: bold;
        }

        .index-footer a {
            text-decoration: none;
            color: var(--neutrals500);
        }

        .index-footer .copyright {
            color: var(--neutrals500);
            font-family: 'bootstrap-icons';

        }
    </style>

    @yield('css')

</head>

<body>


    @include('homepage.template.navbar')

    @yield('main')

    @include('homepage.template.footer')

    @yield('modal')

    <script src="{{ asset('bootstrap-5.3.3-dist/js/bootstrap.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.7/dist/umd/popper.min.js"></script>

    @yield('js')

</body>

</html>
