<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class WhatsAppNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public string $message,
        public ?string $phoneNumber = null,
    ) {}

    public function via(object $notifiable): array
    {
        return ['whatsapp'];
    }

    public function toWhatsApp(object $notifiable): array
    {
        return [
            'message' => $this->message,
            'phone_number' => $this->phoneNumber ?? $notifiable->phone,
        ];
    }

    public function shouldSend(object $notifiable): bool
    {
        return ! empty($this->phoneNumber ?? $notifiable->phone);
    }
}
