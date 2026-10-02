<?php

namespace App\Notifications;

use App\Models\Visit;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class VisitNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Visit $visit,
        public string $message,
        public string $type = 'info',
    ) {}

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'visit_id' => $this->visit->id,
            'ticket_id' => $this->visit->ticket_id,
            'message' => $this->message,
            'type' => $this->type,
            'url' => route('visits.show', $this->visit->id),
        ];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("Visita #{$this->visit->id} - Actualización")
            ->line($this->message)
            ->action('Ver Visita', route('visits.show', $this->visit->id))
            ->line('Gracias por usar nuestro sistema de auditorías.');
    }
}
