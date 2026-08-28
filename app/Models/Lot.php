<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Lot extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'code', 'number', 'name', 'internal_address', 
        'status', 'current_owner_id', 'current_tenant_id', 
        'balance', 'notes'
    ];

    public function owner(): BelongsTo
    {
        return $this->belongsTo(Owner::class, 'current_owner_id');
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class, 'current_tenant_id');
    }

    public function functionalUnits(): HasMany
    {
        return $this->hasMany(FunctionalUnit::class);
    }

    public function historyEvents(): HasMany
    {
        return $this->hasMany(LotHistoryEvent::class);
    }

    public function followUps(): HasMany
    {
        return $this->hasMany(LotFollowUp::class);
    }

    public function ownershipHistory(): HasMany
    {
        return $this->hasMany(OwnershipHistory::class);
    }

    public function tenancyHistory(): HasMany
    {
        return $this->hasMany(TenancyHistory::class);
    }

    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class);
    }

    public function residents(): HasMany
    {
        return $this->hasMany(LotResident::class);
    }

    public function vehicles(): HasMany
    {
        return $this->hasMany(LotVehicle::class);
    }

    public function guestAuthorizations(): HasMany
    {
        return $this->hasMany(GuestAuthorization::class);
    }

    public function reservations(): HasMany
    {
        return $this->hasMany(Reservation::class);
    }
}