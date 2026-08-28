<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OwnershipHistory extends Model
{
    protected $table = 'ownership_history';

    protected $fillable = ['lot_id', 'owner_id', 'start_date', 'end_date', 'reason', 'documents', 'user_id', 'notes'];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function lot(): BelongsTo
    {
        return $this->belongsTo(Lot::class);
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(Owner::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}