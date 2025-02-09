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
    <div style="width: 100%;
   margin-top:30px;
    position: fixed;">
        <a class="mx-auto nav-logo d-block mx-auto" href="{{ route('homepage.beranda') }}">
            <img src="{{ asset('assets/img/logo/logo bulat.png') }}" alt="logo">
            Taman nasional Kerinci Seblat
        </a>
    </div>
    <div class="container"
        style="
    height: 100vh;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;">
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
        </script>
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