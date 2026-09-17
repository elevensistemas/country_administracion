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
}