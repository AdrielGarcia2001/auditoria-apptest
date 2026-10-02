<?php

namespace App\Listeners;

use App\Events\TicketAssigned;
use App\Models\User;
use App\Notifications\TicketNotification;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendTicketAssignedNotification implements ShouldQueue
{
    public function handle(TicketAssigned $event): void
    {
        $user = User::find($event->assignedTo);

        if ($user) {
            $user->notify(new TicketNotification(
                $event->ticket,
                "Se te ha asignado el ticket {$event->ticket->ticket_number}.",
                'assignment'
            ));
        }
    }
}
