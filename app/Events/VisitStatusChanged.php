<?php

namespace App\Events;

use App\Models\Visit;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class VisitStatusChanged
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public Visit $visit,
        public string $oldStatus,
        public string $newStatus,
    ) {}
}
