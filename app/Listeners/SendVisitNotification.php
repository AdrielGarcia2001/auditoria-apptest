<?php

namespace App\Listeners;

use App\Events\VisitStatusChanged;
use App\Models\User;
use App\Notifications\VisitNotification;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendVisitNotification implements ShouldQueue
{
    public function handle(VisitStatusChanged $event): void
    {
        $visit = $event->visit->load(['technician', 'ticket']);

        $recipients = collect();

        if ($visit->technician) {
            $recipients->push($visit->technician);
        }

        if ($visit->ticket) {
            if ($visit->ticket->assignee) {
                $recipients->push($visit->ticket->assignee);
            }
            if ($visit->ticket->creator) {
                $recipients->push($visit->ticket->creator);
            }
        }

        $recipients->unique('id')->each(function (User $user) use ($event) {
            $user->notify(new VisitNotification(
                $event->visit,
                "La visita #{$event->visit->id} cambió de estado de {$event->oldStatus} a {$event->newStatus}.",
                'info'
            ));
        });
    }
}
