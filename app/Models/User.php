<?php

namespace App\Models;

use App\Enums\UserRole;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password', 'role', 'phone', 'is_active', 'approved_at'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'role' => UserRole::class,
            'is_active' => 'boolean',
            'approved_at' => 'datetime',
        ];
    }

    public function crews(): BelongsToMany
    {
        return $this->belongsToMany(Crew::class);
    }

    public function supervisedCrews(): HasMany
    {
        return $this->hasMany(Crew::class, 'supervisor_id');
    }

    public function createdTickets(): HasMany
    {
        return $this->hasMany(Ticket::class, 'created_by');
    }

    public function assignedTickets(): HasMany
    {
        return $this->hasMany(Ticket::class, 'assigned_to');
    }

    public function visits(): HasMany
    {
        return $this->hasMany(Visit::class, 'technician_id');
    }

    public function uploadedEvidence(): HasMany
    {
        return $this->hasMany(Evidence::class, 'uploaded_by');
    }

    public function isApproved(): bool
    {
        return $this->approved_at !== null;
    }

    public function isAdmin(): bool
    {
        return $this->role === UserRole::Admin;
    }

    public function isSupervisor(): bool
    {
        return $this->role === UserRole::Supervisor;
    }

    public function isTechnician(): bool
    {
        return $this->role === UserRole::Technician;
    }
}
