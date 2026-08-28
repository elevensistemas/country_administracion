<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Supplier extends Model
{
    use SoftDeletes;

    protected $table = 'suppliers';

    protected $fillable = [
        'cuit', 'business_name', 'category', 'email', 'phone', 
        'address', 'bank_name', 'cbu_alias', 'status', 'notes'
    ];

    public function invoices(): HasMany
    {
        return $this->hasMany(SupplierInvoice::class, 'supplier_id');
    }
}
