<?php

namespace App\Policies;

use App\Models\Ticket;
use App\Models\User;

class TicketPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Ticket $ticket): bool
    {
        if ($user->isAdmin() || $user->isSupervisor()) {
            return true;
        }

        return $user->id === $ticket->assigned_to
            || $user->id === $ticket->created_by
            || $ticket->crew?->members->contains($user);
    }

    public function create(User $user): bool
    {
        return $user->isAdmin() || $user->isSupervisor();
    }

    public function update(User $user, Ticket $ticket): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        if ($user->isSupervisor()) {
            return true;
        }

        return $user->id === $ticket->assigned_to;
    }

    public function delete(User $user, Ticket $ticket): bool
    {
        return $user->isAdmin();
    }
}
