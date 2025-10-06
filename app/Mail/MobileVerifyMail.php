<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class MobileVerifyMail extends Mailable
{
    use Queueable, SerializesModels;
    public $id;
    public $hash;
    public $redirectUrl;

    public function __construct($user)
    {
        $this->id = $user->getKey();
        $this->hash = sha1($user->getEmailForVerification());

        // Buat URL HTTPS redirect (agar tidak diblokir email client)
        $this->redirectUrl = url("/verify-redirect?id={$this->id}&hash={$this->hash}");
    }

    public function build()
    {
        return $this->subject('Verifikasi Email Akun Anda')
            ->markdown('email.mobile_Authverify')
            ->with([
                'redirectUrl' => $this->redirectUrl,
                'id' => $this->id,
                'hash' => $this->hash,
            ]);
    }
}
