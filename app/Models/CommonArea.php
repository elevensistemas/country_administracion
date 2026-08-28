<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CommonArea extends Model
{
    protected $fillable = [
        'name', 'description', 'capacity', 'is_active', 
        'price', 'requires_approval', 'rules', 'schedule_start', 
        'schedule_end', 'duration_minutes', 'maintenance_blocked_days', 'photos'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'requires_approval' => 'boolean',
        'maintenance_blocked_days' => 'array',
        'photos' => 'array',
        'price' => 'decimal:2',
    ];

    public function reservations(): HasMany
    {
        return $this->hasMany(Reservation::class);
    }
}
