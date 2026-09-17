<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class GuestAuthorization extends Model
{
    protected $fillable = [
        'uuid', 'lot_id', 'user_id', 'type', 'name', 'last_name', 
        'dni', 'license_plate', 'visit_date', 'visit_time', 
        'valid_from', 'valid_until', 'status', 'cancelled_at',
        'notes', 'qr_code'
    ];

    protected $casts = [
        'visit_date' => 'date',
        'valid_from' => 'datetime',
        'valid_until' => 'datetime',
        'cancelled_at' => 'datetime',
    ];

    protected static function booted()
    {
        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = (string) Str::uuid();
            }
            if (empty($model->qr_code)) {
                $model->qr_code = $model->uuid;
            }
        });
    }

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

    public function isCurrentlyValid(): bool
    {
        if ($this->status !== 'active') {
            return false;
        }

        $now = now();
        if ($this->valid_from && $now->lt($this->valid_from)) {
            return false;
        }
        if ($this->valid_until && $now->gt($this->valid_until)) {
            return false;
        }

        return true;
    }
}