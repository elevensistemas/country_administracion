<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class SecurityDevice extends Model
{
    protected $fillable = [
        'name', 'device_identifier', 'api_token_hash', 
        'last_seen_at', 'last_sync_cursor', 'is_active', 
        'revoked_at', 'notes'
    ];

    protected $casts = [
        'last_seen_at' => 'datetime',
        'revoked_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    public function syncLogs(): HasMany
    {
        return $this->hasMany(SecuritySyncLog::class);
    }

    /**
     * Create a new device and return the plain token for one-time display.
     */
    public static function createWithToken(string $name, string $identifier, ?string $notes = null): array
    {
        $plainToken = 'sec_' . Str::random(40);
        $device = static::create([
            'name' => $name,
            'device_identifier' => $identifier,
            'api_token_hash' => hash('sha256', $plainToken),
            'notes' => $notes,
            'is_active' => true,
        ]);

        return [
            'device' => $device,
            'plain_token' => $plainToken,
        ];
    }

    public function isValidToken(string $token): bool
    {
        if (!$this->is_active || $this->revoked_at !== null) {
            return false;
        }

        return hash_equals($this->api_token_hash, hash('sha256', $token));
    }
}