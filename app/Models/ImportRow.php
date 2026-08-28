<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ImportRow extends Model
{
    protected $fillable = ['import_id', 'row_number', 'data', 'errors', 'status'];

    protected $casts = [
        'data' => 'json',
        'errors' => 'json',
    ];

    public function import(): BelongsTo
    {
        return $this->belongsTo(Import::class);
    }
}