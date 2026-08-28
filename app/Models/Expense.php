<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Expense extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'billing_period_id', 'functional_unit_id', 'issue_date', 
        'due_date', 'second_due_date', 'previous_balance', 
        'capital_amount', 'interest_amount', 'adjustments_amount', 
        'discount_amount', 'total_amount', 'status', 'attachment_path'
    ];

    protected $casts = [
        'issue_date' => 'date',
        'due_date' => 'date',
        'second_due_date' => 'date',
    ];

    public function billingPeriod(): BelongsTo
    {
        return $this->belongsTo(BillingPeriod::class);
    }

    public function functionalUnit(): BelongsTo
    {
        return $this->belongsTo(FunctionalUnit::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(ExpenseItem::class);
    }
}