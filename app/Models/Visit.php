<?php

namespace App\Models;

use App\Enums\VisitStatus;
use App\Enums\VisitType;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

#[Fillable([
    'ticket_id',
    'technician_id',
    'type',
    'status',
    'scheduled_at',
    'started_at',
    'completed_at',
    'start_latitude',
    'start_longitude',
    'end_latitude',
    'end_longitude',
    'notes',
])]
class Visit extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'type' => VisitType::class,
            'status' => VisitStatus::class,
            'scheduled_at' => 'datetime',
            'started_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    public function ticket(): BelongsTo
    {
        return $this->belongsTo(Ticket::class);
    }

    public function technician(): BelongsTo
    {
        return $this->belongsTo(User::class, 'technician_id');
    }

    public function checklists(): HasMany
    {
        return $this->hasMany(Checklist::class);
    }

    public function findings(): HasMany
    {
        return $this->hasMany(Finding::class);
    }

    public function evidence(): MorphMany
    {
        return $this->morphMany(Evidence::class, 'evidencable');
    }
}
