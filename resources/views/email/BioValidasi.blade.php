<x-mail::message>
   # Halo, {{ $nama }}

   Selamat! Biodata Anda telah berhasil diverifikasi pada **{{ $tanggalVerifikasi }}**.

   Terima kasih telah menggunakan layanan kami.

   <x-mail::button :url="$url">
      Lihat Detail
   </x-mail::button>

   Salam,<br>
   **{{ config('app.name') }}**
</x-mail::message>