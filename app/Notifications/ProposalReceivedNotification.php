<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use App\Models\CastingApplication;

class ProposalReceivedNotification extends Notification
{
    use Queueable;

    protected $application;

    /**
     * Create a new notification instance.
     */
    public function __construct(CastingApplication $application)
    {
        $this->application = $application;
    }

    /**
     * Get the notification's delivery channels.
     * En este caso, usaremos 'fcm' para notificaciones Push
     * y 'database' para que se guarde el historial en tu app.
     */
    public function via($notifiable)
    {
        return ['database']; // Aquí añadiremos 'fcm' más adelante cuando instales el paquete de Firebase.
    }

    /**
     * Estructura para guardar en la base de datos (Historial de notificaciones)
     */
    public function toDatabase($notifiable)
    {
        return [
            'type' => 'new_proposal',
            'application_id' => $this->application->id,
            'event_id' => $this->application->client_event_id,
            'message' => '¡Tienes una nueva propuesta para tu evento "' . $this->application->event->nombre . '"!',
            'musician_name' => $this->application->musicianProfile->stage_name ?? 'Un músico',
        ];
    }
}