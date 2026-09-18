<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use App\Traits\Auditable;

class Communication extends Model
{
    use Auditable;
    protected $fillable = ['title', 'content', 'attachment_path', 'channels', 'target_type', 'sent_by', 'scheduled_at', 'sent_at'];

    protected $casts = [
        'scheduled_at' => 'datetime',
        'sent_at' => 'datetime',
    ];

    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sent_by');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sent_by');
    }

    public function recipients(): HasMany
    {
        return $this->hasMany(CommunicationRecipient::class);
    }

    public function deliveries(): HasManyThrough
    {
        return $this->hasManyThrough(CommunicationDelivery::class, CommunicationRecipient::class);
    }
}