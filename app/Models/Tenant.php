<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tenant extends Model
{
    use SoftDeletes;

    protected $fillable = ['name', 'last_name', 'dni', 'email', 'phone', 'status', 'notes'];

    public function lots(): HasMany
    {
        return $this->hasMany(Lot::class, 'current_tenant_id');
    }

    public function getFullNameAttribute(): string
    {
        return trim($this->name . ' ' . $this->last_name);
    }
}