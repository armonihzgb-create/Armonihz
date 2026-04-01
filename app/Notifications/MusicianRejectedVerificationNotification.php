<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class MusicianRejectedVerificationNotification extends Notification
{
    use Queueable;

    protected $musicianProfile;
    protected $rejectionReason;

    public function __construct($musicianProfile, string $rejectionReason)
    {
        $this->musicianProfile  = $musicianProfile;
        $this->rejectionReason  = $rejectionReason;
    }

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        $stageName = $this->musicianProfile->stage_name ?? $notifiable->name;

        return (new MailMessage)
            ->subject('Actualización sobre tu verificación de identidad - Armonihz')
            ->view('emails.musician-rejected', [
                'user'            => $notifiable,
                'stageName'       => $stageName,
                'rejectionReason' => $this->rejectionReason,
                'retryUrl'        => url('/musician/verification'),
            ]);
    }
}
