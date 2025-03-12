<x-mail::message>
   # Halo, {{ $nama }}

   @if ($status == 'verified')
   **Selamat!** Biodata Anda telah berhasil diverifikasi pada **{{ $tanggalVerifikasi }}**.
   Anda sekarang dapat mengakses layanan kami sepenuhnya.
   @else
   **Maaf,** biodata Anda tidak dapat diverifikasi pada **{{ $tanggalVerifikasi }}**.
   **Alasan:** {{ $keterangan }}
   @endif

   <x-mail::button :url="$url">
      Lihat Detail
   </x-mail::button>

   Terima kasih,<br>
   **{{ config('app.name') }}**
</x-mail::message>