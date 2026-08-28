<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaymentReceipt extends Model
{
    protected $fillable = ['payment_id', 'file_path', 'file_name', 'file_size'];

    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payment::class);
    }
}