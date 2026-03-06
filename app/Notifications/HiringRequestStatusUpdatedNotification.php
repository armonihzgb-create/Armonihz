<?php

namespace App\Notifications;

use App\Models\HiringRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class HiringRequestStatusUpdatedNotification extends Notification implements ShouldQueue
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
            'type' => 'hiring_request_status_updated',
            'hiring_request_id' => $this->hiringRequest->id,
            'status' => $this->hiringRequest->status,
            'musician_name' => collect($this->hiringRequest->musicianProfile)->get('stage_name') ?? 'Musician',
            'message' => 'Your hiring request has been updated.',
        ];
    }
}
