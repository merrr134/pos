<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Order extends Model
{
    protected $fillable = [
        'order_number',
        'table_id',
        'customer_name',
        'total',
        'status',      // active | paid
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'total' => 'decimal:2',
        ];
    }

    public function table(): BelongsTo
    {
        return $this->belongsTo(Table::class);
    }

    /** Waiters pembuat order. */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /** 1 order : 1 pembayaran (BR-011). */
    public function payment(): HasOne
    {
        return $this->hasOne(Payment::class);
    }

    /** Riwayat cetak checker per station — maks. 1 per station (BR-014, additive Modul 6). */
    public function checkerPrints(): HasMany
    {
        return $this->hasMany(CheckerPrint::class);
    }
}
