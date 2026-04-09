<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CounterOfferAcceptedEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $musicianName;
    public $clientName;
    public $eventDate;
    public $agreedPrice;

    public function __construct($musicianName, $clientName, $eventDate, $agreedPrice)
    {
        $this->musicianName = $musicianName;
        $this->clientName = $clientName;
        $this->eventDate = $eventDate;
        $this->agreedPrice = $agreedPrice;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '¡Tu contraoferta fue aceptada! 🎉 - Armonihz',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.counter-offer-accepted',
        );
    }
}