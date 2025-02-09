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
                    <li><a href="#" class="text-black text-decoration-none d-block py-1">Alamat</a></li>
                    <li><a href="#" class="text-black text-decoration-none d-block py-1">Email</a></li>
                </ul>
            </div>

            <!-- Bantuan -->
            <div class="col-6 col-md-2 text-center text-md-start">
                <h5 class="fw-bold text-uppercase">Bantuan</h5>
                <ul class="list-unstyled">
                    <li><a href="#" class="text-black text-decoration-none d-block py-1">FAQ</a></li>
                    <li><a href="#" class="text-black text-decoration-none d-block py-1">Syarat & Ketentuan</a></li>
                    <li><a href="#" class="text-black text-decoration-none d-block py-1">SOP Pendakian</a></li>
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

<style>
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