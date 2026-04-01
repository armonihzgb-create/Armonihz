<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class MusicianVerifiedNotification extends Notification
{
    use Queueable;

    protected $musicianProfile;

    public function __construct($musicianProfile)
    {
        $this->musicianProfile = $musicianProfile;
    }

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        $stageName = $this->musicianProfile->stage_name ?? $notifiable->name;

        return (new MailMessage)
            ->subject('¡Tu perfil ha sido verificado! ✅ - Armonihz')
            ->view('emails.musician-verified', [
                'user'        => $notifiable,
                'stageName'   => $stageName,
                'dashboardUrl' => url('/dashboard'),
            ]);
    }
}
