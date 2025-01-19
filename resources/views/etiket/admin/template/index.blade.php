<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <title>{{ config('app.name', 'Laravel') }}</title>
    <link rel="shortcut icon" type="image/png" href="{{asset('assets/img/logo/logo bulat.png')}}" />
    <link rel="stylesheet" href="{{asset('modernize/css/styles.css')}}" />
    <link rel="stylesheet" href="{{asset('componen/tailwind-classes.css')}}" />
    <link rel="stylesheet" href="{{asset('componen/colorplate.css')}}" />
    <link rel="stylesheet" href="{{ asset('fontawesome-free-6.5.2-web/css/all.css') }}">

    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">

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
    </style>

    @yield('css')
</head>

<body>



    <div id="modal" class="d-none align-items-center justify-content-start position-fixed top-0 left-0 w-full h-full" style="overflow-x: auto; padding: 0 10%;z-index: 999; background-color: rgba(0,0,0,.2)">
        <img class="position-fixed cursor-pointer" onclick="closeModal()" src="{{asset('assets/icon/tnks/x-light.svg')}}" width="50" style="top: 20px; right: 20px;filter: drop-shadow(0px 0px 10px black)" />
    </div>


    <!--  Body Wrapper -->
    <div class="page-wrapper w-100" id="main-wrapper" data-layout="vertical" data-navbarbg="skin6" data-sidebartype="full" data-sidebar-position="fixed" data-header-position="fixed">
        <!-- ========================================= -->
        @include('etiket.admin.template.sidebar')
        <!--  Main wrapper -->
        <div class="body-wrapper " style="background-color: rgba(245, 246, 250, 1); min-height: 100vh;">
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
    <script src="{{asset('modernize/libs/jquery/dist/jquery.min.js')}}"></script>
    <script src="{{asset('modernize/libs/bootstrap/dist/js/bootstrap.bundle.min.js')}}"></script>

    <script src="{{asset('modernize/js/sidebarmenu.js')}}"></script>
    <script src="{{asset('modernize/js/app.min.js')}}"></script>

    <!-- <script src="{{asset('modernize/libs/simplebar/dist/simplebar.js')}}"></script> -->
    <!-- <script src="{{asset('modernize/js/dashboard.js')}}"></script> -->

    @yield('js')

    @include('etiket.admin.template.modal')
    @include('etiket.admin.template.modal-notif')


    <script>
        function select(event, callerId, inputId, value) {
            const caller = document.getElementById(callerId);
            const input = document.getElementById(inputId);

            caller.textContent = event.target.textContent;
            input.value = value;
        }
    </script>

    <!-- script untuk popper bostrap -->
    <script>
        var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'))
        var popoverList = popoverTriggerList.map(function(popoverTriggerEl) {
            return new bootstrap.Popover(popoverTriggerEl)
        })
    </script>

    <script>
        // script modal foto desrtinasi
        function closeModal() {
            const modal = document.getElementById('modal');
            modal.classList.add("d-none");
            modal.classList.remove("d-flex");

            const imageContainer = document.querySelectorAll(".image-container-in-modal");
            imageContainer.forEach(element => {
                element.remove();
            });
        }

        function openModal(src) {
            const modal = document.getElementById('modal');
            modal.classList.add("d-flex");
            modal.classList.remove("d-none");
            console.log(src);
            src.forEach((image, index) => {
                const div = document.createElement('div');
                div.style.position = "relative";
                div.classList.add("image-container-in-modal", "w-100", "shadow");
                div.innerHTML = `
                    <div class="gradient-top rounded w-100 h-100"></div>
                    <div class="p-3 rounded text-white position-absolute w-100 h-100 d-flex flex-column justify-content-end" style="left: 0; bottom: 0;" >
                        <header class="text-xl font-semibold">${image.nama}</header>
                        <div class="text-lg">${image.detail}</div>
                    </div>
                    <img id="modal-img" style="width:100%;" class="rounded  " src='{{url('')}}/${image.src}' />
                `;
                modal.appendChild(div);

            });

            // modalImg.src = src[0];
        }
    </script>
</body>

</html>