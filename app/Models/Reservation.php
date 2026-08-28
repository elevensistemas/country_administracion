<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Reservation extends Model
{
    protected $fillable = [
        'common_area_id', 'lot_id', 'user_id', 'reservation_date', 
        'start_time', 'end_time', 'price', 'charge_to_expenses', 
        'status', 'notes', 'is_exclusive'
    ];

    protected $casts = [
        'reservation_date' => 'date',
        'charge_to_expenses' => 'boolean',
        'is_exclusive' => 'boolean',
        'price' => 'decimal:2',
    ];

    public function commonArea(): BelongsTo
    {
        return $this->belongsTo(CommonArea::class);
    }

    public function lot(): BelongsTo
    {
        return $this->belongsTo(Lot::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Account movements linked to this reservation (for charges to expensas)
     */
    public function accountMovements(): MorphMany
    {
        return $this->morphMany(AccountMovement::class, 'related');
    }
}
