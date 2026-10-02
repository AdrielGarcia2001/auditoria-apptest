<?php

namespace App\Providers;

use App\Events\TicketAssigned;
use App\Events\TicketStatusChanged;
use App\Events\VisitStatusChanged;
use App\Listeners\SendTicketAssignedNotification;
use App\Listeners\SendTicketNotification;
use App\Listeners\SendVisitNotification;
use App\Listeners\SendWhatsAppNotification;
use App\Listeners\SendWhatsAppVisitNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        TicketStatusChanged::class => [
            SendTicketNotification::class,
            SendWhatsAppNotification::class,
        ],
        VisitStatusChanged::class => [
            SendVisitNotification::class,
            SendWhatsAppVisitNotification::class,
        ],
        TicketAssigned::class => [
            SendTicketAssignedNotification::class,
            SendWhatsAppNotification::class,
        ],
    ];

    public function boot(): void
    {
        //
    }

    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
