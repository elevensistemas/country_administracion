<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Ticket extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'lot_id', 'user_id', 'category_id', 'title', 
        'description', 'status', 'priority', 'assigned_to', 
        'rating', 'rating_comments', 'source_channel'
    ];

    public function lot(): BelongsTo
    {
        return $this->belongsTo(Lot::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class); // Creator
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(TicketCategory::class, 'category_id');
    }

    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function messages(): HasMany
    {
        return $this->hasMany(TicketMessage::class);
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(TicketAttachment::class);
    }

    public function internalNotes(): HasMany
    {
        return $this->hasMany(TicketInternalNote::class);
    }
}