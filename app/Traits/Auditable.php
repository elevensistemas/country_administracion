<?php

namespace App\Traits;

use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;

trait Auditable
{
    /**
     * Boot the Auditable trait for a model.
     */
    public static function bootAuditable(): void
    {
        static::created(function ($model) {
            $model->recordAudit('create', null, $model->filterAuditValues($model->getAttributes()));
        });

        static::updated(function ($model) {
            $dirty = $model->getDirty();
            $oldValues = [];
            $newValues = [];

            foreach ($dirty as $key => $newValue) {
                if ($model->isAuditExcluded($key)) {
                    continue;
                }

                $oldValues[$key] = $model->getOriginal($key);
                $newValues[$key] = $newValue;
            }

            if (!empty($newValues)) {
                $model->recordAudit('update', $oldValues, $newValues);
            }
        });

        static::deleted(function ($model) {
            $model->recordAudit('delete', $model->filterAuditValues($model->getOriginal()), null);
        });

        if (method_exists(static::class, 'restored')) {
            static::restored(function ($model) {
                $model->recordAudit('restore', null, $model->filterAuditValues($model->getAttributes()));
            });
        }
    }

    /**
     * Record an audit log entry.
     */
    protected function recordAudit(string $action, ?array $oldValues, ?array $newValues): void
    {
        try {
            AuditLog::create([
                'user_id'    => Auth::id(),
                'action'     => $action,
                'model_type' => get_class($this),
                'model_id'   => $this->getKey(),
                'old_values' => $oldValues ? json_encode($oldValues, JSON_UNESCAPED_UNICODE) : null,
                'new_values' => $newValues ? json_encode($newValues, JSON_UNESCAPED_UNICODE) : null,
                'ip_address' => request() ? request()->ip() : null,
                'created_at' => now(),
            ]);
        } catch (\Throwable $e) {
            // Silently report to prevent breaking the main transaction/operation
            report($e);
        }
    }

    /**
     * Attributes that should be excluded from audit logs.
     */
    public function getAuditExcludedAttributes(): array
    {
        $defaultExcluded = ['password', 'remember_token', 'updated_at', 'created_at', 'deleted_at', 'two_factor_secret', 'two_factor_recovery_codes'];
        
        if (property_exists($this, 'auditExcluded') && is_array($this->auditExcluded)) {
            return array_merge($defaultExcluded, $this->auditExcluded);
        }

        return $defaultExcluded;
    }

    /**
     * Determine if an attribute is excluded from audit.
     */
    public function isAuditExcluded(string $key): bool
    {
        return in_array($key, $this->getAuditExcludedAttributes(), true);
    }

    /**
     * Filter out excluded attributes from an array of values.
     */
    public function filterAuditValues(array $values): array
    {
        $excluded = $this->getAuditExcludedAttributes();
        return array_diff_key($values, array_flip($excluded));
    }
}
