<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification as FirebaseNotification;
use Kreait\Laravel\Firebase\Facades\Firebase;

class HiringRequestStatusNotification extends Notification
{
    use Queueable;

    protected $hiringRequest;
    protected $status;

    public function __construct($hiringRequest, $status)
    {
        $this->hiringRequest = $hiringRequest;
        $this->status = $status; // 'accepted', 'rejected', o 'counter_offer'
    }

    public function via($notifiable)
    {
        return ['database']; // Guarda en la tabla notifications de Laravel
    }

    // Lógica para armar el título y mensaje según el estado
    private function getNotificationData()
    {
        // Asumiendo que tu relación se llama 'musician' en el modelo HiringRequest
        $nombreMusico = $this->hiringRequest->musician->name ?? 'El músico';

        switch ($this->status) {
            case 'accepted':
                return [
                    'title' => '¡Solicitud Aceptada! 🎉',
                    'message' => "{$nombreMusico} ha aceptado tu oferta para el evento."
                ];
            case 'rejected':
                return [
                    'title' => 'Solicitud Rechazada 😔',
                    'message' => "Lo sentimos, {$nombreMusico} ha declinado tu solicitud."
                ];
            case 'counter_offer':
                return [
                    'title' => 'Nueva Contraoferta 💬',
                    'message' => "{$nombreMusico} te ha enviado una contraoferta. Revisa los detalles."
                ];
            default:
                return [
                    'title' => 'Actualización de solicitud',
                    'message' => "El estado de tu solicitud con {$nombreMusico} ha cambiado."
                ];
        }
    }

    public function toDatabase($notifiable)
    {
        $data = $this->getNotificationData();

        return [
            'title' => $data['title'],
            'message' => $data['message'],
            'hiring_request_id' => $this->hiringRequest->id,
            'status' => $this->status,
        ];
    }

    // Tu mismo método para enviar el Push
    public function sendPush($notifiable)
    {
        if (!$notifiable->fcm_token) {
            return;
        }

        $data = $this->getNotificationData();
        $messaging = Firebase::messaging();

        $message = CloudMessage::withTarget('token', $notifiable->fcm_token)
            ->withNotification(
                FirebaseNotification::create(
                    $data['title'],
                    $data['message']
                )
            )
            // Opcional: mandar datos extra ocultos a la app móvil
            ->withData([
                'hiring_request_id' => (string) $this->hiringRequest->id,
                'status' => $this->status
            ])
            ->withHighestPossiblePriority();

        try {
            $messaging->send($message);
        } catch (\Exception $e) {
            \Log::error('Error FCM en HiringRequest: ' . $e->getMessage());
        }
    }
}