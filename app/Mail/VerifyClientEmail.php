<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class VerifyClientEmail extends Mailable
{
    use Queueable, SerializesModels;

    // 1. Declarar las variables públicas para que la vista Blade pueda leerlas
    public $clientName;
    public $verificationLink;

    /**
     * Create a new message instance.
     */
    public function __construct($clientName, $verificationLink)
    {
        // 2. Asignar los valores cuando se crea el correo
        $this->clientName = $clientName;
        $this->verificationLink = $verificationLink;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            // 3. Este será el asunto que el usuario verá en su bandeja de entrada
            subject: '¡Verifica tu correo electrónico! - Armonihz', 
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            // 4. Asegúrate de que esta ruta coincida con la ubicación de tu archivo Blade
            // Esto asume que el archivo está en resources/views/emails/verify-client.blade.php
            view: 'emails.verify-client', 
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