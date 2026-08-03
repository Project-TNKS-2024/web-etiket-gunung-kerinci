<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\URL;

class MobileVerifyMail extends Mailable
{
    use Queueable, SerializesModels;
    public string $verificationUrl;

    public function __construct($user)
    {
        $this->verificationUrl = URL::temporarySignedRoute(
            'api.auth.email.verify',
            now()->addMinutes((int) config('auth.email_verification.expire')),
            [
                'id' => $user->getKey(),
                'hash' => sha1($user->getEmailForVerification()),
            ],
        );
    }

    public function build()
    {
        return $this->subject('Verifikasi Email Akun Anda')
            ->markdown('email.mobile_Authverify')
            ->with([
                'verificationUrl' => $this->verificationUrl,
            ]);
    }
}
