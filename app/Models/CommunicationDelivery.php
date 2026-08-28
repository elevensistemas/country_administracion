<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CommunicationDelivery extends Model
{
    protected $fillable = [
        'communication_recipient_id', 'channel', 'status', 
        'provider_message_id', 'delivered_at', 'read_at', 
        'error_message', 'retry_count'
    ];

    protected $casts = [
        'delivered_at' => 'datetime',
        'read_at' => 'datetime',
    ];

    public function recipient(): BelongsTo
    {
        return $this->belongsTo(CommunicationRecipient::class, 'communication_recipient_id');
    }
}