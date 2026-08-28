<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class FunctionalUnit extends Model
{
    use SoftDeletes;

    protected $fillable = ['lot_id', 'code', 'name', 'description', 'balance'];

    public function lot(): BelongsTo
    {
        return $this->belongsTo(Lot::class);
    }

    public function owners(): BelongsToMany
    {
        return $this->belongsToMany(Owner::class, 'owner_functional_unit')
                    ->withPivot('share_percentage');
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_functional_unit')
                    ->withPivot('relationship_type');
    }

    public function expenses(): HasMany
    {
        return $this->hasMany(Expense::class);
    }

    public function accountMovements(): HasMany
    {
        return $this->hasMany(AccountMovement::class);
    }
}