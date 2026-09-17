<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LoginLog extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'ip_address',
        'user_agent',
        'status',
        'failed_attempts',
        'created_at',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get human-readable status name.
     */
    public function getHumanStatusAttribute(): string
    {
        return match ($this->status) {
            'success' => 'Exitoso',
            'failed'  => 'Fallido (Credenciales)',
            'blocked' => 'Bloqueado (Intentos)',
            default   => ucfirst($this->status),
        };
    }

    /**
     * Parse and get a simplified browser & OS string from user agent.
     */
    public function getSimplifiedAgentAttribute(): string
    {
        $agent = $this->user_agent ?? '';
        if (empty($agent)) {
            return 'Desconocido';
        }

        $platform = 'Desconocido';
        if (str_contains($agent, 'Windows')) {
            $platform = 'Windows';
        } elseif (str_contains($agent, 'Macintosh') || str_contains($agent, 'Mac OS')) {
            $platform = 'macOS';
        } elseif (str_contains($agent, 'iPhone') || str_contains($agent, 'iPad')) {
            $platform = 'iOS';
        } elseif (str_contains($agent, 'Android')) {
            $platform = 'Android';
        } elseif (str_contains($agent, 'Linux')) {
            $platform = 'Linux';
        }

        $browser = 'Navegador';
        if (str_contains($agent, 'Edg')) {
            $browser = 'Edge';
        } elseif (str_contains($agent, 'Chrome') && !str_contains($agent, 'Edg')) {
            $browser = 'Chrome';
        } elseif (str_contains($agent, 'Safari') && !str_contains($agent, 'Chrome')) {
            $browser = 'Safari';
        } elseif (str_contains($agent, 'Firefox')) {
            $browser = 'Firefox';
        } elseif (str_contains($agent, 'Opera') || str_contains($agent, 'OPR')) {
            $browser = 'Opera';
        }

        return "{$browser} ({$platform})";
    }
}