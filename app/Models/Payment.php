<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    protected $fillable = [
        'order_id',
        'amount_paid',
        'change',
        'tax_percent',    // snapshot % pajak saat bayar (BR-016, additive Modul 8)
        'tax_amount',     // snapshot nominal pajak saat bayar (BR-016)
        'payment_method', // cash (default) | qris (siap diperluas)
        'received_by',
        'paid_at',        // penanda transaksi selesai (BR-005)
    ];

    protected function casts(): array
    {
        return [
            'amount_paid' => 'decimal:2',
            'change'      => 'decimal:2',
            'tax_percent' => 'decimal:2',
            'tax_amount'  => 'decimal:2',
            'paid_at'     => 'datetime',
        ];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /** Kasir penerima pembayaran. */
    public function receiver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'received_by');
    }
}
