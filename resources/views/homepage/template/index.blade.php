<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <title>{{ config('app.name', 'Laravel') }}{{ isset($titlePage) ? ' - '.$titlePage : '' }}</title>
    <link rel="shortcut icon" type="image/png" href="{{ asset('assets/icon/tnks.png') }}" />

    <link rel="stylesheet" href="{{ asset('assets/icon/tnks.png') }}" />
    <!-- style -->
    <link rel="stylesheet" href="{{ asset('bootstrap-5.3.3-dist/css/bootstrap.min.css') }}">
    <!-- icon -->
    <link rel="stylesheet" href="{{ asset('bootstrap-5.3.3-dist/font/bootstrap-icons.min.css') }}">
    <link rel="stylesheet" href="{{ asset('fontawesome-free-6.5.2-web/css/all.css') }}">
    <!-- color -->
    <link rel="stylesheet" href="{{ asset('componen/colorplate.css') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap"
        rel="stylesheet">
    <!-- <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> -->
    <style>
        * {
            font-family: "Poppins", sans-serif;
        }

        body {
            background-color: rgba(245, 246, 250, 1);
            min-height: 100vh;
            display: grid;
            grid-template-rows: auto 1fr auto;
        }

        .btn-gl-primary:hover {
            background: linear-gradient(263deg, #63B8FF 12.63%, #0169BF 80.63%);
            background-size: 500%;
            color: white;
            border: none;
        }


        /* header homepage */
        .header-bg {
            position: relative;
            background: url("{{ asset('assets/img/bg/title-header-bg.png') }}") no-repeat;
            background-size: cover;
            background-position: 50% 50%;
            color: white;
        }

        @media (max-width: 425px) {
            #navbarSupportedContent .navbar-nav .nav-item {
                width: -webkit-fill-available;
            }
        }

        .header-bg::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.6);
            /* Adjust the alpha value for the desired opacity */
            z-index: 1;
        }

        .header-content {
            position: relative;
            z-index: 2;
        }

        /* navbar */
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

        .navbar-nav .nav-link.active {
            background-color: #fff;
            color: var(--primary700);
        }

        .navbar-nav .nav-link:hover {
            color: var(--secondary) !important;
        }

        /* .nav-link */

        /* body homepage detail card destinasi */
        .index-text-cardDestinasi {
            display: -webkit-box;
            -webkit-box-orient: vertical;
            overflow: hidden;
            text-overflow: ellipsis;
            line-clamp: 2;
            -webkit-line-clamp: 2;
        }

        .index-text-cardDestinasi span,
        .index-text-cardDestinasi strong {
            background-color: transparent !important;
            font-size: 16px !important;
        }

        /* footer homepage */
        footer {
            background-color: white;
        }

        .index-footer {
            box-shadow: 0 -4px 6px rgba(0, 0, 0, 0.1);
        }


        /* sidebar admin user */

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
            background: linear-gradient(263deg, #63B8FF 12.63%, #0169BF 80.63%);
            background-size: 500%;
            color: white;
            border: none;
        }

        /* template toast */
        .toast-container {
            position: fixed;
            top: 1rem;
            right: 1rem;
            z-index: 1050;
        }


        .btn-gl-primary {
            background: linear-gradient(263deg, #0169BF 12.63%, #63B8FF 80.63%);
            color: #fff;
            border: none;
            background-size: 100%;

        }
    </style>

    @yield('css')

</head>

<body>


    @include('homepage.template.navbar')

    <main>
        @include('etiket.auth.template.script-password')
        @yield('main')
    </main>

    @include('homepage.template.footer')

    @yield('modal')

    <!-- <script src="{{ asset('bootstrap-5.3.3-dist/js/bootstrap.js') }}"></script> -->
    <script src="{{ asset('bootstrap-5.3.3-dist/js/bootstrap.bundle.js') }}"></script>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.7/dist/umd/popper.min.js"></script> -->

    @include('homepage.template.modal-notif')

    <!-- script navigasi navbar homepage -->
    <script>
        const navItems = document.querySelectorAll(".nav-link");

        const currentUrl = window.location.href;
        // Remove 'active' class from all links
        navItems.forEach((item) => {
            item.classList.remove('active');
            // Check if the current URL matches the link's href
            if (currentUrl.includes(item.getAttribute("href"))) {
                item.classList.add('gk-text-primary700');
                item.classList.add('active');
                item.classList.remove('text-white');
            }
        });
    </script>

    <!-- script untuk generate input -->
    <script src="{{ asset('componen/generateInput.js') }}"></script>

    @yield('js')

</body>

</html>