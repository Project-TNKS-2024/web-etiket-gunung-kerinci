<x-mail::message>
   # Reset Password

   Kami menerima permintaan reset password untuk akun Anda. Klik tombol di bawah untuk mengatur ulang password Anda:

   <x-mail::button :url="$url">
      Reset Password
   </x-mail::button>

   Jika Anda tidak meminta reset password, abaikan email ini.

   Terima kasih,<br>
   {{ config('app.name') }}
</x-mail::message>