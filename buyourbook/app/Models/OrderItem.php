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
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'integer',
            'unit_price' => 'integer',
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
