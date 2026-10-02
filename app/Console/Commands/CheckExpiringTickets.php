<?php

namespace App\Console\Commands;

use App\Models\Ticket;
use App\Models\User;
use App\Notifications\WhatsAppNotification;
use App\Services\WhatsAppService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class CheckExpiringTickets extends Command
{
    protected $signature = 'tickets:check-expiring';

    protected $description = 'Verifica tickets por vencer y envía notificaciones';

    public function handle(WhatsAppService $whatsApp): int
    {
        $expiringSoon = Carbon::now()->addHours(24);

        $tickets = Ticket::with(['assignee', 'creator', 'crew.members'])
            ->where('due_date', '<=', $expiringSoon)
            ->where('due_date', '>', Carbon::now())
            ->whereNotIn('status', ['completed', 'verified', 'cancelled'])
            ->get();

        $this->info("{$tickets->count()} tickets por vencer en las próximas 24 horas.");

        foreach ($tickets as $ticket) {
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

            $recipients->unique('id')->each(function (User $user) use ($ticket, $whatsApp) {
                if ($user->phone) {
                    $whatsApp->sendTicketExpiringAlert(
                        $user->phone,
                        $ticket->ticket_number,
                        $ticket->title,
                        $ticket->due_date->format('d/m/Y H:i')
                    );

                    $user->notify(new WhatsAppNotification(
                        "Ticket {$ticket->ticket_number} por vencer",
                        $user->phone
                    ));
                }
            });
        }

        return Command::SUCCESS;
    }
}
