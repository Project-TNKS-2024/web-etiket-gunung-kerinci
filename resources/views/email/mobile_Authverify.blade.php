@component('mail::message')
# Verifikasi Email

Klik tombol di bawah ini untuk verifikasi akun Anda di aplikasi TNKAS:

@component('mail::button', ['url' => $url])
Verifikasi Email
@endcomponent

Jika tombol tidak berfungsi, salin link berikut lalu buka di aplikasi:
{{ $url }}

Terima kasih,<br>
**Tim TNKAS**
@endcomponent