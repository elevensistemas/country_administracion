<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TenancyHistory extends Model
{
    protected $table = 'tenancy_history';

    protected $fillable = ['lot_id', 'tenant_id', 'start_date', 'end_date', 'owner_id', 'documents', 'notes'];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function lot(): BelongsTo
    {
        return $this->belongsTo(Lot::class);
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(Owner::class);
    }
}