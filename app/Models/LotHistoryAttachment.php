<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LotHistoryAttachment extends Model
{
    protected $fillable = ['lot_history_event_id', 'file_path', 'file_name'];

    public function event(): BelongsTo
    {
        return $this->belongsTo(LotHistoryEvent::class, 'lot_history_event_id');
    }
}