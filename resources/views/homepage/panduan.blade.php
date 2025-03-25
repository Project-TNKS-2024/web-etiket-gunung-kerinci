@extends('homepage.template.index')


@section('css')
<style>

</style>
@endsection

@section('main')
@include('homepage.template.header', [
'title' => 'Alur Pemesanan Tiket Wisata',
'caption' => 'Taman Nasional Kerinci Seblat',
])

<div class="container my-5" style="padding:  50px;">
    <header class="">
        <div class="d-flex my-0 py-0" style="gap: 40px;">
            <section class="header-nav selected-nav" id="alur-booking" style="cursor:pointer" onclick="showContent(0)">
                Alur Pemesanan Tiket
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
                    Akses laman website e-tiket TNKS, lalu lakukan pendaftaran akun dan isi biodata Anda dengan lengkap. Selanjutnya Admin akan melakukan pengecekan terhadap biodata akun yang baru Anda daftarkan untuk memastikan data lengkap dan sesuai dengan kartu identitas yang diunggah.
                </dd>
            </dl>
            <dl>
                <dt>
                    <li>Pesan Tiket</li>
                </dt>
                <dd>
                    Masuk pada website e-tiket TNKS menggunakan akun yang Anda miliki, lalu lakukan pemesanan tiket dengan memilih menu Pesan Tiket. Selanjutnya pilih Destinasi Wisata Pendakian Gunung Kerinci, asukan tanggal, jumlah pendaki, dan jalur pendakian yang ingin anda tuju, serta cek kuota yang tersedia untuk memastikan kuota pendakian masih ada pada tanggal yang Anda pilih.

                </dd>
            </dl>
            <dl>
                <dt>
                    <li>Isi Formulir Pendaftaran</li>
                </dt>
                <dd>Lengkapi formulir pendaftaran dengan memasukan kode pendaki yang akan melakukan pendakian. Pastikan pendaki yang akan melakukan pendakian telah memiliki akun yang tervalidasi sebelumnya.
                </dd>
            </dl>
            <dl>
                <dt>
                    <li>Setujui Syarat dan Ketentuan</li>
                </dt>
                <dd>Baca dan setujui syarat serta ketentuan yang berlaku sesuai SOP pendakian Gunung Kerinci.
                </dd>
            </dl>
            <dl>
                <dt>
                    <li>Proses Pembayaran
                    </li>
                </dt>
                <dd>
                    Pastikan tidak ada kesalahan pada data pendaki dan rincian pemesanan tiket yang Anda pesan, lalu pilih metode pembayaran yang Anda inginkan dan lakukan pembayaran sesuai dengan metode pembayaran yang Anda pilih. Selanjutnya, unggah bukti pembayaran dan kemudian tunggu validasi pembayaran oleh admin.
                </dd>
            </dl>
            <dl>
                <dt>
                    <li>Gunakan QR Tiket</li>
                </dt>
                <dd>
                    Setelah pembayaran berhasil divalidasi, anda akan mendapatkan kode QR tiket untuk masuk ke wilayah pendakian Gunung Kerinci. Scan kode QR tersebut di pintu masuk untuk memulai pendakian.
                </dd>
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
                    Langkah pertama yang dilakukan untuk memiliki akun pada website E-tiket TNKS adalah dengan mengakses laman e-tiket TNKS, lalu klik tombol Daftar untuk memulai proses pendaftaran.
                </dd>
            </dl>
            <dl>
                <dt>
                    <li>Pilih Cara Pendfartaran Akun</li>
                </dt>
                <dd>
                    <ul>
                        <li><b>Daftar Akun pada sistem E-tiket TNKS</b>.
                            Isi alamat email yang aktif dan buat kata sandi untuk akun Anda.
                            Pastikan anda menggunakan email yang valid karena proses verifikasi akan dilakukan melalui email tersebut.
                            Selanjutnya lakukan konfirmasi akun dengan mengklik tautan khusus (one-link) yang dikirimkan ke email yang Anda daftarkan.
                            Masuk ke sistem menggunakan email dan kata sandi yang telah didaftarkan.</li>
                        <li><b>Daftar dengan Akun Google</b>.
                            Klik tombol "Daftar dengan Akun Google", setelah itu masukkan email dan kata sandi Akun Google Anda.
                            Setelah itu Anda akan langsung memperoleh halaman akun pada website e-tiket TNKS.
                        </li>
                    </ul>
                </dd>
            </dl>
            <dl>
                <dt>
                    <li>Isi Biodata Lengkap</li>
                </dt>
                <dd>
                    Setelah Anda berhasil masuk, Anda wajib melengkapi profil biodata Anda. Informasi yang perlu diisi meliputi:
                    <ul>
                        <li>Nama lengkap sesuai identitas.</li>
                        <li>Kewarganegaraan.</li>
                        <li>Nomor Identitas.</li>

                        <li>Lampiran Identitas (KTP / KK / Passpor ).</li>
                        <li>Jenis Kelamin.</li>
                        <li>Tanggal lahir.</li>
                        <li>Alamat Domisili (khusus WNI).</li>
                        </li>
                    </ul>
                </dd>
                <dd>
                    Setelah semua biodata dan dokumen diisi serta diunggah, kirim formulir dengan mengklik tombol Verifikasi Profil. Selanjutnya Admin akan memeriksa data Anda untuk memastikan kelengkapan dan keabsahan dokumen sebelum akun Anda dapat digunakan.
                    Setelah biodata terverifikasi, Anda akan menerima email pemberitahuan bahwa akun Anda telah aktif dan siap digunakan untuk proses pemesanan tiket.
                </dd>
            </dl>

        </ol>

    </section>

</div>
@endsection