<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'name',
    'code',
    'model',
    'serial_number',
    'location',
    'description',
    'installation_date',
    'status',
])]
class Asset extends Model
{
    use HasFactory;

    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class);
    }
}
