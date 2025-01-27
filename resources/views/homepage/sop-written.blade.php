@extends('homepage.template.index')



@section('css')
<style>
    .chapter-header {
        margin: 25px 0;
        text-align: center;
        font-weight: 600;
    }

    .chapter-subheader {
        font-weight: 700;
        font-size: 16px;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif
    }

    .chapter-content {
        font-size: 16px;
        margin-left: 20px;
        text-align: justify;
        font-weight: 400;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif
    }

    .chapter-content p::first-letter {
        /* margin-left: 50px; */
    }

    article {
        max-width: 700px;
    }

    .glass {
        backdrop-filter: blur(10px);
        -webkit-backdrop-filter: blur(10px);
        text-align: center;
    }

    .navigation-button {
        height: fit-content;
        background-color: rgba(0, 123, 255, 0.4);
        cursor: pointer;
        transition: background-color 0.3s ease;
        padding: 10px 20px;
    }

    .navigation-button:hover {
        background-color: rgba(0, 123, 255, 0.8);
        /* Adjust opacity on hover */
    }

    .navigation-button i {
        width: 50px;
    }

    .navs {
        position: fixed;
        top: 10%;
        left: -250px;
        position: flex;
        transition: transform 0.3s ease;
        z-index: 999;
    }

    .navs.left-move {
        transform: translateX(250px);
    }


    .navigation-page {
        padding: 15px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        background-color: rgba(255, 255, 255, 0.7);
        border-top-right-radius: 0;
        border-bottom-right-radius: 0;
        border-radius: 0px;
        width: 250px;
    }

    .navigation-page ol li {
        cursor: pointer;
    }

    .navigation-page a:hover {
        color: black;
        font-weight: 600;
    }

    * {
        scroll-behavior: smooth;
    }

    .navigation-page a {
        text-decoration: none;
        /* Remove underline */
        color: inherit;
        /* Inherit color from parent */
    }
</style>
@endsection

@section('main')
@include('homepage.template.header', [
'title' => 'Standar Operasional Prosedur',
'caption' => 'Pendakian Gunung Kerinci Di Taman Nasional Kerinci Seblat',
])


<div class="navs d-flex">
    <script>
        function toggleNavs() {
            const navsContainer = document.querySelector('.navs');
            navsContainer.classList.toggle('left-move');
        }
    </script>
    <div class="navigation-page glass border">
        <ol type="I" class="text-start d-flex flex-column gap-2 my-0 py-0">
            <li class="py-0">
                <a href="#bab-pendahuluan">Pendahuluan</a>
                <ol type="A" class="py-0">
                    <li><a href="#pendahuluan-a">Latar Belakang</a></li>
                    <li><a href="#pendahuluan-b">Maksud dan Tujuan</a></li>
                    <li><a href="#pendahuluan-c">Ruang Lingkup</a></li>
                    <li><a href="#pendahuluan-d">Pengertian</a></li>
                </ol>
            </li>
            <li><a href="#bab-arahan-teknis">Arahan Teknis</a></li>
            <li>
                <a href="#bab-ketentuan-pendakian">Ketentuan Pendakian</a>
                <ol type="A" class="py-0">
                    <li><a href="#ketentuan-a">Ketentuan</a></li>
                    <li><a href="#ketentuan-b">Kewajiban Pendaki</a></li>
                    <li><a href="#ketentuan-c">Larangan</a></li>
                    <li><a href="#ketentuan-d">Sanksi</a></li>
                </ol>
            </li>
            <li><a href="#bab-penutup">Penutup</a></li>
        </ol>
    </div>
    <div class="navigation-button glass shadow" onclick="toggleNavs()">
        <i class="bi bi-list gk-text-base-black"></i>
    </div>
</div>


<div class="container my-5 ">
    <article class="font-serif px-3 px-sm-5 d-block m-auto border py-5 rounded shadow-sm">
        <dl type="A">
            <header class="chapter-header" id="bab-pendahuluan">I. PENDAHULUAN</header>
            @include('homepage.sop.PENDAHULUAN.a')
            @include('homepage.sop.PENDAHULUAN.b')
            @include('homepage.sop.PENDAHULUAN.c')
            @include('homepage.sop.PENDAHULUAN.d')
        </dl>
        <dl>
            <header class="chapter-header" id="bab-arahan-teknis">II. ARAHAN TEKNIS</header>
            <dd class="chapter-content">
                <p>Gunung Kerinci merupakan habitat berbagai jenis flora dan fauna sangat penting bagi keseimbangan
                    ekosistem TNKS. Keberadaan jenis flora dan fauna di Gunung Kerinci ini sangat sensitive terhadap
                    perilaku pendaki, oleh karena itu kegiatan pendakian di Gunung Kerinci harus memperhatikan
                    perlindungan
                    keanekaragaman hayati.</p>
                <p>Aktivitas pendaki di dalam kawasan taman nasional berpotensi menimbulkan dampak negatif terhadap
                    keanekaragaman hayati dalam bentuk :
                </p>
                <ul>
                    <li>Penyebaran biji (dan atau benih) dan juga satwa ke dalam kawasan yang dibawa oleh pendaki baik
                        sengaja maupun tidak sengaja dari luar kawasan;</li>
                    <li>Pemadatan tanah yang dapat menyebabkan erosi, terutama pada jalur pendakian dan lokasi-lokasi
                        camping/pendirian tenda pendaki;</li>
                    <li>Gangguan terhadap satwa liar, terutama saat musim berkembang biak satwa liar, dan kemungkinan
                        adanya
                        perubahan perilaku satwa liar;</li>
                    <li>Perusakan vegetasi di sepanjang jalur pendakian dan di lokasi camping akibat pencabutan,
                        pengambilan, pematahan ranting, cabang untuk berbagai keperluan;</li>
                    <li>Pencemaran lingkungan akibat sampah dan kotoran manusia di jalur pendakian, lokasi camping dan
                        di
                        lokasi sumber mata air, yang tidak memperhatikan kaidah lingkungan;</li>
                    <li>Kebakaran yang dipicu oleh pembuatan api unggun, puntung rokok, dan lain-lain.</li>
                </ul>
            </dd>
        </dl>

        <dl>
            <header class="chapter-header" id="bab-ketentuan-pendakian">III. KETENTUAN PENDAKIAN</header>
            <p class="chapter-content">Setiap pendaki yang melalui pintu masuk di Pos R10 Kersik Tuo Kabupaten Kerinci
                Provinsi Jambi wajib memiliki karcis sedangkan pendaki yang melalaui Camping Ground Bangun Rejo
                Kabupaten Solok Selatan Provinsi Sumatera Barat wajib memiliki karcis dan Surat Ijin Masuk Kawasan
                Konservasi (SIMAKSI) pendakian karena melalui zona rimba.
            </p>
            @include('homepage.sop.KETENTUAN.a')
            @include('homepage.sop.KETENTUAN.b')
            @include('homepage.sop.KETENTUAN.c')
            @include('homepage.sop.KETENTUAN.d')
        </dl>
        <dl>
            <header class="chapter-header" id="bab-penutup">IV. PENUTUP</header>
            <dd class="chapter-content">
                Ketentuan yang belum/tidak tertuang di dalam SOP ini akan diatur lebih lanjut melalui Surat Keputusan
                Kepala Balai TNKS sesuai dengan ketentuan yang berlaku. Demikian Standar Operasional Prosedur (SOP) ini
                disusun untuk dapat menjadi pedoman dalam kegiatan pendakian di Kawasan TNKS.
            </dd>
        </dl>
        {{-- <footer class="text-right my-5">
                <div class="chapter-content text-end">
                    <section>Sungai Penuh, X Desember 2023</section>
                    <section>KEPALA BALAI BESAR,</section>
                    <div class="my-5"></div>
                    <section>HAIDIR, S.Hut., M.Si.</section>
                    <section>NIP. 19730729 199803 1 002</section>
                </div>
            </footer> --}}
    </article>
</div>
@endsection