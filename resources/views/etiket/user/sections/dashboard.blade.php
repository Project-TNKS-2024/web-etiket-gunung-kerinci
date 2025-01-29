@extends('etiket.user.template.index')

@section('sub-css')
<style>
    .card-tiket table tr td {
        margin: 0px;
        padding: 4px;
    }

    .card-tiket .tiket-qr img {
        margin: auto;
    }
</style>
@endsection


@section('sub-main')


<!-- notif jika belum verif biodata -->
<div>
    @if ($user->biodata == null or $user->biodata->verified != 'verified')
    <div class="alert alert-warning" role="alert">
        Anda belum menyelesaikan verifikasi biodata anda. Silahkan lengkapi biodata anda. Silahkan klik <a href="{{ route('user.dashboard.profile') }}">disini</a> untuk melengkapi biodata anda.
    </div>
    @endif
</div>

<!-- 
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
</div> -->

<div class="card shadow">
    <div class="card-body">
        <div class="container-fluid">
            <h4 class="font-semibold">Booking</h4>

            <div class="card-container mb-3" style="overflow-x: auto; white-space: nowrap;">
                @include('homepage.template.tiket.ListCardTiket', ['bookings' => $bookings])
            </div>
            <!-- slide vcard -->
        </div>
    </div>
</div>
@endsection