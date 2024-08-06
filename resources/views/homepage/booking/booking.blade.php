@extends('homepage.template.index')


@section('css')
<style>
    .pdf-container {
        max-width: 700px;
        width: 100%;
    }

    .header-bg {
        position: relative;
        background: url("{{ asset('assets/img/bg/title-header-bg.png') }}") no-repeat;
        background-size: cover;
        background-position: 50% 50%;
        color: white;
    }


    .header-bg::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.6);
        /* Adjust the alpha value for the desired opacity */
        z-index: 1;
    }

    .header-content {
        position: relative;
        z-index: 2;
    }

    .border-between {
        border-top: 2px solid white;
        width: 50px;
        margin: 20px 0;
    }
</style>
@endsection

@section('main')
@include('homepage.template.header', [
'title' => 'Pendakian Gunung Kerinci',
'caption' => '.',
])
<div class="container my-5">
    <div class="row">
        <div class="col-12 col-sm-12 col-lg-7 ">
            <div id="carouselExample" class="carousel slide">
                <div class="carousel-inner">

                    @foreach ($gambar as $g)
                    <div class="carousel-item {{ $loop->index == 0 ? 'active' : ''}}">
                        <img src="{{ url('/').'/'.$g->src }}" class="d-block w-100" style=";object-fit: cover;height: 480px;" alt="...">
                    </div>
                    @endforeach
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
                    <a href="{{route('homepage.bookingpaket',['id' => $p->id])}}" style="display: table;" class="btn btn-primary ms-auto">Pesan</a>
                </div>
            </div>
            @endforeach





        </div>
    </div>
</div>

@endsection

@section('js')

@endsection