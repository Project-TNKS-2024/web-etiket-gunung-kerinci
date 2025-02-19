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

        a {
            text-decoration: none;
        }

        .bg-regis {
            background-image: url("{{ asset('assets/img/bg/bg register.png') }}");
            background-size: cover;
            min-height: 100vh;
        }

        .index-t1 {
            color: var(--neutrals600);
            font-size: 16px;
        }

        .index-t2 {
            color: var(--neutrals600);
            font-size: 14px;
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

        /* googke */
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
</head>

<body>
    <div class="row m-0">
        <div class="col-sm-4 col-md-6 d-none d-sm-block bg-regis">

        </div>
        <div class="col-12 col-sm-8 col-md-6 d-flex align-items-center flex-column">
            <div class="mt-3">
                <a class="mx-auto nav-logo" href="{{ route('homepage.beranda') }}">
                    <img src="{{ asset('assets/icon/tnks.png') }}" alt="logo">
                    Taman nasional Kerinci Seblat
                </a>
            </div>
            <div class="card border border-0 w-100">
                <div class="card-body">
                    <h5 class="text-center index-t1">Selamat Datang!</h5>
                    <h5 class="text-center fw-bold">Pendaftaran Akun Pendaki</h5>
                    <form method="POST" action="{{ route('register.action') }}" class="index-t2 mt-4">
                        @csrf
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" value="{{ old('email') }}" required>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <div class="input-group">
                                <input type="password" class="form-control" id="password" name="password" required>
                                <span class="input-group-text btn-visibility" data-target='password'><i class="fa-solid fa-eye"></i> </i></span>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="password_confirmation" class="form-label">Ulangi Password</label>
                            <div class="input-group">
                                <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
                                <span class="input-group-text btn-visibility" data-target='password_confirmation'><i class="fa-solid fa-eye"></i> </i></span>
                            </div>
                        </div>
                        <div class="form-group mb-3">
                            <button type="submit" class="btn btn-primary w-100">Buat Akun</button>
                        </div>
                        <div class="form-group mb-3">
                            <a href="{{ route('oauth.google') }}" class="btn w-100 d-flex align-items-center justify-content-center google-btn">
                                <img src="{{ asset('assets/icon/google.png') }}" alt="Google Logo" class="me-2" width="20">
                                Daftar Dengan Akun Google
                            </a>
                        </div>
                        <div class="form-group mb-3 text-center">
                            <p>Sudah Punya Akun? <a href="{{ route('login') }}">Log in</a></p>
                        </div>
                    </form>
                </div>
            </div>
            <footer class="text-center mt-auto">
                <p class="index-t2">© Taman Nasional Kerinci Seblat 2024</p>
            </footer>
        </div>
    </div>

    <script src="{{ asset('bootstrap-5.3.3-dist/js/bootstrap.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.7/dist/umd/popper.min.js"></script>
    @include('homepage.template.modal-notif')
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