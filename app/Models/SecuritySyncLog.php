<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SecuritySyncLog extends Model
{
    protected $fillable = [
        'security_device_id', 'since_cursor', 'records_count', 
        'ip_address', 'user_agent', 'status', 'error_message'
    ];

    public function device(): BelongsTo
    {
        return $this->belongsTo(SecurityDevice::class, 'security_device_id');
    }
}