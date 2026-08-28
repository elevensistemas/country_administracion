<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LotHistoryCategory extends Model
{
    protected $fillable = ['name', 'display_name'];

    public function events(): HasMany
    {
        return $this->hasMany(LotHistoryEvent::class, 'category_id');
    }
}