<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Riwayat cetak checker per station (BR-014 — sekali cetak).
 * UNIQUE(order_id, station) di DB menjamin maksimal satu baris per station per order.
 */
class CheckerPrint extends Model
{
    protected $fillable = [
        'order_id',
        'station',   // kitchen | barista | (station masa depan)
        'printed_at',
    ];

    protected function casts(): array
    {
        return [
            'printed_at' => 'datetime',
        ];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}
