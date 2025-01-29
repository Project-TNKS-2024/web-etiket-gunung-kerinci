<html lang="en">

<head>

   <meta charset="utf-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
   <meta name="description" content="">
   <meta name="author" content="">
   <title>TNKS Gunung Kerinci</title>
   <link rel="shortcut icon" type="image/png" href="http://127.0.0.1:8000/assets/img/logo/logo bulat.png">

   <link rel="stylesheet" href="http://127.0.0.1:8000/assets/img/logo/logo bulat.png">
   <!-- style -->
   <link rel="stylesheet" href="http://127.0.0.1:8000/bootstrap-5.3.3-dist/css/bootstrap.min.css">
   <!-- icon -->
   <link rel="stylesheet" href="http://127.0.0.1:8000/bootstrap-5.3.3-dist/font/bootstrap-icons.min.css">
   <link rel="stylesheet" href="http://127.0.0.1:8000/fontawesome-free-6.5.2-web/css/all.css">
   <!-- color -->
   <link rel="stylesheet" href="http://127.0.0.1:8000/componen/colorplate.css">
   <link rel="preconnect" href="https://fonts.googleapis.com">
   <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin="">
   <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&amp;display=swap" rel="stylesheet">
   <!-- <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> -->
   <style>
      .pdf-container {
         max-width: 700px;
         width: 100%;
      }

      .header-bg {
         position: relative;
         background: url("http://127.0.0.1:8000/assets/img/bg/title-header-bg.png") no-repeat;
         background-size: cover;
         background-position: 50% 50%;
         color: white;
      }

      @media (max-width: 425px) {
         #navbarSupportedContent .navbar-nav .nav-item {
            width: -webkit-fill-available;
         }
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

      .border-between {
         border-top: 2px solid white;
         width: 50px;
         margin: 20px 0;
      }

      .poppins-thin {
         font-family: "Poppins", sans-serif;
         font-weight: 100;
         font-style: normal;
      }

      .poppins-extralight {
         font-family: "Poppins", sans-serif;
         font-weight: 200;
         font-style: normal;
      }

      * {
         font-family: "Poppins", sans-serif;
      }

      .index-navbar {
         flex-wrap: wrap !important;
      }

      .index-nav-ats img {
         width: 50px;
      }

      .index-nav-ats a {
         text-decoration: none;
         color: black;
         font-size: 18px;
         font-weight: bold;
      }

      .index-nav-toggle {
         border: none;
         box-shadow: none !important;
      }

      .index-text-cardDestinasi {
         display: -webkit-box;
         /* Gunakan layout box untuk mendukung clamp */
         -webkit-box-orient: vertical;
         /* Orientasi vertikal */
         overflow: hidden;
         /* Sembunyikan teks yang melebihi batas */
         text-overflow: ellipsis;
         /* Tambahkan "..." di akhir teks */
         line-clamp: 2;
         /* Batasi teks ke 2 baris */
         -webkit-line-clamp: 2;
         /* Batasi teks ke 2 baris (untuk browser berbasis WebKit) */
      }

      .index-footer {
         box-shadow: 0 -4px 6px rgba(0, 0, 0, 0.1);
      }

      .index-footer img {
         height: 100px;
      }

      .index-footer h5 {
         font-size: 18px;
         font-weight: bold;
      }

      .index-footer h4 {
         font-weight: bold;
      }

      .index-footer a {
         text-decoration: none;
         color: var(--neutrals500);
      }

      .index-footer .copyright {
         color: var(--neutrals500);
         font-family: 'bootstrap-icons';

      }

      .useradmin .btn {
         border: 2px solid var(--primary100);
      }

      .useradmin .btn.active {
         border: 2px solid var(--primary) !important;
      }

      .useradmin .btn.close {
         border: none;
         background-color: var(--error200) !important;
         color: white;
      }

      .dashboard-sidebar-btn {
         background: none;
         color: #000;
         border: none;
         padding: 10px 20px;
         border-radius: 5px;
         text-align: center;
         text-decoration: none;
         display: inline-block;
      }

      .dashboard-sidebar-btn.close {
         background-color: var(--error200) !important;
         color: white;
      }

      .dashboard-sidebar-btn.active {
         background: linear-gradient(263deg, #0169BF 12.63%, #63B8FF 80.63%);
         color: #fff;
      }

      .dashboard-sidebar-btn {
         background: var(--neutrals100);
         color: var(--base-black);
         border: none;
         padding: 10px 20px;
         border-radius: 5px;
         text-align: center;
         text-decoration: none;
         display: inline-block;
      }

      .dashboard-sidebar-btn:hover {
         background: var(--neutrals200);
      }

      .dashboard-sidebar-btn.active:hover {
         background: linear-gradient(263deg, #63B8FF 12.63%, #0169BF 80.63%);
         background-size: 500%;
         color: white;
         border: none;
      }

      .toast-container {
         position: fixed;
         top: 1rem;
         right: 1rem;
         z-index: 1050;
      }

      .btn-gl-primary {
         background: linear-gradient(263deg, #0169BF 12.63%, #63B8FF 80.63%);
         color: #fff;
         border: none;
         background-size: 100%;

      }

      .btn-gl-primary:hover {
         background: linear-gradient(263deg, #63B8FF 12.63%, #0169BF 80.63%);
         background-size: 500%;
         color: white;
         border: none;
      }


      body {
         background-color: rgba(245, 246, 250, 1);
         min-height: 100vh;
         display: grid;
         grid-template-rows: auto 1fr auto;
      }

      footer {
         background-color: white;
      }

      .profile-container:hover {
         background-color: var(--primary600);
      }
   </style>

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

</head>

<body>


   <nav class="navbar navbar-expand-sm gk-bg-primary700 index-navbar py-0" style="font-size: 14px">
      <div class="w-100 index-nav-ats gk-bg-neutrals-base-white py-2">
         <div class="container d-flex justify-content-between align-items-center">
            <a href="http://127.0.0.1:8000/beranda" class="d-flex align-items-center" style="gap: 10px;">
               <img src="http://127.0.0.1:8000/assets/img/logo/logo bulat.png" alt="">
               <div>Taman Nasional Kerinci Seblat</div>
            </a>
            <!-- <div class="d-none d-sm-flex align-items-center ">
                <select class="form-select border-0 form-select-sm w-100" style="background-color: transparent"
                    aria-label="Small select example">
                    <option value="id" selected>Pilih Bahasa : Indonesia</option>
                    <option value="en">Select Language: English</option>
                </select>
            </div> -->
         </div>
      </div>
      <div class="container">
         <div></div>
         <button class="navbar-toggler index-nav-toggle" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
         </button>
         <div class="collapse navbar-collapse py-2" id="navbarSupportedContent">

            <ul class="navbar-nav me-auto mb-2 mb-sm-0 d-flex align-items-center align-items-md-center">

               <li class="nav-item ">
                  <a href="http://127.0.0.1:8000/beranda" id="navigasi0" class="nav-link text-center text-white rounded-4 py-2 py-md-0 px-2 px-md-2 px-sm-1 mx-2" style="cursor: pointer; font-size: 14px;" aria-current="page" data-route="[http://127.0.0.1:8000/beranda]">
                     Beranda
                  </a>
               </li>
               <li class="nav-item ">
                  <a href="http://127.0.0.1:8000/sop" id="navigasi1" class="nav-link text-center rounded-4 py-2 py-md-0 px-2 px-md-2 px-sm-1 mx-2 gk-bg-base-white gk-text-primary700 font-semibold" style="cursor: pointer; font-size: 14px;" aria-current="page" data-route="[http://127.0.0.1:8000/sop]">
                     SOP Pendakian
                  </a>
               </li>
               <li class="nav-item ">
                  <a href="http://127.0.0.1:8000/panduan" id="navigasi2" class="nav-link text-center text-white rounded-4 py-2 py-md-0 px-2 px-md-2 px-sm-1 mx-2" style="cursor: pointer; font-size: 14px;" aria-current="page" data-route="[http://127.0.0.1:8000/panduan]">
                     Panduan Booking
                  </a>
               </li>
               <li class="nav-item ">
                  <a href="http://127.0.0.1:8000/booking/destinasi/list" id="navigasi3" class="nav-link text-center text-white rounded-4 py-2 py-md-0 px-2 px-md-2 px-sm-1 mx-0 mx-md-2" style="cursor: pointer; font-size: 14px;" aria-current="page" data-route="[http://127.0.0.1:8000/booking/destinasi/list, http://127.0.0.1:8000/booking/destinasi/1/paket, http://127.0.0.1:8000/booking/destinasi/paket/1/tiket, http://127.0.0.1:8000/booking/destinasi/2/paket, http://127.0.0.1:8000/booking/destinasi/paket/2/tiket, http://127.0.0.1:8000/booking/destinasi/3/paket, http://127.0.0.1:8000/booking/destinasi/paket/3/tiket, ]">
                     Booking Online
                  </a>
               </li>


            </ul>

            <ul class="navbar-nav mb-2 mb-sm-0 d-flex align-items-center ">

               <!-- If the user is logged in with the role 'user' -->
               <li class="nav-item ">
                  <a class="nav-link text-white rounded-4 py-2 py-md-0 px-2 px-md-2 px-sm-1 mx-2" aria-current="page" href="http://127.0.0.1:8000/dashboard">Dashboard</a>
               </li>
               <div class="dropdown">
                  <span class="profile-container rounded-pill d-flex align-items-center" style="user-select: none; cursor: pointer;" data-bs-toggle="dropdown" aria-expanded="false">
                     <img src="http://127.0.0.1:8000/assets/img/dashboard/Ellipse 143.png" style="height: 30px;">
                  </span>

                  <ul class="dropdown-menu dropdown-menu-end">
                     <!-- Dashboard -->
                     <li>
                        <form action="http://127.0.0.1:8000/logout" method="post" class="m-0">
                           <input type="hidden" name="_token" value="2TORxBSoMgGhSVRXR8nu9VO0yISJRkXgVDGHFtHU" autocomplete="off"> <button class="dropdown-item d-flex align-items-center text-danger" type="submit">
                              <i class="me-2 bi bi-box-arrow-right"></i> Logout
                           </button>
                        </form>
                     </li>
                  </ul>
               </div>


            </ul>


            <!-- <div class="d-block d-sm-none my-2">
                <select class="form-select form-select-sm" aria-label="Small select example">
                    <option value="id" selected>Pilih Bahasa : Indonesia</option>
                    <option value="en">Select Language: English</option>
                </select>
            </div> -->
         </div>
      </div>
   </nav>
   <main>
      <script>
         function addVisibilityToggle(id) {
            const element = document.getElementById(id);

            if (!element) {
               console.error(`Element with ID "${id}" not found.`);
               return;
            }

            const parentElement = document.createElement('div');
            parentElement.classList.add('position-relative', 'w-100');
            element.parentNode.insertBefore(parentElement, element);
            parentElement.appendChild(element);

            const iconContainer = document.createElement('div');
            iconContainer.classList.add('position-absolute', 'align-items-center', 'd-flex', 'px-2');
            iconContainer.style.width = 'fit-content';
            iconContainer.style.cursor = 'pointer';
            iconContainer.style.height = '100%';
            iconContainer.style.background = 'transparent';
            iconContainer.style.top = '0';
            iconContainer.style.right = '0';

            // const icon = document.createElement('img');
            // icon.src = "http://127.0.0.1:8000/assets/icon/tnks/eye.svg";
            // icon.style.cursor = 'pointer';

            // iconContainer.appendChild(icon);
            iconContainer.innerHTML = `
<div class="position-relative d-flex align-items-center justify-content-center">
    <img src="http://127.0.0.1:8000/assets/icon/tnks/eye.svg" />
    <div id='${id}-toggler' class='position-absolute'
        style='width: 100%; height: 90%; border-right: 2px solid rgba(152, 162, 179, 1); transform: rotate(-45deg) scale(0); top: 35%; left: -30%; transition: height 0.3s ease, transform 0.3s ease;'>
    </div>
</div>
`;


            iconContainer.addEventListener('click', function(e) {

               const toggler = document.getElementById(id + '-toggler');
               console.log(element.type);
               // element.type = element.type == 'password' ? 'text' : 'password';
               if (element.type == 'password') {
                  element.type = 'text';
                  toggler.style.transform = 'rotate(-45deg) scale(1)';
               } else {
                  element.type = 'password'
                  toggler.style.transform = 'rotate(-45deg) scale(0)';
               }
            });
            parentElement.appendChild(iconContainer);
         }
      </script>
      <header class="header-bg text-white text-center py-3">
         <div class="container header-content">
            <h1 class="fw-semibold fs-5 lead">Standar Operasional Prosedur</h1>
            <div class="border-between m-auto d-block"></div>
            <p class="lead fs-6">Pendakian Gunung Kerinci Di Taman Nasional Kerinci Seblat</p>
         </div>
      </header>


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
               <dt class="chapter-subheader" id="pendahuluan-a">A. Latar Belakang</dt>
               <dd class="chapter-content">
                  <p>
                     Dalam rangka untuk melakukan pelayanan prima kepada
                     pengunjung dan untuk
                     mewujudkan pengelolaan
                     ekowisata yang profesional, efektif dan efisien dalam hal pengelolaan ekowisata Pendakian Gunung
                     Kerinci
                     maka diperlukan Standar Operasional Prosedur (yang selanjutnya di singkat SOP) pada seluruh proses
                     penyelenggaraan pengelolaannya.
                  </p>
                  <p>Salah satu objek daya tarik wisata alam di Taman Nasional Kerinci Seblat (TNKS) adalah Gunung Kerinci
                     yang merupakan gunung berapi aktif tertinggi di Indonesia (3.805 mdpl) yang terletak di dua Provinsi
                     yaitu Jambi dan Sumatera Barat. Sehingga perlu disusun SOP yang merupakan pedoman atau acuan untuk
                     melaksanakan tugas pekerjaan sesuai dengan Tugas dan fungsi pokok pengelola wisata. SOP juga
                     merupakan alat penilaian kinerja berdasarkan indikator indikator teknis, administrasif dan
                     prosedural sesuai dengan tata kerja, prosedur dan sistem kerja pada unit kerja yang bersangkutan.
                     SOP berisi Prosedur Kerja yaitu urutan-urutan yang telah dibuat dalam melakukan suatu pekerjaan
                     dimana terdapat tahapan demi tahapan yang harus dilalui sehingga terlihat jelas adanya aturan yang
                     harus ditaati oleh orang yang akan menjalankan prosedur kerja pada bidang tugas yang telah mereka
                     kerjakan dan membuat suatu pekerjaan itu mudah dimengerti dan dipahami. Dengan adanya standar
                     operasional prosedur ini kedepannya bisa dilakukan evaluasi dan peningkatan kualitas kerja yang
                     lebih baik seiring dengan berjalannya waktu.</p>
               </dd>
               <dt class="chapter-subheader" id="pendahuluan-b">B. Maksud dan Tujuan</dt>
               <dd class="chapter-content">
                  <ol>
                     <li>Maksud</li>
                     <p>Maksud penyusunan SOP pendakian Gunung Kerinci di TNKS ini adalah sebagai salah satu upaya untuk meningkatkan
                        pelayanan demi keselamatan, kenyamanan dan ketertiban pendaki serta menjaga kelestarian keanekaragaman
                        hayati dan ekosistem Gunung Kerinci.
                     </p>
                     <li>Tujuan</li>
                     <p>SOP Pendakian Gunung Kerinci di TNKS ini disusun sebagai pedoman atau aturan pelaksanaan/penyelenggaraan
                        pendakian Gunung Kerinci.
                     </p>
                  </ol>
               </dd>
               <dt class="chapter-subheader" id="pendahuluan-c">C. Ruang Lingkup</dt>
               <dd class="chapter-content">
                  <p>
                     Ruang Lingkup SOP Pendakian Gunung Kerinci di TNKS ini meliputi arahan teknis, prosedur pendakian dan larangan
                     serta
                     sanksi.
                  </p>
               </dd>
               <dt class="chapter-subheader" id="pendahuluan-d">D. Pengertian</dt>
               <dd class="chapter-content">
                  <ol>
                     <li>
                        <strong>Kawasan Pelestarian Alam</strong> adalah kawasan dengan ciri khas tertentu, baik di darat maupun di
                        perairan yang mempunyai fungsi perlindungan sistem penyangga kehidupan, pengawetan keanekaragaman jenis
                        tumbuhan dan satwa, serta pemanfaatan secara lestari sumber daya alam hayati dan ekosistemnya.
                     </li>
                     <li>
                        <strong>Taman Nasional</strong> adalah kawasan pelestarian alam yang mempunyai ekosistem asli, dikelola
                        dengan sistem zonasi yang dimanfaatkan untuk tujuan penelitian, ilmu pengetahuan, pendidikan, menunjang
                        budidaya, pariwisata dan rekreasi.
                     </li>
                     <li>
                        <strong>Sumber Daya Alam Hayati</strong> adalah unsur-unsur hayati di alam yang terdiri dari sumber daya
                        alam nabati (tumbuhan) dan sumber daya alam hewani (satwa) yang bersama dengan unsur non hayati di
                        sekitarnya secara keseluruhan membentuk ekosistem.
                     </li>
                     <li>
                        <strong>Wisata Alam</strong> adalah kegiatan perjalanan atau sebagian dari kegiatan tersebut yang dilakukan
                        secara sukarela serta bersifat sementara untuk menikmati gejala keunikan dan keindahan alam di kawasan suaka
                        margasatwa, taman nasional, taman hutan raya, dan taman wisata alam.
                     </li>
                     <li>
                        <strong>Laporan aktivitas gunung api</strong> adalah laporan aktivitas yang diterbitkan oleh Kementerian
                        Energi dan Sumber Daya Mineral (KESDM), Badan Geologi, Pusat Vulkanologi dan Mitigasi Bencana Gunung Api dan
                        dapat diakses melalui <a href="https://magma.esdm.go.id/v1/gunung-api/laporan">https://magma.esdm.go.id/v1/gunung-api/laporan</a>;
                     </li>
                     <li>
                        <strong>Surat izin masuk kawasan konservasi (Simaksi)</strong> adalah izin yang diberikan oleh pejabat
                        berwenang kepada pemohon untuk masuk kawasan suaka alam, kawasan pelestarian alam dan taman buru.
                     </li>
                     <li>
                        <strong>Karcis</strong> adalah tiket masuk resmi yang dikeluarkan oleh Balai Besar TNKS sesuai dengan
                        Peraturan Pemerintah Nomor 12 Tahun 2014 tentang Jenis dan Tarif atas Jenis Penerimaan Negara Bukan Pajak
                        yang Berlaku Pada Kementerian Kehutanan.
                     </li>
                     <li>
                        <strong>Pendakian Gunung Kerinci</strong> adalah kegiatan olahraga, profesi dan rekreasi wisata alam
                        bertujuan untuk menikmati keindahan alam Gunung Kerinci melalui jalur pendakian yang telah ditetapkan oleh
                        Balai Besar TNKS.
                     </li>
                     <li>
                        <strong>Kemah</strong> adalah meletakkan, membangun tenda atau struktur berbentuk tenda dipergunakan untuk
                        berteduh atau menginap.
                     </li>
                     <li>
                        <strong>Pintu masuk pendakian</strong> adalah pintu resmi yang telah ditetapkan oleh Balai Besar TNKS yaitu
                        di Pusat Informasi R10 Kersik Tuo Kabupaten Kerinci Provinsi Jambi dan Camping Ground Bangun Rejo Kabupaten
                        Solok Selatan Provinsi Sumatera Barat.
                     </li>
                     <li>
                        <strong>Pos</strong> adalah bangunan fasilitas untuk melakukan pengecekan ulang bagi para pendaki yang
                        lokasinya berada di Pusat Informasi R10 Kersik Tuo Kabupaten Kerinci Provinsi Jambi dan Visitor Center
                        Bangun Rejo Kabupaten Solok Selatan Provinsi Sumatera Barat.
                     </li>
                     <li>
                        <strong>Pendaki</strong> adalah pengunjung yang melakukan pendakian di jalur resmi Gunung Kerinci dan telah
                        memenuhi persyaratan pendakian dan memiliki karcis masuk kawasan TNKS.
                     </li>
                     <li>
                        <strong>Pendaki nusantara</strong> adalah pengunjung berkewarganegaraan Indonesia (WNI) yang melakukan
                        pendakian di Gunung Kerinci. Identitas kewarganegaraan dibuktikan dengan menunjukkan KTP/KK.
                     </li>
                     <li>
                        <strong>Pendaki mancanegara</strong> adalah pengunjung berkewarganegaraan asing (WNA) yang melakukan
                        pendakian di Gunung Kerinci. Identitas kewarganegaraan dibuktikan dengan status kewarganegaraan di dokumen
                        kependudukan.
                     </li>
                     <li>
                        <strong>Petugas Pemungut</strong> adalah aparatur sipil negara Balai Besar TNKS yang ditetapkan dengan Surat
                        Keputusan Kepala Balai Besar TNKS yang mempunyai tugas memungut karcis masuk Kawasan TNKS.
                     </li>
                     <li>
                        <strong>Volunteer/Relawan</strong> adalah sukarelawan bersifat independen yang dibina oleh Balai Besar TNKS
                        guna menumbuh kembangkan kegiatan konservasi berupa kesadartahuan, perlindungan dan pelestarian alam di
                        kawasan TNKS.
                     </li>
                     <li>
                        <strong>Interpreter</strong> adalah orang yang menyampaikan informasi alam/lingkungan/hutan kepada pendaki
                        sehingga menjadi jembatan antara keduanya yang pada akhirnya akan menumbuhkan kepedulian, pemahaman dan
                        penyadaran terhadap pentingnya alam lingkungan/hutan tersebut.
                     </li>
                     <li>
                        <strong>Pemandu</strong> adalah orang yang memiliki kemampuan kepemanduan gunung dan memiliki sertifikat
                        dari asosiasi atau organisasi yang diakui.
                     </li>
                     <li>
                        <strong>Porter</strong> adalah orang-orang yang dibayar untuk membantu membawa barang-barang para pendaki
                        pada saat melakukan aktivitas pendakian gunung. Seringkali porter juga bertugas untuk menyiapkan makanan
                        pada saat pendakian.
                     </li>
                     <li>
                        <strong>Penutupan Pendakian</strong> adalah kebijakan menutup semua bentuk aktivitas pendakian ke Gunung
                        Kerinci yang ditetapkan oleh Kepala Balai Besar TNKS.
                     </li>
                     <li>
                        <strong>Pembatasan pendakian</strong> adalah kebijakan pembatasan bagi pendaki yang ditetapkan oleh Kepala
                        Balai Besar TNKS berdasarkan laporan aktivitas gunung api.
                     </li>
                     <li>
                        <strong>Pemulihan/Recovery ekosistem</strong> adalah upaya perbaikan ekosistem dari kondisi rusak ke kondisi
                        awal/baik secara alami maupun dengan campur tangan manusia.
                     </li>
                     <li>
                        <strong>Sistem Booking Online</strong> adalah pendaftaran dan pembayaran karcis masuk untuk kegiatan
                        pendakian Gunung Kerinci oleh para calon pendaki secara online.
                     </li>
                     <li>
                        <strong>Kuota pendaki</strong> adalah jumlah pendaki maksimal harian yang diizinkan untuk melakukan
                        pendakian di setiap pintu masuk resmi yang telah ditetapkan oleh Balai Besar TNKS.
                     </li>
                     <li>
                        <strong>Vandalisme</strong> adalah salah satu tindakan perusakan fasilitas wisata alam,
                        mencoret-coret/melukai pohon, batu, dan lain-lain.
                     </li>
                     <li>
                        <strong>Perizinan Berusaha Penyediaan Jasa Wisata Alam</strong> yang selanjutnya disingkat PB-PJWA adalah
                        izin usaha yang diberikan untuk penyediaan jasa wisata alam pada kegiatan Pengusahaan Pariwisata Alam di
                        Suaka Margasatwa, Taman Nasional, Taman Hutan Raya, dan Taman Wisata Alam.
                     </li>
                     <li>
                        <strong>Trekking Organizer (TO)</strong> adalah orang/badan usaha/koperasi yang memiliki Perizinan Berusaha
                        Penyediaan Jasa Wisata Alam (PB-PJWA) yang dikeluarkan oleh Kementerian Lingkungan Hidup dan Kehutanan
                        berupa penyedia jasa perjalanan wisata pendakian.
                     </li>
                     <li>
                        <strong>Penyedia Jasa Pramuwisata</strong> adalah orang/badan usaha/koperasi yang memiliki Perizinan
                        Berusaha Penyediaan Jasa Wisata Alam (PB-PJWA) yang dikeluarkan oleh Kementerian Lingkungan Hidup dan
                        Kehutanan berupa penyedia jasa pramuwisata.
                     </li>
                     <li>
                        <strong>Penyedia Jasa Makanan dan Minuman</strong> adalah orang/badan usaha/koperasi yang memiliki Perizinan
                        Berusaha Penyediaan Jasa Wisata Alam (PB-PJWA) yang dikeluarkan oleh Kementerian Lingkungan Hidup dan
                        Kehutanan berupa jasa penyediaan makanan dan minuman.
                     </li>
                     <li>
                        <strong>Surat Keterangan Sehat</strong> adalah surat yang diterbitkan oleh dokter pemerintah, Puskesmas,
                        Klinik Kesehatan ataupun Rumah Sakit yang menyatakan bahwa yang bersangkutan dalam kondisi layak untuk
                        melakukan pendakian. Surat keterangan sehat diperoleh paling lama 2 hari sebelum memulai pendakian.
                     </li>
                     <li>
                        <strong>Data checklist sampah</strong> adalah daftar barang bawaan pendaki yang berpotensi menghasilkan
                        sampah.
                     </li>
                     <li>
                        <strong>Data checklist perlengkapan standar pendakian</strong> adalah daftar perlengkapan standar pendakian
                        yang wajib dibawa oleh pendaki.
                     </li>
                  </ol>
               </dd>
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
               <dt class="chapter-subheader" id="ketentuan-a">A. Ketentuan</dt>
               <dd class="chapter-content">
                  <ol class="d-flex flex-column gap-2">
                     <li>Pembelian karcis di Kantor Balai Besar TNKS Sungai Penuh, Pusat informasi R10 Kersik Tuo Kabupaten Kerinci
                        Provinsi Jambi untuk pendakian melalui pintu masuk Kersik Tuo sedangkan pendakian yang melalui pintu masuk
                        Camping Ground Bangun Rejo Kabupaten Solok Selatan Provinsi Sumatera Barat menggunakan Karcis dan SIMAKSI;
                     </li>
                     <li>Pengurusan SIMAKSI dilakukan secara online dan langsung di Kantor BBTNKS di Sungai Penuh atau Kantor Bidang
                        Pengelolaan TN Wilayah II Sumatera Barat di Padang atau Kantor Seksi Pengelolaan TN Wilayah IV Sangir dan
                        dapat dilakukan 2 bulan hingga 1 hari sebelum pendakian;</li>
                     <li>Batas waktu pendakian melalui Pos R10 Kersik Tuo Kabupaten Kerinci Provinsi Jambi adalah 2 (dua) hari dan
                        pendakian melalui Camping Ground Bangun Rejo Kabupaten Solok Selatan Provinsi Sumatera Barat adalah 4
                        (empat) hari, apabila merencanakan lebih dari itu <b>wajib</b> memberitahukan/ menginformasikan diawal
                        keberangkatan dan dikenai biaya kelipatannya;</li>
                     <li>Pendaki <b>wajib</b> memberitahukan rencana tanggal dan lokasi turun/kembali dan melaporkan kepada petugas
                        di pintu
                        masuk;</li>
                     <li>Tanggung jawab dan keselamatan pendaki menjadi tanggung jawab pribadi dan tidak akan menuntut pihak
                        pengelola (Balai Besar TNKS), pemandu, porter, pemerintah dan pemerintah daerah;</li>
                     <li>Bagi pendaki yang naik dari pintu masuk Kersik Tuo dan turun melalui pintu masuk Camping Ground Bangun Rejo
                        Kabupaten Solok Selatan <b>wajib</b> menggunakan SIMAKSI;</li>
                     <li>Apabila pendaki melanggar kelebihan hari dalam mendaki akan dikenakan SANKSI, kecuali dalam keadaan darurat
                        dan telah dilaporkan kepada petugas;</li>
                     <li>Jumlah pendaki yang diijinkan naik maksimum 46 orang/hari khusus jalur pendakian melalui Camping Ground
                        Bangun Rejo Kabupaten Solok Selatan Provinsi Sumatera Barat;</li>
                     <li>Setiap SIMAKSI pendakian dikeluarkan untuk grup pendaki dengan jumlah minimum 3 (tiga) orang dan maksimum 10
                        (sepuluh) orang pendaki, serta untuk mengurangi resiko kecelakaan diwajibkan menggunakan jasa pemanduan dari
                        Sekretariat Bersama Pemandu Gunung Kerinci Kabupaten Solok Selatan yang telah mendapat rekomendasi dari
                        Balai Besar TNKS;</li>
                     <li>Proses pengambilan SIMAKSI bisa dilakukan pada hari jam/ kerja;</li>
                     <li>Semua calon pendaki <b>wajib</b> menyerahkan fotokopi Kartu Identitas yang masih berlaku seperti: SIM, KTA,
                        KTP,
                        Paspor, atau Kartu Pelajar serta Surat Keterangan Sehat dokter (asli);</li>
                     <li>Calon pendaki yang berumur kurang dari 17 tahun, <b>wajib</b> menyerahkan Surat Izin Orang Tua/ Wali dan
                        ditandatangani di atas materai Rp. 10.000 serta dilampirkan fotokopi KTP Orang Tua/ Wali yang masih berlaku;
                     </li>
                     <li>Bagi Warga Negara Asing (WNA) yang akan mendaki <b>diwajibkan</b> untuk menggunakan jasa pemanduan yang
                        telah
                        terdaftar di Balai Besar TNKS;</li>
                     <li>Untuk komposisi jumlah pengunjung, pemandu yaitu:
                        <ol type="a">
                           <li>Maksimal pemanduan: 1 orang pemandu untuk 5 orang pengunjung/pendaki</li>
                           <li>Maksimal beban porter: 1 orang porter seberat 25 Kg</li>
                        </ol>
                     </li>
                     <li>Membayar karcis masuk sesuai dengan peraturan yang berlaku dan asuransi keselamatan</li>
                  </ol>

               </dd>
               <dt class="chapter-subheader" id="ketentuan-b">B. Pendaki DIWAJIBKAN</dt>
               <dd class="chapter-content ">
                  <ol class="d-flex flex-column gap-2">
                     <li>Berbadan sehat pada saat melakukan pendakian dengan menunjukkan Surat Keterangan Dokter (asli) pada pintu
                        masuk dan pada saat mengurus Simaksi;</li>
                     <li>Masuk jalur pendakian antara pukul 07.30 s/d 15.00 WIB dan mendaki pada jalur yang sudah ditentukan/ jalur
                        resmi;</li>
                     <li>Mematuhi semua rambu-rambu dan informasi keselamatan yang ada di sepanjang jalur pendakian;</li>
                     <li>Melakukan evakuasi mandiri terhadap diri dan rekannya yang sakit sebelum mendapatkan bantuan dari petugas;
                     </li>
                     <li>Wajib menggunakan dan membawa jasa Pemandu/ Porter yang telah terdaftar di Balai Besar TNKS;</li>
                     <li>Memakai dan membawa perlengkapan standar pendakian gunung serta perbekalan pendakian yang cukup;</li>
                     <li>Mengisi form identitas diri dan isian barang bawaan yang menghasilkan sampah;</li>
                     <li>Membawa trash bag kantong sampah dan membawa sampah bawaannya ke luar kawasan Taman Nasional;</li>
                     <li>Memprioritaskan penanganan bagi wanita yang sedang menstruasi utamanya segera membawa turun korban tersebut
                        apabila sudah menderita sakit;</li>
                     <li>Membawa kelengkapan P3K standar, dan survival kit standar;</li>
                     <li>Menjaga norma agama, norma susila dan kearifan lokal;</li>
                     <li>Mengikuti jalur pendakian yang sudah ditetapkan Balai Besar TNKS.</li>
                  </ol>

                  <p>Petugas Balai Besar TNKS akan memeriksa barang bawaan, karcis dan SIMAKSI sebelum dan sesudah memasuki kawasan.
                     Dalam rangka penangulangan sampah di Gunung Kerinci oleh para pendaki diwajibkan untuk meninggalkan salah satu
                     identitas asli pribadi kepada petugas akan dikembalikan kepada pendaki apabila barang bawaan yang menghasilkan
                     sampah dibawa kembali keluar TNKS.
                  </p>
               </dd>
               <dt class="chapter-subheader" id="ketentuan-c">C. Setiap Pendaki DILARANG</dt>
               <dd class="chapter-content">
                  <ol class="d-flex flex-column gap-2">
                     <li>Membawa Satwa dan tumbuhan dan bagian-bagiannya dari luar dan dari dalam kawasan TNKS;</li>
                     <li>Mengambil, merusak, semua jenis tanaman dan bagian-bagiannya di dalam kawasan TNKS;</li>
                     <li>Membunuh, melukai, mengambil satwa beserta bagian-bagiannya dari dalam kawasan TNKS;</li>
                     <li>Memetik, memindahkan, memotong, menebang, mencabut tumbuhan di dalam kawasan TNKS;</li>
                     <li>Membawa Senjata api, senjata tajam (Parang, Golok dan sejenisnya) kecuali pisau lipat, pisau saku/pisau
                        kecil;</li>
                     <li>Melakukan aktivitas yang menyebabkan kebakaran hutan di dalam kawasan TNKS;</li>
                     <li>Membuang sampah di dalam kawasan TNKS;</li>
                     <li>Mengganggu, memindahkan dan merusak fasilitas yang tersedia di dalam kawasan TNKS;</li>
                     <li>Melakukan Vandalisme dalam kawasan TNKS;</li>
                     <li>Melakukan pendakian antara jam 15.00 s/d 07.30 WIB;</li>
                     <li>Mengganti identitas/pendaki pada SIMAKSI atau tidak sesuai dengan SIMAKSI;</li>
                     <li>Menggunakan SIMAKSI pendakian untuk kegiatan Diklat pencinta alam/kegiatan orientasi pencinta alam;</li>
                     <li>Buang Air Besar di aliran sungai/mata air dan di sepanjang jalur pendakian;</li>
                     <li>Membawa dan menggunakan Narkotika dan obat-obatan Terlarang (<b>NARKOBA</b>), Miras dan bahan-bahan yang
                        dilarang
                        oleh undang-undang Republik Indonesia di dalam kawasan TNKS dan sekitarnya.</li>
                  </ol>

               </dd>
               <dt class="chapter-subheader" id="ketentuan-d">D. SANKSI</dt>
               <dd class="chapter-content">
                  <p>Sanksi dikenakan kepada Pendaki apabila melanggar ketentuan sebagai berikut : </p>
                  <ol class="d-flex flex-column gap-2">
                     <li>Bagi pendaki yang memasuki kawasan TNKS lebih dari pukul 15.00 WIB diwajib untuk menunggu di lokasi berkemah
                        (camping) atau homestay terdekat sampai pukul 07.30 WIB;</li>
                     <li>Bagi pendaki yang tidak membawa alat pendakian sesuai standar maka harus melengkapinya atau tidak diizinkan
                        melakukan pendakian;</li>
                     <li>Bagi yang melanggar aturan tersebut pada point A, B dan C, maka pendaki yang bersangkutan dan organisasinya
                        akan masuk Daftar Hitam (BLACKLIST) dan tidak diperbolehkan untuk melakukan pendakian kembali ke Gunung
                        Kerinci sampai batas waktu yang diberikan Balai Besar TNKS</li>
                  </ol>
                  <p>Bagi Para Pendaki yang melakukan perbuatan hukum yang melanggar Peraturan Perundang-undangan yang berlaku di
                     Republik Indonesia, dilakukan tindakan sesuai dengan Peraturan Perundang-undangan Republik Indonesia serta
                     sesuai dengan Kitab Undang-Undang Hukum Acara Indonesia.
                  </p>

               </dd>
            </dl>
            <dl>
               <header class="chapter-header" id="bab-penutup">IV. PENUTUP</header>
               <dd class="chapter-content">
                  Ketentuan yang belum/tidak tertuang di dalam SOP ini akan diatur lebih lanjut melalui Surat Keputusan
                  Kepala Balai TNKS sesuai dengan ketentuan yang berlaku. Demikian Standar Operasional Prosedur (SOP) ini
                  disusun untuk dapat menjadi pedoman dalam kegiatan pendakian di Kawasan TNKS.
               </dd>
            </dl>

         </article>
      </div>
   </main>

   <footer class="pt-4 pb-2 index-footer">
      <div class="container">
         <div class="row">
            <div class="col-md-5 d-flex align-items-center gap-2">
               <img src="http://127.0.0.1:8000/assets/img/logo/logo bulat.png" alt="Logo" class="img-fluid my-0">
               <div>
                  <h4 class="mb-0 lead fw-normal fs-3">Taman Nasional Kerinci Seblat</h4>
               </div>
            </div>
            <div class="col-md-2">
               <h5>Informasi</h5>
               <ul class="list-unstyled">
                  <li><a href="#">Tentang Kami</a></li>
                  <li><a href="#">Alamat</a></li>
                  <li><a href="#">Email</a></li>
               </ul>
            </div>
            <div class="col-md-2">
               <h5>Bantuan</h5>
               <ul class="list-unstyled">
                  <li><a href="#">FAQ</a></li>
                  <li><a href="#">Syarat &amp; Ketentuan</a></li>
                  <li><a href="#">SOP Pendakian</a></li>
               </ul>
            </div>
            <div class="col-md-3">
               <h5>Sosial Media Kami</h5>
               <a href="#" class="me-2"><i class="bi bi-facebook"></i></a>
               <a href="#" class="me-2"><i class="bi bi-twitter"></i></a>
               <a href="#" class="me-2"><i class="bi bi-instagram"></i></a>
               <a href="#" class="me-2"><i class="bi bi-linkedin"></i></a>
               <a href="#"><i class="bi bi-youtube"></i></a>
            </div>
         </div>

         <div class="text-center mt-2">
            <p class="mb-0 copyright">Copyright © Taman Nasional Kerinci Seblat 2024</p>
         </div>
      </div>
   </footer>

   <!-- <script src="http://127.0.0.1:8000/bootstrap-5.3.3-dist/js/bootstrap.js"></script> -->
   <script src="http://127.0.0.1:8000/bootstrap-5.3.3-dist/js/bootstrap.bundle.js"></script>

   <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
   <!-- <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.7/dist/umd/popper.min.js"></script> -->

   <div class="toast-container" id="toastContainer">




   </div>

   <!-- script notif -->
   <script>
      document.addEventListener('DOMContentLoaded', function() {
         const toastElements = document.querySelectorAll('.toast');
         toastElements.forEach(toastElement => {
            const toast = new bootstrap.Toast(toastElement, {
               autohide: true,
               delay: 5000 // 1000 milliseconds = 1 seconds
            });
            toast.show();
         });
      });

      class myNotif {
         constructor() {}
         primay() {}
         warning() {}
         danger() {}
         success() {}
      }
   </script>
   <!-- script navigasi navbar homepage -->
   <script>
      const extractUrls = (str) => {
         const match = str.match(/\[(.*?)\]/);
         if (match) {
            const urlString = match[1];
            return urlString.split(", ").map(url => url.trim());
         }
         return [];
      };

      const navItems = document.querySelectorAll(".nav-link");
      for (let i = 0; i < navItems.length; i++) {
         const routes = navItems[i].getAttribute('data-route');
         const tr = routes != null ? extractUrls(routes) : '[]';
         // console.log(tr);
         if (!tr.includes(window.location.href)) {
            navItems[i].classList.remove('gk-bg-base-white');
            navItems[i].classList.remove('gk-text-primary700');
            navItems[i].classList.remove('font-semibold');
            navItems[i].classList.add('text-white');
         } else {
            navItems[i].classList.add('gk-bg-base-white');
            navItems[i].classList.add('gk-text-primary700');
            navItems[i].classList.add('font-semibold');
            navItems[i].classList.remove('text-white');
         }
      }
   </script>

   <!-- script untuk generate input -->
   <script src="http://127.0.0.1:8000/componen/generateInput.js"></script>





</body>

</html>