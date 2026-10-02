<?php

namespace App\Listeners;

use App\Events\TicketStatusChanged;
use App\Models\User;
use App\Notifications\TicketNotification;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendTicketNotification implements ShouldQueue
{
    public function handle(TicketStatusChanged $event): void
    {
        $ticket = $event->ticket->load(['assignee', 'creator', 'crew.members']);

        $recipients = collect();

        if ($ticket->assignee) {
            $recipients->push($ticket->assignee);
        }

        if ($ticket->creator) {
            $recipients->push($ticket->creator);
        }

        if ($ticket->crew) {
            $recipients->push(...$ticket->crew->members);
        }

        $recipients->unique('id')->each(function (User $user) use ($event) {
            $user->notify(new TicketNotification(
                $event->ticket,
                "El ticket {$event->ticket->ticket_number} cambió de estado de {$event->oldStatus} a {$event->newStatus}.",
                'info'
            ));
        });
    }
}
