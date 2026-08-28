<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class LotHistoryEvent extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'lot_id', 'functional_unit_id', 'event_type_id', 'category_id', 
        'related_model_type', 'related_model_id', 'owner_id', 
        'tenant_id', 'user_id', 'title', 'description', 
        'event_date', 'status', 'priority', 'source_channel', 
        'visibility', 'is_confidential', 'metadata'
    ];

    protected $casts = [
        'event_date' => 'datetime',
        'metadata' => 'json',
        'is_confidential' => 'boolean',
    ];

    public function lot(): BelongsTo
    {
        return $this->belongsTo(Lot::class);
    }

    public function functionalUnit(): BelongsTo
    {
        return $this->belongsTo(FunctionalUnit::class);
    }

    public function eventType(): BelongsTo
    {
        return $this->belongsTo(LotHistoryEventType::class, 'event_type_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(LotHistoryCategory::class, 'category_id');
    }

    public function related(): MorphTo
    {
        return $this->morphTo();
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(Owner::class);
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(LotHistoryAttachment::class);
    }

    public function followUps(): HasMany
    {
        return $this->hasMany(LotFollowUp::class);
    }
}