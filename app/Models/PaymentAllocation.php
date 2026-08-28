<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaymentAllocation extends Model
{
    protected $fillable = [
        'payment_id', 'account_movement_id', 'allocated_amount', 'user_id', 
        'method', 'previous_balance', 'posterior_balance', 'notes', 'status', 
        'reverted_at', 'reverted_by', 'reversion_reason'
    ];

    protected $casts = [
        'reverted_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function revertedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reverted_by');
    }

    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payment::class);
    }

    public function accountMovement(): BelongsTo
    {
        return $this->belongsTo(AccountMovement::class);
    }
}