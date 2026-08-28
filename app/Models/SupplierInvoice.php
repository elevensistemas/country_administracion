<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class SupplierInvoice extends Model
{
    use SoftDeletes;

    protected $table = 'supplier_invoices';

    protected $fillable = [
        'supplier_id', 'invoice_number', 'concept', 'amount', 
        'issue_date', 'due_date', 'file_path', 'status', 'notes'
    ];

    protected $casts = [
        'issue_date' => 'date',
        'due_date' => 'date',
        'amount' => 'decimal:2',
    ];

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class, 'supplier_id');
    }
}
