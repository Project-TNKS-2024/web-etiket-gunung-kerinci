<x-mail::message>
   # Halo, {{ $order['name'] }}

   Kami ingin memberitahu bahwa pembayaran untuk booking dengan ID **{{ $order['booking_code'] }}** gagal diproses.

   ### Detail Pembayaran:
   - **Jumlah:** Rp{{ number_format($order['amount'], 0, ',', '.') }}
   - **Status Pembayaran:** ❌ Gagal

   Silakan coba lagi atau hubungi admin jika ada kendala.

   <x-mail::button :url="route('homepage.booking.struk', $order['booking_code'])">
      Cek Detail Booking
   </x-mail::button>

   Terima kasih,<br>
   **{{ config('app.name') }}**
</x-mail::message>