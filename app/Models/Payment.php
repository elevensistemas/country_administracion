<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Payment extends Model
{
    protected $fillable = [
        'owner_id', 'lot_id', 'functional_unit_id', 'payment_date', 
        'import_date', 'amount', 'bank', 'payment_method', 
        'operation_number', 'receipt_path', 'notes', 'user_id', 
        'source_channel', 'status', 'matching_score', 'matched_debit_id', 
        'reconciliation_method', 'reconciled_at', 'reverted_at', 'reverted_by', 
        'reversion_reason'
    ];

    protected $casts = [
        'payment_date' => 'date',
        'import_date' => 'date',
        'reconciled_at' => 'datetime',
        'reverted_at' => 'datetime',
    ];

    public function owner(): BelongsTo
    {
        return $this->belongsTo(Owner::class);
    }

    public function lot(): BelongsTo
    {
        return $this->belongsTo(Lot::class);
    }

    public function functionalUnit(): BelongsTo
    {
        return $this->belongsTo(FunctionalUnit::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class); // Approved by
    }

    public function revertedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reverted_by');
    }

    public function receipts(): HasMany
    {
        return $this->hasMany(PaymentReceipt::class);
    }

    public function allocations(): HasMany
    {
        return $this->hasMany(PaymentAllocation::class);
    }
}