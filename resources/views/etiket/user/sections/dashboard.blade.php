@extends('etiket.user.template.index')

@section('sub-css')
@endsection


@section('sub-main')
<script></script>

<div class="col py-5 px-4 my-5 my-md-0" style="min-height: 700px; overflow-y: auto;">
    <h3 class="font-semibold">Selamat datang di website E-Tiket TNKS, {{auth()->user()->fullname}}</h3>
    <div class="row gap-3 px-2">
        <div class="col-sm-12 col-md-12 col-lg-3 col-12 text-sm p-2 rounded py-3 d-flex gap-3" style="border: 1px solid var(--neutrals300)">
            <div><img src="{{asset('assets/icon/tnks/compass-dark.svg')}}" width="40"/></div>
            <div>
                <div>Explorasi Selesasi</div>
                <div>1 Explorasi</div>
            </div>
        </div>
        <div class="col-sm-12 col-md-12 col-lg-3 col-12 text-sm p-2 rounded py-3 d-flex gap-3" style="border: 1px solid var(--neutrals300)">
            <div><img src="{{asset('assets/icon/tnks/compass-dark.svg')}}" width="40"/></div>
            <div>
                <div>Status Pendaftaran</div>
                <div>1 Explorasi</div>
            </div>
        </div>
        <div class="col-sm-12 col-md-12 col-lg-3 col-12 text-sm p-2 rounded py-3 d-flex gap-3" style="border: 1px solid var(--neutrals300)">
            <div><img src="{{asset('assets/icon/tnks/compass-dark.svg')}}" width="40"/></div>
            <div>
                <div>Hitung Mundur Check-In</div>
                <div>1 Explorasi</div>
            </div>
        </div>
    </div>
    <h4 class="font-semibold mt-4">Riwayat Booking</h4>
    <div class="row">
        {{-- div class col-12 col-sm-12 col-md-12 col-lg-5 gap-1 --}}
    </div>

</div>

    <script>
        console.log('users: ', @json($user));
        let countriesData = [];

        const sidebarMenu = document.querySelectorAll(".dashboard-sidebar-btn");
        sidebarMenu.forEach((o, i) => {
            sidebarMenu[i].classList.remove("active");
        });
        const profile = document.querySelector("#dashboard");
        profile.classList.add("active");
    </script>
@endsection

@push('scripts')
<!-- Popper.js -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/2.11.7/umd/popper.min.js" integrity="sha384-oBqDVmMz4fnFO9gybA6zjzLIpKflFsFVxYycpV5x9W4FUIsxAB7BJ4Vf8pZFOFpg" crossorigin="anonymous">
</script>
<!-- Bootstrap JS -->
<script src="https://stackpath.bootstrapcdn.com/bootstrap/5.1.3/js/bootstrap.min.js" integrity="sha384-QJHtvGhmr9b0Lr1Bt6f3B6md62z0vT4Xdb1BzVg20r13a6c4a21fF3+k9J7l6+dB" crossorigin="anonymous">
</script>
@endpush

@section('js')
@endsection
