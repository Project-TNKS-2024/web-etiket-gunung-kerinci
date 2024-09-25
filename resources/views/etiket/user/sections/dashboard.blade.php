@extends('etiket.user.template.index')

@section('sub-css')
@endsection


@section('sub-main')

<div class="row">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-warning shadow h-100">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col-auto">
                        <img src="{{asset('assets/icon/tnks/compass-dark.svg')}}" width="40" />
                    </div>
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                            Explorasi Selesai</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">18</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card shadow">
    <div class="card-body">
        <div class="container-fluid">
            <h4 class="font-semibold">Riwayat Booking</h4>

            <!-- slide vcard -->
        </div>
    </div>
</div>
@endsection

@section('js')
<script>
    let countriesData = [];

    const sidebarMenu = document.querySelectorAll(".dashboard-sidebar-btn");
    sidebarMenu.forEach((o, i) => {
        sidebarMenu[i].classList.remove("active");
    });
    const profile = document.querySelector("#dashboard");
    profile.classList.add("active");
</script>
@endsection