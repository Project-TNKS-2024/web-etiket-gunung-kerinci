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
            <h5 class="mb-3 fw-bold">Pilih Jenis Paket {{ $gunung->nama }}</h5>

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

    <article class="my-5" style="text-align: justify">
        <h5 class="fw-bold"> Wisata Gunung Kerinci: Keindahan Alam Tertinggi di Sumatera</h5>
        <section>
            Gunung Kerinci, yang terletak di Provinsi Jambi, adalah gunung tertinggi di Sumatera dan gunung berapi
            tertinggi di Indonesia dengan ketinggian mencapai 3.805 meter di atas permukaan laut. Gunung ini berada
            dalam kawasan Taman Nasional Kerinci Seblat (TNKS), yang merupakan salah satu situs warisan dunia UNESCO.
            Destinasi wisata ini terkenal dengan panorama alamnya yang memukau, keanekaragaman hayati, dan tantangan
            pendakiannya.
        </section>

        <ol class="px-3">
            <dl>
                <dt class="mt-3">
                    <h5 class="fw-bold">
                        <li>Keindahan Alam dan Pemandangan</li>
                    </h5>
                </dt>
                <dd>
                    <section>
                        Gunung Kerinci menawarkan pemandangan yang luar biasa indah, mulai dari lembah hijau yang subur,
                        perkebunan teh Kayu Aro yang membentang luas, hingga hamparan hutan tropis yang masih sangat
                        alami. Dari puncaknya, pendaki bisa menikmati panorama matahari terbit dengan latar belakang
                        Samudra Hindia di sisi barat dan Danau Gunung Tujuh yang eksotis di sisi timur.
                    </section>
                </dd>
                <dt class="mt-3">
                    <h5 class="fw-bold">
                        <li>Aktivitas Pendakian</li>
                    </h5>
                </dt>
                <dd>
                    <section>
                        Pendakian Gunung Kerinci adalah daya tarik utama bagi para pecinta alam dan petualangan. Rute
                        pendakian biasanya dimulai dari Desa Kersik Tuo. Pendakian membutuhkan waktu sekitar 2-3 hari
                        tergantung pada kondisi cuaca dan stamina pendaki. Rute ini memberikan pengalaman melewati hutan
                        tropis yang lebat, lengkap dengan flora dan fauna endemik seperti harimau Sumatera dan burung
                        rangkong.
                        Tips Pendakian:
                        <ul>
                            <li>Pastikan kondisi fisik dalam keadaan prima.</li>
                            <li>
                                Gunakan perlengkapan pendakian yang memadai, seperti jaket tebal, sepatu gunung, dan lampu
                                senter.
                            </li>
                            <li>Ikuti panduan dari pemandu lokal untuk menjaga keselamatan.</li>
                        </ul>
                    </section>
                </dd>
                <dt class="mt-3">
                    <h5 class="fw-bold">
                        <li>Keanekaragaman Hayati</li>
                    </h5>
                </dt>
                <dd>
                    <section>
                        Sebagai bagian dari TNKS, Gunung Kerinci memiliki kekayaan flora dan fauna yang luar biasa.
                        Kawasan ini merupakan habitat bagi berbagai spesies langka, termasuk harimau Sumatera, badak
                        Sumatera, serta beragam jenis burung dan tanaman endemik seperti bunga Rafflesia dan bunga
                        bangkai (Amorphophallus titanum).
                    </section>
                </dd>
                <dt class="mt-3">
                    <h5 class="fw-bold">
                        <li>Destinasi Wisata Sekitar</li>
                    </h5>
                </dt>
                <dd>
                    <section>
                        Selain mendaki Gunung Kerinci, pengunjung juga bisa mengeksplorasi berbagai destinasi wisata
                        lain di sekitarnya, seperti:
                        <ul>
                            <li>Danau Gunung Tujuh: Danau vulkanik tertinggi di Asia Tenggara.</li>
                            <li>Perkebunan Teh Kayu Aro: Salah satu perkebunan teh tertua dan tertinggi di dunia.</li>
                            <li>Air Terjun Telun Berasap: Air terjun megah yang dikelilingi hutan tropis.</li>
                        </ul>
                    </section>
                </dd>
                <dt class="mt-3">
                    <h5 class="fw-bold">
                        <li>Akses Menuju Gunung Kerinci</li>
                    </h5>
                </dt>
                <dd>
                    <section>
                        Untuk mencapai Gunung Kerinci, pengunjung bisa terbang ke Bandara Sultan Thaha di Jambi atau
                        Minangkabau di Padang, lalu melanjutkan perjalanan darat ke Desa Kersik Tuo. Perjalanan ini
                        memakan waktu sekitar 6-8 jam, tetapi pemandangan sepanjang jalan cukup menakjubkan.
                    </section>
                </dd>
            </dl>
        </ol>
    </article>
</div>
@endsection

@section('js')
@endsection