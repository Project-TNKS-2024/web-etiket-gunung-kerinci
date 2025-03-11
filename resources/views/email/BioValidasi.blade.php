<x-mail::message>
   # Halo, {{ $nama }}

   @if ($status == 'verified')
   {!! '<b>Selamat!</b>' !!} Biodata Anda telah berhasil diverifikasi pada {!! '<b>' . $tanggalVerifikasi . '</b>' !!}.
   Anda sekarang dapat mengakses layanan kami sepenuhnya.
   @else
   {!! '<b>Maaf,</b>' !!} biodata Anda tidak dapat diverifikasi pada {!! '<b>' . $tanggalVerifikasi . '</b>' !!}.
   {!! '<b>Alasan:</b>' !!} {{ $keterangan }}
   @endif

   <x-mail::button :url="$url">
      Lihat Detail
   </x-mail::button>

   Terima kasih,
   {!! '<b>' . config('app.name') . '</b>' !!}
</x-mail::message>