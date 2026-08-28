<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LotVehicle extends Model
{
    protected $fillable = [
        'lot_id', 'brand', 'model', 'color', 'license_plate'
    ];

    public function lot(): BelongsTo
    {
        return $this->belongsTo(Lot::class);
    }
}
