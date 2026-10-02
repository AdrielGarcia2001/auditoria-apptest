<?php

namespace App\Providers;

use App\Models\Ticket;
use App\Models\Visit;
use App\Policies\TicketPolicy;
use App\Policies\VisitPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        Ticket::class => TicketPolicy::class,
        Visit::class => VisitPolicy::class,
    ];

    public function boot(): void
    {
        //
    }
}
