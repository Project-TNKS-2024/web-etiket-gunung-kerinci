<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <title>{{ config('app.name', 'Laravel') }}</title>
    <link rel="shortcut icon" type="image/png" href="{{ asset('assets/icon/tnks.png') }}" />
    <link rel="stylesheet" href="{{ asset('modernize/css/styles.css') }}" />
    <link rel="stylesheet" href="{{ asset('componen/tailwind-classes.css') }}" />
    <link rel="stylesheet" href="{{ asset('componen/colorplate.css') }}" />
    <link rel="stylesheet" href="{{ asset('fontawesome-free-6.5.2-web/css/all.css') }}">

    <!-- Bootstrap CSS -->

    <!-- Bootstrap JS -->


    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap"
        rel="stylesheet">

    <style>
        .logo-img {
            text-decoration: none;
            color: black;
            font-weight: bold;
            font-size: 20px;
            font-family: sans-serif;
        }

        .logo-img img {
            width: 40px;
            margin-right: 5px;
        }

        * {
            font-family: "Poppins", sans-serif;
        }

        .gradient-top {
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 25%;
            /* Covering the top 25% */
            background: linear-gradient(to top, rgba(0, 0, 0, .5), transparent);
            pointer-events: none;
            /* Ensure it doesn't block interactions with the img */
        }

        .lis-collapse-destinasi {
            background-color: #d4e3f6;
            border-radius: 0 0 10px 10px;
        }

        .body-wrapper {
            display: flex;
            flex-direction: column;
        }

        .borderx {
            border-color: var(--neutrals500);
        }

        .toast-container {
            position: fixed;
            top: 1rem;
            right: 1rem;
            z-index: 1050;
        }

        .sidebar-nav ul .sidebar-item .sidebar-link span {
            width: 24px;
            justify-content: center;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .card .card-header {
            background-color: var(--bs-primary);
        }

        .card .card-header * {
            color: white;
        }

        .body-wrapper .table tr td .btn {
            padding: 0.5rem 0.5rem;
        }
    </style>

    @yield('css')
</head>

<body>

    <!--  Body Wrapper -->
    <div class="page-wrapper w-100" id="main-wrapper" data-layout="vertical" data-navbarbg="skin6"
        data-sidebartype="full" data-sidebar-position="fixed" data-header-position="fixed">
        <!-- ========================================= -->
        @include('etiket.admin.template.sidebar')
        <!--  Main wrapper -->
        <div class="body-wrapper " style="background-color: #d3d6df; min-height: 100vh;">
            <!--  Header Start -->
            <!-- ================================================ -->
            @include('etiket.admin.template.navbar')
            <!--  Header End -->
            <div class="container-fluid mx-3 my-3" style="width: -webkit-fill-available; max-width: none; flex:1;">
                <!--  Row 1 -->
                @yield('main')


            </div>
            <!-- footer  -->
            <!-- ============================================== -->
            @include('etiket.admin.template.footer')
        </div>
    </div>
    <script src="{{ asset('modernize/libs/jquery/dist/jquery.min.js') }}"></script>
    <script src="{{ asset('modernize/libs/bootstrap/dist/js/bootstrap.bundle.min.js') }}"></script>

    <script src="{{ asset('modernize/js/sidebarmenu.js') }}"></script>
    <script src="{{ asset('modernize/js/app.min.js') }}"></script>

    @yield('js')


    @include('etiket.admin.template.modal-notif')

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

</body>

</html>