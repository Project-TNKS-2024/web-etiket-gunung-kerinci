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

                @foreach ($bookings as $booking)
                @include('etiket.user.sections.tiket', ['booking'=>$booking])
                @endforeach
                @if (count($bookings) == 0)
                <div>
                    <p class="gk-text-neutrals700 text-center">Tidak ada tiket yang aktif</p>
                </div>
                @endif

            </div>
        </div>
    </div>
</div>
@endsection




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