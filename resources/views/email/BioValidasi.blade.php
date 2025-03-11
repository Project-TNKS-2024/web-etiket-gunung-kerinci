<x-mail::message>
   # Halo, {{ $nama }}

   @if ($status == 'verified')
   <strong>Selamat!</strong> Biodata Anda telah berhasil diverifikasi pada <strong>{{ $tanggalVerifikasi }}</strong>.
   Anda sekarang dapat mengakses layanan kami sepenuhnya.
   @else
   <strong>Maaf,</strong> biodata Anda tidak dapat diverifikasi pada <strong>{{ $tanggalVerifikasi }}</strong>.
   <strong>Alasan:</strong> {{ $keterangan }}
   @endif

   <x-mail::button :url="$url">
      Lihat Detail
   </x-mail::button>

   Terima kasih,<br>
   **{{ config('app.name') }}**
</x-mail::message>