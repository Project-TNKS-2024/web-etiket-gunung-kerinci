@extends('homepage.template.index')


@section('css')
<style>

</style>
@endsection

@section('main')
@include('homepage.template.header', [
'title' => 'Pendakian Gunung Kerinci',
'caption' => '',
])
<div class="container my-5">
    <div class="row">
        <div class="col-12 col-sm-12 col-lg-7 ">
            <div id="carouselExample" class="carousel slide">
                <div class="carousel-inner">
                    @forelse ($gambar as $g)
                    <div class="carousel-item {{ $loop->index == 0 ? 'active' : ''}}">
                        <img src="{{ url('/').'/'.$g->src }}" class="d-block w-100" style="object-fit: cover;height: 480px;" alt="...">
                    </div>
                    @empty
                    <div class="carousel-item active">
                        <img src="{{ asset('assets/img/sampel/sampel 2.png') }}" class="d-block w-100" style="object-fit: cover;height: 480px;" alt="...">
                    </div>
                    @endforelse
                </div>
                <button class="carousel-control-prev" type="button" data-bs-target="#carouselExample" data-bs-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Previous</span>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#carouselExample" data-bs-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Next</span>
                </button>
            </div>
        </div>
        <div class="col-12 col-sm-12 col-lg-5 mt-3 mt-lg-0">

            <h4 class="mb-4">Pilih Paket Pendakian {{$gunung->nama}}</h4>

            @foreach ($paket as $p)
            <div class="card mb-3">
                <div class="card-body">
                    <h5 class="card-title fw-bold">{{$p->nama}}</h5>
                    <p class="card-text">{{$p->keterangan}}</p>
                    <a href="{{route('homepage.booking.destinasi.paket.tiket',['id' => $p->id])}}" style="display: table;" class="btn btn-primary ms-auto">Pesan</a>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>

@endsection

@section('js')

@endsection