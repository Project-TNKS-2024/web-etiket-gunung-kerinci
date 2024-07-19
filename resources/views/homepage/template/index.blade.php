<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <title>{{ config('app.name', 'Laravel') }}</title>
    <link rel="stylesheet" href="{{ asset('assets/img/logo/logo bulat.png') }}" />

    <link rel="stylesheet" href="{{ asset('bootstrap-5.3.3-dist/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('bootstrap-5.3.3-dist/font/bootstrap-icons.min.css') }}">
    <link rel="stylesheet" href="{{ asset('fontawesome-free-6.5.2-web/css/all.css') }}">
    <link rel="stylesheet" href="{{ asset('componen/colorplate.css') }}">
    <link rel="stylesheet" href="{{ asset('componen/tailwind-classes.css') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        .border-between {
            border-top: 2px solid white;
            width: 50px;
            margin: 20px 0;
        }

        .poppins-thin {
            font-family: "Poppins", sans-serif;
            font-weight: 100;
            font-style: normal;
            a
        }

        .poppins-extralight {
            font-family: "Poppins", sans-serif;
            font-weight: 200;
            font-style: normal;
        }

        * {
            font-family: "Poppins", sans-serif;
        }

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

        .useradmin .btn {
            border: 2px solid var(--primary100);
        }

        .useradmin .btn.active {
            border: 2px solid var(--primary) !important;
        }

        .useradmin .btn.close {
            border: none;
            background-color: var(--error200) !important;
            color: white;
        }

        .bg-linear-gradient-primary {
            background: linear-gradient(263deg, #0169BF 12.63%, #63B8FF 80.63%);
            color: #fff;
            border: none;
            padding: 10px 20px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
        }

        .bg-linear-gradient-danger {
            background: linear-gradient(263deg, #da260e 12.63%, #FF6363 80.63%);
            color: #fff;
            border: none;
            padding: 10px 20px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
        }


        .dashboard-sidebar-btn {
            background: none;
            color: #000;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
        }

        .dashboard-sidebar-btn.close {
            background-color: var(--error200) !important;
            color: white;
        }

        .dashboard-sidebar-btn.active {
            background: linear-gradient(263deg, #0169BF 12.63%, #63B8FF 80.63%);
            color: #fff;
        }

        .dashboard-sidebar-btn {
            background: var(--neutrals100);
            color: var(--base-black);
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
        }

        .dashboard-sidebar-btn:hover {
            background: var(--neutrals200);
        }

        .dashboard-sidebar-btn.active:hover {
            background: linear-gradient(263deg, #0169BF 12.63%, #63B8FF 80.63%);
        }

        .toast-container {
            position: fixed;
            top: 1rem;
            right: 1rem;
            z-index: 1050;
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
    <script src="{{ asset('bootstrap-5.3.3-dist/js/bootstrap.bundle.min.js') }}"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.7/dist/umd/popper.min.js"></script>

    @include('homepage.template.modal-notif')

    @yield('js')

</body>

</html>