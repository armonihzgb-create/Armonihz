<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NewHiringRequestEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $musicianName;
    public $clientName;
    public $eventDate;

    public function __construct($musicianName, $clientName, $eventDate)
    {
        $this->musicianName = $musicianName;
        $this->clientName = $clientName;
        $this->eventDate = $eventDate;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '¡Tienes una nueva solicitud de contratación! 🎸 - Armonihz',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.new-request',
        );
    }
}