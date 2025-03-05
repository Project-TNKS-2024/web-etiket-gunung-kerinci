@extends('homepage.template.index')


@section('css')
<style>

</style>
@endsection

@section('main')
@include('homepage.template.header', [
'title' => 'Pendakian '.$gunung->nama,
'caption' => '',
])
<div class="container my-5">
    <div class="row">
        <div class="col-12">
            <div id="carouselExample" class="carousel slide">
                <div class="carousel-inner">
                    @forelse ($gambar as $g)
                    <div class="carousel-item {{ $loop->index == 0 ? 'active' : '' }}">
                        <img src="{{ url('/') . '/' . $g->src }}" class="d-block w-100 shadow-3 rounded border"
                            style="object-fit: inherit;height: 500px; object-position: 0% 0%" alt="...">
                    </div>
                    @empty
                    <div class="carousel-item active">
                        <img src="{{ asset('assets/img/sampel/sampel 2.png') }}" class="d-block w-100"
                            style="object-fit: cover;height: 480px;" alt="...">
                    </div>
                    @endforelse
                </div>
                <button class="carousel-control-prev" type="button" data-bs-target="#carouselExample"
                    data-bs-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Previous</span>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#carouselExample"
                    data-bs-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Next</span>
                </button>
            </div>
        </div>
        <div class="my-3 col-12">
            <h5 class="mb-3 fw-bold">Paket Wisata Pendakian {{ $gunung->nama }}</h5>

            <div class="row g-3">
                @foreach ($paket as $p)
                <div class="col-12 col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title fw-semibold">{{ strtoupper($p->nama) }}</h5>
                            <p class="card-text">{{ $p->keterangan }}</p>
                            <a href="{{ route('homepage.booking.destinasi.paket.tiket', ['id' => $p->id]) }}"
                                style="display: table;"
                                class="btn btn-primary gk-bg-primary300 border-0 gk-text-primary900 fw-semibold px-4 ms-auto">Pesan</a>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    <article class="mt-2 mb-5" style="text-align: justify">
        {!! $gunung->detail !!}
    </article>
</div>
@endsection

@section('js')
@endsection