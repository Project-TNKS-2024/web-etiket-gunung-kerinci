<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class MobileVerifyMail extends Mailable
{
    use Queueable, SerializesModels;

    public $url;

    /**
     * Buat instance baru.
     */
    public function __construct($user)
    {
        $hash = sha1($user->getEmailForVerification());
        // Deep link ke aplikasi mobile
        $this->url = "tnkas://verify-email?id={$user->getKey()}&hash={$hash}";
    }

    /**
     * Build pesan email.
     */
    public function build()
    {
        return $this->subject('Verifikasi Email Akun Anda')
            ->markdown('email.mobile_Authverify', [
                'url' => $this->url,
            ]);
    }
}
