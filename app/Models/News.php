<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\Auditable;

class News extends Model
{
    use Auditable;
    protected $table = 'news';

    protected $fillable = [
        'title', 'summary', 'content', 'image_path', 'file_path', 
        'status', 'visibility', 'recipients_type', 'publish_date', 
        'published_at', 'channels', 'is_published'
    ];

    protected $casts = [
        'publish_date' => 'datetime',
        'published_at' => 'datetime',
        'is_published' => 'boolean',
    ];

    /**
     * Relationship: Lots targeted by this news publication.
     */
    public function lots(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Lot::class, 'news_lots');
    }

    /**
     * Check if news applies to all lots.
     */
    public function isTargetedToAll(): bool
    {
        return $this->recipients_type !== 'specific_lots';
    }

    /**
     * Check if news applies to a given lot.
     */
    public function appliesToLot(int|Lot $lot): bool
    {
        if ($this->isTargetedToAll()) {
            return true;
        }

        $lotId = $lot instanceof Lot ? $lot->id : $lot;
        return $this->lots->contains('id', $lotId);
    }
}