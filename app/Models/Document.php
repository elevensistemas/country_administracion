<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Document extends Model
{
    protected $fillable = ['category_id', 'name', 'description', 'visibility', 'is_archived'];

    public function category(): BelongsTo
    {
        return $this->belongsTo(DocumentCategory::class);
    }

    public function versions(): HasMany
    {
        return $this->hasMany(DocumentVersion::class);
    }

    public function latestVersion()
    {
        return $this->hasOne(DocumentVersion::class)->latestOfMany();
    }
}