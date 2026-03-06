<?php

namespace App\Notifications;

use App\Models\HiringRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class HiringRequestCreatedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $hiringRequest;

    /**
     * Create a new notification instance.
     */
    public function __construct(HiringRequest $hiringRequest)
    {
        $this->hiringRequest = $hiringRequest;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'hiring_request_created',
            'hiring_request_id' => $this->hiringRequest->id,
            'event_date' => $this->hiringRequest->event_date,
            'client_name' => collect($this->hiringRequest->client)->get('name') ?? 'Client',
            'message' => 'You have received a new hiring request.',
        ];
    }
}
