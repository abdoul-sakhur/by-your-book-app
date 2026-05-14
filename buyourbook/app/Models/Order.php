<?php

namespace App\Models;

use App\Enums\OrderStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'relay_point_id',
        'status',
        'total_amount',
        'delivery_fee',
        'payment_method',
        'payment_received_at',
        'delivery_address',
        'delivery_phone',
        'delivery_notes',
    ];

    protected function casts(): array
    {
        return [
            'status'               => OrderStatus::class,
            'total_amount'         => 'integer',
            'delivery_fee'         => 'integer',
            'payment_received_at'  => 'datetime',
        ];
    }

    // --- Helpers workflow ---

    public function isPaid(): bool
    {
        return $this->payment_received_at !== null;
    }

    public function canBeCancelledByBuyer(): bool
    {
        return $this->status === OrderStatus::Pending;
    }

    /**
     * Tous les items de la commande sont-ils marqués prêts par leur vendeur ?
     */
    public function allItemsReadyBySeller(): bool
    {
        return $this->items->every(fn ($item) => $item->seller_ready);
    }

    // --- Relations ---

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function relayPoint(): BelongsTo
    {
        return $this->belongsTo(RelayPoint::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function events(): HasMany
    {
        return $this->hasMany(OrderEvent::class);
    }

    // --- Scopes ---

    public function scopeForUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeByStatus($query, OrderStatus $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Recalcule le total à partir des items.
     */
    public function recalculateTotal(): void
    {
        $this->update([
            'total_amount' => $this->items->sum(fn ($item) => $item->quantity * $item->unit_price),
        ]);
    }
}
