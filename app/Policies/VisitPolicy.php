<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Visit;

class VisitPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Visit $visit): bool
    {
        if ($user->isAdmin() || $user->isSupervisor()) {
            return true;
        }

        return $user->id === $visit->technician_id;
    }

    public function create(User $user): bool
    {
        return $user->isAdmin() || $user->isSupervisor();
    }

    public function update(User $user, Visit $visit): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        if ($user->isSupervisor()) {
            return true;
        }

        return $user->id === $visit->technician_id;
    }

    public function delete(User $user, Visit $visit): bool
    {
        return $user->isAdmin();
    }
}
