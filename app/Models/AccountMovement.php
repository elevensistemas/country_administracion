<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class AccountMovement extends Model
{
    protected $fillable = [
        'functional_unit_id', 'type', 'date', 'amount', 
        'balance_after', 'description', 'related_model_type', 'related_model_id'
    ];

    protected $casts = [
        'date' => 'date',
    ];

    public function functionalUnit(): BelongsTo
    {
        return $this->belongsTo(FunctionalUnit::class);
    }

    public function related(): MorphTo
    {
        return $this->morphTo();
    }
}