<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'last_name',
        'email',
        'phone',
        'dni',
        'status',
        'relationship_type',
        'password',
        'hire_date',
        'resignation_date',
        'notes',
        'first_login_at',
        'last_login_at',
        'login_count',
        'last_login_ip',
        'last_login_agent',
        'terms_accepted_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'hire_date' => 'datetime',
            'resignation_date' => 'datetime',
            'first_login_at' => 'datetime',
            'last_login_at' => 'datetime',
            'terms_accepted_at' => 'datetime',
        ];
    }

    /**
     * Relations
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class);
    }

    public function preferences(): HasOne
    {
        return $this->hasOne(UserPreference::class);
    }

    public function functionalUnits(): BelongsToMany
    {
        return $this->belongsToMany(FunctionalUnit::class, 'user_functional_unit')
                    ->withPivot('relationship_type');
    }

    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class);
    }

    public function messages(): HasMany
    {
        return $this->hasMany(TicketMessage::class);
    }

    public function loginLogs(): HasMany
    {
        return $this->hasMany(LoginLog::class);
    }

    public function activityLogs(): HasMany
    {
        return $this->hasMany(UserActivityLog::class);
    }

    public function reservations(): HasMany
    {
        return $this->hasMany(Reservation::class);
    }

    public function guestAuthorizations(): HasMany
    {
        return $this->hasMany(GuestAuthorization::class);
    }

    /**
     * Helpers for Roles and Permissions
     */
    public function hasRole(string|array $roleName): bool
    {
        if (is_array($roleName)) {
            return $this->roles()->whereIn('name', $roleName)->exists();
        }
        return $this->roles()->where('name', $roleName)->exists();
    }

    public function hasPermission(string $permissionName): bool
    {
        // If superadmin, always allow
        if ($this->isSuperAdmin()) {
            return true;
        }

        // Check if any role has this permission
        return $this->roles()->whereHas('permissions', function ($query) use ($permissionName) {
            $query->where('name', $permissionName);
        })->exists();
    }

    public function isSuperAdmin(): bool
    {
        return $this->relationship_type === 'superadmin' || $this->hasRole('superadmin');
    }

    public function isAdmin(): bool
    {
        return in_array($this->relationship_type, ['admin', 'superadmin']) || $this->hasRole(['admin', 'superadmin']);
    }

    public function isAccounting(): bool
    {
        return $this->relationship_type === 'accounting' || $this->hasRole('accounting');
    }

    public function isOwner(): bool
    {
        return $this->relationship_type === 'owner' || $this->hasRole('owner');
    }

    public function isTenant(): bool
    {
        return $this->relationship_type === 'tenant' || $this->hasRole('tenant');
    }

    public function getFullNameAttribute(): string
    {
        return trim($this->name . ' ' . $this->last_name);
    }
}
