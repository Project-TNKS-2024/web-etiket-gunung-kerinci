@extends('homepage.template.index')

@section('css')
    <style>
        .dashboard-sidebar.accessories {
            height: 141px;
        }

        .dashboard-sidebar {
            min-width: 338px;
            min-height: 500px;
        }


        @media (max-width: 768px) {
            .dashboard-sidebar {
                height: 500px;
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
    {{-- <main class="container content my-4" style="">
        <div class="container-fluid p-0">

            <div class="row " style="min-height: 500px;height:100%;">
                <div class="col col-md-5 col-xl-4 dashboard-sidebar">
                    @include('etiket.user.template.sidebar')
                </div>

                <div class="col-md-7 col-xl-8 my-5 my-sm-5 my-md-0 my-lg-0"
                    style="min-height: 500px; max-height: 100%; overflow-y: auto;">
                    @yield('sub-main')
                </div>
            </div>
        </div>
    </main> --}}
    <main class="container-fluid gk-bg-neutrals100 px-5" style="overflow: hidden">

        <div class="row mx-auto justify-content-center" style="min-height: 500px;">
            <div class="col-12 col-sm-12 col-md-3 dashboard-sidebar py-5" style="">
                @include('etiket.user.template.sidebar')
            </div>

            @yield('sub-main')
        </div>
    </main>
@endsection

@section('js')
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.7/dist/umd/popper.min.js"></script>
    <script src="{{ asset('bootstrap-5.3.3-dist/js/bootstrap.min.js') }}"></script>


    <script>
        $(document).ready(function() {
            $('#identitas-dropdown').dropdown();
        });
    </script>
@endsection
