<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class BiodataVerifiedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $biodata;
    /**
     * Create a new message instance.
     */
    public function __construct($biodata)
    {
        $this->biodata = $biodata;
    }


    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Biodata Verified Mail',
        );
    }

    public function build()
    {
        return $this->subject('Biodata Anda Telah Diverifikasi- ' . config('app.name'))
            ->markdown('email.BioValidasi', [
                'nama' => $this->biodata->fullName,
                'tanggalVerifikasi' => $this->biodata->verified_at->format('d M Y H:i'),
                'url' => route('user.dashboard.profile'),
            ]);
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'email.BioValidasi',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
