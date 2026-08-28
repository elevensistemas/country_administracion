<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CommunicationRecipient extends Model
{
    protected $fillable = ['communication_id', 'user_id', 'lot_id', 'preferred_channel', 'email', 'phone', 'status'];

    public function communication(): BelongsTo
    {
        return $this->belongsTo(Communication::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function lot(): BelongsTo
    {
        return $this->belongsTo(Lot::class);
    }

    public function deliveries(): HasMany
    {
        return $this->hasMany(CommunicationDelivery::class);
    }
}