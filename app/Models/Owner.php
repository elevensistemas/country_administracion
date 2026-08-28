<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Owner extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name', 'last_name', 'business_name', 'dni', 'cuit', 
        'email', 'email_alternate', 'phone', 'phone_alternate', 
        'address', 'status', 'notes', 'preferred_channel'
    ];

    public function lots(): HasMany
    {
        return $this->hasMany(Lot::class, 'current_owner_id');
    }

    public function functionalUnits(): BelongsToMany
    {
        return $this->belongsToMany(FunctionalUnit::class, 'owner_functional_unit')
                    ->withPivot('share_percentage');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function getFullNameAttribute(): string
    {
        return trim($this->name . ' ' . $this->last_name);
    }
}