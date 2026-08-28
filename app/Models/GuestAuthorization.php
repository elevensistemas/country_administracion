<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GuestAuthorization extends Model
{
    protected $fillable = [
        'lot_id', 'user_id', 'type', 'name', 'last_name', 
        'dni', 'license_plate', 'visit_date', 'visit_time', 
        'status', 'notes', 'qr_code'
    ];

    protected $casts = [
        'visit_date' => 'date',
    ];

    public function lot(): BelongsTo
    {
        return $this->belongsTo(Lot::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getFullNameAttribute(): string
    {
        return trim($this->name . ' ' . $this->last_name);
    }
}
