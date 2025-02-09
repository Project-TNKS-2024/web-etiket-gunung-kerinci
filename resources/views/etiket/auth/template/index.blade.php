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
    <link rel="stylesheet" href="{{ asset('fontawesome-free-6.5.2-web/css/all.css') }}">
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

        .google-btn {
            height: 40px;
            background-color: #ffffff;
            border: 1px solid #dadce0;
            border-radius: 4px;
            padding: 0 12px;
            transition: background-color 0.2s;
        }

        .google-btn:hover {
            background-color: #f8f9fa;
            border-color: #dadce0;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
        }

        .google-btn:active {
            background-color: #f1f3f4;
        }

        .google-icon {
            width: 18px;
            height: 18px;
            margin-right: 8px;
        }

        .btn-text {
            color: #3c4043;
            font-family: "Google Sans", Roboto, Arial, sans-serif;
            font-size: 14px;
            font-weight: 500;
            letter-spacing: 0.25px;
        }

        .google-btn img {
            width: 20px;
            height: 20px;
        }
    </style>
    @yield('css')
</head>

<body>
    <div style="width: 100%;margin-top:30px;position: fixed;">
        <a class="mx-auto nav-logo d-block mx-auto" href="{{ route('homepage.beranda') }}">
            <img src="{{ asset('assets/icon/tnks.png') }}" alt="logo">
            Taman nasional Kerinci Seblat
        </a>
    </div>
    <div class="container" style="height: 100vh;display: flex;flex-direction: column; align-items: center;justify-content: center;">
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
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const btnVisibilityPassword = document.querySelectorAll(".btn-visibility");
            btnVisibilityPassword.forEach(btn => {
                btn.addEventListener("click", function() {
                    const ipt = document.getElementById(btn.dataset.target);
                    if (ipt.type === "password") {
                        ipt.type = "text";
                        btn.innerHTML = '<i class="fa-solid fa-eye-slash"></i>';
                    } else {
                        ipt.type = "password";
                        btn.innerHTML = '<i class="fa-solid fa-eye"></i>';
                    }
                });
            });
        });
    </script>
</body>

</html>