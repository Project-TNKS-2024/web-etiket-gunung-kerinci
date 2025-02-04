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
    </style>
</head>

<body>
    <div class="row m-0">
        <div class="col-sm-4 col-md-6 d-none d-sm-block bg-regis">

        </div>
        <div class="col-12 col-sm-8 col-md-6 d-flex align-items-center flex-column">
            <div class="mt-3">
                <a class="mx-auto nav-logo" href="{{ route('homepage.beranda') }}">
                    <img src="{{ asset('assets/img/logo/logo bulat.png') }}" alt="logo">
                    Taman nasional Kerinci Seblat
                </a>
            </div>
            <div class="card border border-0 w-100">
                <div class="card-body">
                    <h5 class="text-center index-t1">Selamat Datang!</h5>
                    <h5 class="text-center fw-bold">Pendaftaran Akun Pendaki</h5>
                    <form method="POST" action="{{ route('register.action') }}" class="index-t2 mt-4">
                        @csrf
                        <div class="row mb-3">
                            <div class="form-group col-12 ">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror"
                                    id="email" name="email" value="{{ old('email') }}" required>
                                @error('email')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="form-group col-6">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" class="form-control @error('password') is-invalid @enderror"
                                    id="password" name="password" required>
                                @error('password')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                            <div class="form-group col-6">
                                <label for="password_confirmation" class="form-label">Ulangi Password</label>
                                <input type="password" class="form-control" id="password_confirmation"
                                    name="password_confirmation" required>
                            </div>

                            <script>
                                function addVisibilityToggle(id) {
                                    const element = document.getElementById(id);

                                    if (!element) {
                                        console.error(`Element with ID "${id}" not found.`);
                                        return;
                                    }

                                    const parentElement = document.createElement('div');
                                    parentElement.classList.add('position-relative', 'w-100');
                                    element.parentNode.insertBefore(parentElement, element);
                                    parentElement.appendChild(element);

                                    const iconContainer = document.createElement('div');
                                    iconContainer.classList.add('position-absolute', 'align-items-center', 'd-flex', 'px-2');
                                    iconContainer.style.width = 'fit-content';
                                    iconContainer.style.cursor = 'pointer';
                                    iconContainer.style.height = '100%';
                                    iconContainer.style.background = 'transparent';
                                    iconContainer.style.top = '0';
                                    iconContainer.style.right = '0';

                                    // const icon = document.createElement('img');
                                    // icon.src = "{{ asset('assets/icon/tnks/eye.svg') }}";
                                    // icon.style.cursor = 'pointer';

                                    // iconContainer.appendChild(icon);
                                    iconContainer.innerHTML = `
        <div class="position-relative d-flex align-items-center justify-content-center">
            <img src="{{ asset('assets/icon/tnks/eye.svg') }}" />
            <div id='${id}-toggler' class='position-absolute'
                style='width: 100%; height: 90%; border-right: 2px solid rgba(152, 162, 179, 1); transform: rotate(-45deg) scale(0); top: 35%; left: -30%; transition: height 0.3s ease, transform 0.3s ease;'>
            </div>
        </div>
        `;


                                    iconContainer.addEventListener('click', function(e) {

                                        const toggler = document.getElementById(id + '-toggler');
                                        console.log(element.type);
                                        // element.type = element.type == 'password' ? 'text' : 'password';
                                        if (element.type == 'password') {
                                            element.type = 'text';
                                            toggler.style.transform = 'rotate(-45deg) scale(1)';
                                        } else {
                                            element.type = 'password'
                                            toggler.style.transform = 'rotate(-45deg) scale(0)';
                                        }
                                    });
                                    parentElement.appendChild(iconContainer);
                                }
                                addVisibilityToggle('password');
                                addVisibilityToggle('password_confirmation');
                            </script>
                        </div>
                        <!-- <div class="form-group mb-3">
                            <label class="form-label d-block">Jenis Kelamin</label>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="gender" id="male" value="Laki-Laki" required>
                                <label class="form-check-label" for="male">
                                    Laki-Laki
                                </label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="gender" id="female" value="Perempuan" required>
                                <label class="form-check-label" for="female">
                                    Perempuan
                                </label>
                            </div>
                        </div> -->
                        <div class="form-group mb-3">
                            <button type="submit" class="btn btn-primary w-100">Buat Akun</button>
                        </div>
                        <div class="form-group mb-3">
                            <a href="#" class="btn btn-outline-secondary w-100">Daftar Dengan Akun Google</a>
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
</body>

</html>