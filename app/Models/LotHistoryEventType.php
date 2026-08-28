<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LotHistoryEventType extends Model
{
    protected $fillable = ['name', 'display_name'];

    public function events(): HasMany
    {
        return $this->hasMany(LotHistoryEvent::class, 'event_type_id');
    }
}