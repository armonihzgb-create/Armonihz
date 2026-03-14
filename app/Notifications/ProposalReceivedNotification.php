<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\DatabaseMessage;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification as FirebaseNotification;
use Kreait\Laravel\Firebase\Facades\Firebase;

class ProposalReceivedNotification extends Notification
{
    use Queueable;

    protected $application;

    public function __construct($application)
    {
        $this->application = $application;
    }

    public function via($notifiable)
    {
        return ['database']; // guardamos también en BD
    }

    public function toDatabase($notifiable)
    {
        return [
            'title' => 'Nueva propuesta 🎵',
            'message' => 'Un músico envió una propuesta para tu evento',
            'event_id' => $this->application->client_event_id,
        ];
    }

    public function sendPush($notifiable)
    {
        if (!$notifiable->fcm_token) {
            return;
        }

        $messaging = Firebase::messaging();

        $message = CloudMessage::withTarget('token', $notifiable->fcm_token)
            ->withNotification(
                FirebaseNotification::create(
                    'Nueva propuesta 🎵',
                    'Un músico envió una propuesta para tu evento'
                )
            )
            ->withHighestPossiblePriority();

        $messaging->send($message);
    }

    public function send($notifiable)
    {
        $this->sendPush($notifiable);
    }
}