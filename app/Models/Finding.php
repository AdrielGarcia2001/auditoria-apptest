<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

#[Fillable(['visit_id', 'title', 'description', 'severity', 'status', 'recommendation'])]
class Finding extends Model
{
    use HasFactory;

    public function visit(): BelongsTo
    {
        return $this->belongsTo(Visit::class);
    }

    public function evidence(): MorphMany
    {
        return $this->morphMany(Evidence::class, 'evidencable');
    }
}
