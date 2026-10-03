<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class InviteMail extends Mailable
{
    public function __construct(public string $name, public string $businessName, public string $url) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Set your '.config('app.name').' password');
    }

    public function content(): Content
    {
        return new Content(markdown: 'mail.invite');
    }
}
