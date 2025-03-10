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
    public $status;

    /**
     * Create a new message instance.
     */
    public function __construct($biodata, $status)
    {
        $this->biodata = $biodata;
        $this->status = $status;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Status Verifikasi Biodata - ' . config('app.name'),
        );
    }

    public function build()
    {
        return $this->subject('Status Verifikasi Biodata Anda - ' . config('app.name'))
            ->markdown('email.BioValidasi', [
                'nama' => $this->biodata->fullName,
                'tanggalVerifikasi' => $this->biodata->verified_at->format('d M Y H:i'),
                'status' => $this->status,
                'keterangan' => $this->biodata->keterangan ?? '-',
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
