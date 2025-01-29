@extends('etiket.user.template.index')

@section('sub-css')
<style>
    .card-tiket .status-tiket {
        font-weight: semibold;
    }

    .card-tiket .status-tiket span {
        font-weight: semibold;
    }

    .card-tiket .tiket-detail input {
        border: none;
        padding-left: 0px;
    }

    .card-tiket .tiket-qr img {
        margin: auto;
    }
</style>
@endsection


@section('sub-main')
<div class="card shadow">
    <div class="card-body">
        <div class="container-fluid">
            <h4 class="font-semibold">Riwayat Booking</h4>
            <div class="card-container mb-3" style="overflow-x: auto; white-space: nowrap;">

                @include('homepage.template.tiket.ListCardTiket', ['bookings' => $bookings])

            </div>
        </div>
    </div>
</div>
@endsection