<?php

namespace App\Listeners;

use App\Events\VisitStatusChanged;
use App\Services\WhatsAppService;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendWhatsAppVisitNotification implements ShouldQueue
{
    public function __construct(
        protected WhatsAppService $whatsApp,
    ) {}

    public function handle(VisitStatusChanged $event): void
    {
        $visit = $event->visit->load(['technician', 'ticket']);

        if (! $visit->technician || ! $visit->technician->phone) {
            return;
        }

        if ($event->newStatus === 'scheduled') {
            $this->whatsApp->sendVisitAssignedNotification(
                $visit->technician->phone,
                $visit->ticket->ticket_number,
                $visit->ticket->title,
                $visit->scheduled_at?->format('d/m/Y H:i') ?? 'No definida',
                $visit->ticket->asset->location ?? ''
            );
        }

        if ($event->newStatus === 'completed') {
            $this->whatsApp->sendVisitCompletedNotification(
                $visit->technician->phone,
                $visit->ticket->ticket_number,
                $visit->type->value,
                $visit->completed_at?->format('d/m/Y H:i') ?? now()->format('d/m/Y H:i'),
                $visit->technician->name
            );
        }
    }
}
