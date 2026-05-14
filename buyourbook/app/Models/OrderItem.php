<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'seller_book_id',
        'quantity',
        'unit_price',
        'seller_ready',
        'seller_ready_at',
        'seller_paid',
        'seller_paid_at',
    ];

    protected function casts(): array
    {
        return [
            'quantity'        => 'integer',
            'unit_price'      => 'integer',
            'seller_ready'    => 'boolean',
            'seller_ready_at' => 'datetime',
            'seller_paid'     => 'boolean',
            'seller_paid_at'  => 'datetime',
        ];
    }

    // --- Relations ---

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function sellerBook(): BelongsTo
    {
        return $this->belongsTo(SellerBook::class);
    }

    /**
     * Sous-total de cet item.
     */
    public function getSubtotalAttribute(): int
    {
        return $this->quantity * $this->unit_price;
    }
}
