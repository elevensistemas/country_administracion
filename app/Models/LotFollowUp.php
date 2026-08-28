<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LotFollowUp extends Model
{
    protected $fillable = [
        'lot_history_event_id', 'lot_id', 'reason', 
        'assignee_id', 'due_date', 'priority', 'status', 
        'reminder_sent', 'notes'
    ];

    protected $casts = [
        'due_date' => 'date',
        'reminder_sent' => 'boolean',
    ];

    public function event(): BelongsTo
    {
        return $this->belongsTo(LotHistoryEvent::class, 'lot_history_event_id');
    }

    public function lot(): BelongsTo
    {
        return $this->belongsTo(Lot::class);
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assignee_id');
    }
}