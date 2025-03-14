<footer class="pt-5 pb-3 index-footer">
    <div class="container">
        <div class="row gy-4">
            <!-- Logo dan Nama TNKS -->
            <div class="col-12 col-md-5 d-flex align-items-center gap-3 text-center text-md-start">
                <img src="{{ asset('assets/icon/tnks.png') }}" alt="Logo" class="img-fluid" style="max-width: 80px;">
                <div>
                    <h4 class="mb-0 lead fw-bold fs-4">Taman Nasional <br> Kerinci Seblat</h4>
                    <p class="text-secondary small">Melindungi Keanekaragaman Hayati</p>
                </div>
            </div>

            <!-- Informasi -->
            <div class="col-6 col-md-2 text-center text-md-start">
                <h5 class="fw-bold text-uppercase">Informasi</h5>
                <ul class="list-unstyled">
                    <li><a href="{{ $fweb->text1 ?? '#' }}" class="text-black text-decoration-none d-block py-1">Tentang Kami</a></li>
                    <li><a href="#" class="text-black text-decoration-none d-block py-1">Alamat :</a></li>
                    <li><a href="#" class="text-secondary text-decoration-none d-block py-1 small">{{ $falamat->text1 }}</a></li>
                </ul>
            </div>

            <!-- Bantuan -->
            <div class="col-6 col-md-2 text-center text-md-start">
                <h5 class="fw-bold text-uppercase">Bantuan</h5>
                <ul class="list-unstyled">
                    <li><a href="{{ $ftutorial->text1 }}" class="text-black text-decoration-none d-block py-1">Video Tutorial</a></li>
                    <!-- <li><a href="{{route('homepage.snk')}}" class="text-black text-decoration-none d-block py-1">Syarat & Ketentuan</a></li> -->
                </ul>
            </div>

            <!-- Sosial Media -->
            <div class="col-12 col-md-3 text-center text-md-start">
                <h5 class="fw-bold text-uppercase">Sosial Media</h5>
                <div class="d-flex justify-content-center justify-content-md-start gap-3">
                    <a href="{{ $ffacebook->text1 ?: '#' }}" class="social-icon">
                        <i class="bi bi-facebook"></i>
                    </a>
                    <a href="{{ $finstagram->text1 ?: '#' }}" class="social-icon">
                        <i class="bi bi-instagram"></i>
                    </a>
                    <a href="{{ $fyoutube->text1 ?: '#' }}" class="social-icon">
                        <i class="bi bi-youtube"></i>
                    </a>
                </div>
            </div>
        </div>

        <hr class="border-secondary my-4">

        <!-- Hak Cipta -->
        <div class="text-center">
            <p class="mb-0 text-secondary small">
                Copyright &copy; {{ date('Y') }} Taman Nasional Kerinci Seblat | All Rights Reserved
            </p>
        </div>
    </div>
</footer>
<!-- Tombol Chat WhatsApp -->
<a href="https://wa.me/{{$ftelp->text2}}?text=Halo%20CS,%20saya%20ingin%20bertanya%20tentang%20layanan%20Anda"
    target="_blank"
    class="CSwhatsapp-btn">
    <img src="https://upload.wikimedia.org/wikipedia/commons/6/6b/WhatsApp.svg"
        alt="WhatsApp"
        class="whatsapp-icon">
</a>

<style>
    /* Gaya untuk tombol melayang */
    .CSwhatsapp-btn {
        position: fixed;
        bottom: 20px;
        right: 20px;
        background-color: #25D366;
        color: white;
        width: 50px;
        height: 50px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 0 10px 4px rgba(255, 255, 255, 0.8), 0 4px 8px rgba(0, 0, 0, 0.2);
        transition: transform 0.3s ease-in-out;
        z-index: 9999;
        text-decoration: none;
    }

    .CSwhatsapp-btn:hover {
        transform: scale(1.1);
    }

    .whatsapp-icon {
        width: 35px;
        height: 35px;
    }

    .social-icon {
        font-size: 1.5rem;
        transition: all 0.3s ease-in-out;
    }

    .social-icon:hover {
        color: #f8b400;
        transform: scale(1.1);
    }

    footer a {
        transition: color 0.3s ease-in-out;
    }

    footer a:hover {
        color: #f8b400 !important;
    }

    /* Responsif untuk layar kecil */
    @media (max-width: 576px) {
        .social-icon {
            font-size: 1.25rem;
        }
    }
</style>