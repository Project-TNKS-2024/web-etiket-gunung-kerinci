@extends('homepage.template.index')

@section('css')
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

@section('js')
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>

<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.7/dist/umd/popper.min.js"></script>
<script src="{{ asset('bootstrap-5.3.3-dist/js/bootstrap.min.js') }}"></script>

@stack('scripts')
@endsection