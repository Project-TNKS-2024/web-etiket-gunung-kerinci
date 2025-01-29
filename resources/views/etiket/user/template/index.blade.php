@extends('homepage.template.index')

@section('css')
<link rel="stylesheet" href="{{ asset('componen/tailwind-classes.css') }}">
<style>
    .dashboard-sidebar.accessories {
        height: 141px;
    }

    .dashboard-sidebar {
        /* min-width: 338px; */
        min-height: 500px;
    }


    @media (max-width: 768px) {
        .dashboard-sidebar {
            min-height: 500px;
        }
    }

    label.mandatory::after {
        content: " *";
        color: red;
    }

    .custom-dropdown-item {
        width: fit-content;
        /* Set your desired width here */
    }

    .border-secondary {
        border-color: var(--neutrals500)
    }
</style>

@yield('sub-css')
@endsection

@section('main')
<main class="container-fluid gk-bg-neutrals100 px-md-5" style="overflow: hidden">

    <div class="row mx-auto justify-content-center py-4" style="min-height: 500px;">

        <div class="col-12 col-sm-12 col-md-5 col-lg-3 dashboard-sidebar">
            @include('etiket.user.template.sidebar')
        </div>

        <div class="col-12 col-sm-12 col-md-7 col-lg-9 mt-4 mt-md-0">
            @yield('sub-main')
        </div>
    </div>
</main>
@endsection

<!-- menu active -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        let menuDFash = document.querySelectorAll('.dashboard-sidebar-btn');
        menuDFash.forEach(element => {
            if (window.location.href === element.href) {
                element.classList.add('active');
            } else {
                element.classList.remove('active');
            }
        });
    });
</script>


@section('js')
<script src="https://cdn.rawgit.com/davidshimjs/qrcodejs/gh-pages/qrcode.min.js"></script>
<script>
    const qrcodes = document.querySelectorAll('.qrcode_kodebooking');
    qrcodes.forEach(e => {
        qr = e.dataset['qr'];
        new QRCode(e, {
            text: qr,
            width: 200,
            height: 200
        });
    });
</script>

@endsection