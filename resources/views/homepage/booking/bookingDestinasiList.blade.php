@extends('homepage.template.index')


@section('css')
<style>
    .btn-destinasi {
        display: block;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .btn-destinasi:hover {
        transform: translateY(-10px);
        /* Mengangkat kotak ke atas */
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
        /* Memberikan bayangan */
    }
</style>
@endsection

@section('main')
@include('homepage.template.header', [
'title' => 'Pilih Destinasi',
'caption' => '',
])
<div class="container my-5">
    <div class="row">
        @foreach ($destinasi as $item)
        <div class="col-12 col-md-6 col-lg-4 mb-3">
            <div class="card h-100 btn-destinasi ">
                <a href="{{ route('homepage.booking.destinasi.paket', ['id' => $item->id]) }}"
                    class="text-decoration-none">
                    <div class="position-relative">
                        <div class="position-absolute bottom-0 start-0 w-100 h-50 d-flex justify-content-start align-items-end"
                            style="width: 100px; background: linear-gradient(to top, rgba(0, 0, 0, 1), rgba(0, 0, 0, 0)); pointer-events: none;">
                            <div class="card-body text-white">
                                <h5 class="card-title">{{ $item->nama }}</h5>
                                <p class="card-text index-text-cardDestinasi">
                                    {{ $item->detail }}
                                </p>
                            </div>
                        </div>
                        @if (count($item->gambar_destinasi) > 0)
                        <img src="{{ asset($item->gambar_destinasi[0]->src) }}"
                            class="card-img-top rounded shadow border-0" alt="Jalur Pendakian Kersik Tuo"
                            style="object-fit: cover; height: 400px;">
                        @else
                        <img src="{{ asset('assets/img/sampel/sampel 2.png') }}"
                            class="card-img-top rounded shadow border-0" alt="Default Image"
                            style="object-fit: cover; height: 400px;">
                        @endif
                    </div>


                </a>
            </div>
        </div>
        @endforeach
    </div>
</div>
</div>
@endsection

@section('js')
@endsection