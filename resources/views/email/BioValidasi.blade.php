<x-mail::message>
   # Halo, {{ $nama }}

   @if ($status == 'verified')
   Selamat!

   Akun Anda telah berhasil diverifikasi pada {{ $tanggalVerifikasi }}.
   Anda kini mendapatkan ID Pendaki, yang dapat dilihat di halaman Profil dan digunakan untuk pemesanan tiket.
   @else
   Maaf, akun Anda tidak dapat diverifikasi karena {{ $keterangan }}.
   Silakan periksa kembali informasi Anda dan ajukan ulang verifikasi melalui halaman profil.

   Jika butuh bantuan, hubungi layanan pengguna yang ada di halaman Beranda
   @endif

   <x-mail::button :url="$url">
      Lihat Detail
   </x-mail::button>

   Terima kasih,<br>
   **{{ config('app.name') }}**
</x-mail::message>