<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AuditLog extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'action',
        'model_type',
        'model_id',
        'old_values',
        'new_values',
        'ip_address',
        'created_at',
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
        'created_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Map model classes to human readable names.
     */
    public static function getModelNamesMap(): array
    {
        return [
            'App\Models\User' => 'Usuario',
            'App\Models\Owner' => 'Propietario',
            'App\Models\Lot' => 'Lote',
            'App\Models\FunctionalUnit' => 'Unidad Funcional',
            'App\Models\Payment' => 'Pago',
            'App\Models\PaymentAllocation' => 'Imputación de Pago',
            'App\Models\Expense' => 'Expensa / Liquidación',
            'App\Models\ExpenseItem' => 'Item de Gasto',
            'App\Models\Supplier' => 'Proveedor',
            'App\Models\SupplierInvoice' => 'Factura Proveedor',
            'App\Models\Ticket' => 'Reclamo / Ticket',
            'App\Models\Reservation' => 'Reserva',
            'App\Models\CommonArea' => 'Espacio Común',
            'App\Models\Document' => 'Documento',
            'App\Models\DocumentCategory' => 'Categoría de Documento',
            'App\Models\News' => 'Noticia',
            'App\Models\Communication' => 'Comunicado',
            'App\Models\SystemSetting' => 'Configuración de Sistema',
            'App\Models\SecurityDevice' => 'Dispositivo de Seguridad',
        ];
    }

    /**
     * Get a human-readable name for the audited model.
     */
    public function getHumanModelNameAttribute(): string
    {
        $map = self::getModelNamesMap();
        return $map[$this->model_type] ?? class_basename($this->model_type);
    }

    /**
     * Get human-readable action name.
     */
    public function getHumanActionNameAttribute(): string
    {
        return match ($this->action) {
            'create' => 'Creación',
            'update' => 'Modificación',
            'delete' => 'Eliminación',
            'restore' => 'Restauración',
            default => ucfirst($this->action),
        };
    }
}