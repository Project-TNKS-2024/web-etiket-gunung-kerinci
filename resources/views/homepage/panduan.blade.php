@extends('homepage.template.index')


@section('css')
<style>

</style>
@endsection

@section('main')
@include('homepage.template.header', [
'title' => 'Alur Booking Online Tiket Wisata Pendakian Gunung Kerinci',
'caption' => 'Pendakian Gunung Kerinci Di Taman Nasional Kerinci Seblat',
])

<div class="container my-5" style="padding:  50px;">
    <header class="">
        <div class="d-flex my-0 py-0" style="gap: 40px;">
            <section class="header-nav selected-nav" id="alur-booking" style="cursor:pointer" onclick="showContent(0)">
                Alur Booking
            </section>
            <section class="header-nav" id="alur-akun" style="cursor:pointer" onclick="showContent(1)">
                Pendaftaran dan Verifikasi Akun
            </section>
        </div>
        <hr class="py-0 my-0">
    </header>

    <script>
        function showContent(index) {
            // console.log()
            const content = [document.getElementById('booking-content'), document.getElementById('akun-content')];
            const button = [document.getElementById('alur-booking'), document.getElementById('alur-akun')]
            content[index].classList.remove('d-none');
            content[Math.abs(index - 1)].classList.add('d-none')
            button[index].classList.add('selected-nav');
            button[Math.abs(index - 1)].classList.remove('selected-nav')

        }
    </script>

    <section class="my-4" id="booking-content">
        <ol class="px-3">
            <dl>
                <dt>
                    <li>Registrasi Akun</li>
                </dt>
                <dd>
                    Akses laman website e-tiket TNKS melaui etikettnks.gov.id, lalu lakukan pendaftaran akun dan isi
                    biodata lengkap Anda. Selanjutnya Admin akan melakukan pengecekan terhadap biodata akun yang baru
                    terdaftar untuk memastikan data lengkap dan sesuai dengan kartu identitas yang diunggah.
                </dd>
            </dl>
            <dl>
                <dt>
                    <li>Booking Online</li>
                </dt>
                <dd>Login pada website e-tiket TNKS, lalu lakukan pemesanan tiket dengan memilih menu Booking Online,
                    selanjutnya pilih Destinasi Wisata Pendakian Gunung Kerinci. Masukan tanggal, jumlah pendaki, dan
                    jalur pendakian yang ingin anda tuju, serta cek kuota yang tersedia untuk memastikan kuota pendakian
                    masih ada pada tanggal yang Anda pilih.</dd>
            </dl>
            <dl>
                <dt>
                    <li>Isi Formulir Pendaftaran</li>
                </dt>
                <dd>Lengkapi formulir pendaftaran dengan memasukan kode pendaki yang akan melakukan pendakian. Pastikan
                    pendaki yang akan melakukan pendakian telah memiliki akun yang tervalidasi sebelumnya.</dd>
            </dl>
            <dl>
                <dt>
                    <li>Setujui Syarat dan Ketentuan</li>
                </dt>
                <dd>Baca dan setujui syarat serta ketentuan yang berlaku sesuai SOP pendakian Gunung Kerinci.</dd>
            </dl>
            <dl>
                <dt>
                    <li>Lakukan pembayaran dan unggah bukti pembayaran, kemudian tunggu validasi pembayaran oleh admin
                    </li>
                </dt>
                <dd>Lakukan pembayaran dan unggah bukti pembayaran, kemudian tunggu validasi pembayaran oleh admin.</dd>
            </dl>
            <dl>
                <dt>
                    <li>Gunakan QR Tiket</li>
                </dt>
                <dd>Setelah pembayaran berhasil divalidasi, anda akan mendapatkan kode QR tiket. Scan kode QR tersebut
                    di pintu masuk untuk memulai pendakian.</dd>
            </dl>
        </ol>

    </section>
    <section class="my-4 d-none" id="akun-content">
        <ol class="px-3">
            <dl>
                <dt>
                    <li>Klik Tombol Daftar</li>
                </dt>
                <dd>
                    Langkah pertama adalah membuka situs etikettnks.gov.id dan memilih menu Registrasi Akun atau tombol
                    Daftar untuk memulai proses pendaftaran.
                </dd>
            </dl>
            <dl>
                <dt>
                    <li>Masukkan Email dan Password</li>
                </dt>
                <dd>Isi alamat email yang aktif dan buat kata sandi untuk akun Anda. Pastikan menggunakan email yang
                    valid karena proses verifikasi akan dilakukan melalui email tersebut.</dd>
            </dl>
            <dl>
                <dt>
                    <li>Verifikasi Email</li>
                </dt>
                <dd>Setelah mengisi email dan password, Anda akan menerima email konfirmasi berisi tautan khusus
                    (one-link). Klik tautan tersebut untuk memverifikasi akun Anda dan melanjutkan ke langkah
                    berikutnya.</dd>
            </dl>
            <dl>
                <dt>
                    <li>Isi Biodata Lengkap</li>
                </dt>
                <dd>
                    Setelah verifikasi email berhasil, lengkapi biodata Anda di formulir pendaftaran. Informasi yang
                    perlu diisi meliputi:
                    <ul>
                        <li>Nama lengkap sesuai identitas.</li>
                        <li>Nomor KTP.</li>
                        <li>Tanggal lahir.</li>
                        <li>Alamat lengkap.</li>
                        <li>Surat keterangan sehat yang diunggah dalam format digital.</li>
                        <li>Suratizin orang tua, khusus bagi pendaki di bawah usia 18 tahun, yang juga harus diunggah.
                        </li>
                    </ul>
                </dd>
            </dl>
            <dl>
                <dt>
                    <li>Kirim dan Tunggu Verifikasi</li>
                </dt>
                <dd>Setelah semua biodata dan dokumen diisi serta diunggah, kirim formulir dengan mengklik tombol
                    Submit. Admin akan memeriksa data Anda untuk memastikan kelengkapan dan keabsahan dokumen sebelum
                    akun Anda dapat digunakan.</dd>
            </dl>
            <dl>
                <dt>
                    <li>Setelah biodata terverifikasi, akun Anda akan aktif dan siap digunakan untuk proses pemesanan
                        tiket.</li>
                </dt>
                <dd></dd>
            </dl>
        </ol>

    </section>

</div>
@endsection