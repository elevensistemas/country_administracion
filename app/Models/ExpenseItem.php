<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExpenseItem extends Model
{
    protected $fillable = ['expense_id', 'concept', 'amount', 'category'];

    public function expense(): BelongsTo
    {
        return $this->belongsTo(Expense::class);
    }
}