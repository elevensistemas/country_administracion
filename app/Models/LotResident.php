<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LotResident extends Model
{
    protected $fillable = [
        'lot_id', 'name', 'last_name', 'dni', 'phone', 'email', 'relationship'
    ];

    public function lot(): BelongsTo
    {
        return $this->belongsTo(Lot::class);
    }

    public function getFullNameAttribute(): string
    {
        return trim($this->name . ' ' . $this->last_name);
    }
}
