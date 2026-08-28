<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BillingPeriod extends Model
{
    protected $fillable = ['period', 'start_date', 'end_date', 'status'];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function expenses(): HasMany
    {
        return $this->hasMany(Expense::class);
    }
}