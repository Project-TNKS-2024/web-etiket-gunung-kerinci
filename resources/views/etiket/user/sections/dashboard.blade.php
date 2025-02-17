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
<div>
    <h5>Selamat Datang</h5>
    <h3><b>{{$user->biodata ? ($user->biodata->verified == 'verified' ? $user->biodata->first_name . ' ' . $user->biodata->last_name : 'Pendaki Handal') : 'Pendaki Handal'}}</b></h3>
</div>


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