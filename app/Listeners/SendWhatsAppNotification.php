<?php

namespace App\Listeners;

use App\Events\TicketAssigned;
use App\Services\WhatsAppService;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendWhatsAppNotification implements ShouldQueue
{
    public function __construct(
        protected WhatsAppService $whatsApp,
    ) {}

    public function handle(TicketAssigned $event): void
    {
        $user = $event->ticket->assignee;

        if (! $user || ! $user->phone) {
            return;
        }

        $this->whatsApp->sendTicketStatusChanged(
            $user->phone,
            $event->ticket->ticket_number,
            'nuevo',
            'asignado'
        );
    }
}
