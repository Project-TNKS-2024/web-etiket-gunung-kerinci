<x-mail::message>
   # Pembayaran Berhasil 🎉

   Halo **{{ $order['name'] }}**,

   Terima kasih telah melakukan pembayaran. Berikut adalah detail pesanan Anda:

   - **Kode Booking:** {{ $order['booking_code'] }}
   - **Jumlah Dibayar:** Rp{{ number_format($order['amount'], 0, ',', '.') }}
   - **Tanggal Pembayaran:** {{ $order['payment_date'] }}

   Anda dapat melihat detail pemesanan dengan menekan tombol di bawah ini:

   <x-mail::button :url="$order['invoice_url']">
      Lihat Detail Pembayaran
   </x-mail::button>

   Terima kasih telah menggunakan layanan kami! 😊

   Salam,
   **{{ config('app.name') }}**
</x-mail::message>