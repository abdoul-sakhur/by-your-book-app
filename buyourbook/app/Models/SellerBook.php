<?php

namespace App\Models;

use App\Enums\BookCondition;
use App\Enums\BookStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SellerBook extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'official_book_id',
        'condition',
        'price',
        'quantity',
        'images',
        'status',
        'rejection_reason',
        'admin_notes',
        'purchase_price',
        'buyback_price',
        'counter_price',
        'buyback_status',
        'buyback_notes',
        'admin_paid_seller',
    ];

    protected function casts(): array
    {
        return [
            'condition'       => BookCondition::class,
            'status'          => BookStatus::class,
            'images'          => 'array',
            'price'           => 'integer',
            'quantity'        => 'integer',
            'purchase_price'  => 'integer',
            'buyback_price'   => 'integer',
            'counter_price'   => 'integer',
            'admin_paid_seller' => 'boolean',
        ];
    }

    // --- Relations ---

    public function seller(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function officialBook(): BelongsTo
    {
        return $this->belongsTo(OfficialBook::class);
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    // --- Scopes ---

    public function scopeApproved($query)
    {
        return $query->where('status', BookStatus::Approved);
    }

    public function scopePending($query)
    {
        return $query->where('status', BookStatus::Pending);
    }

    public function scopeAvailable($query)
    {
        return $query->approved()->where('quantity', '>', 0);
    }

    public function scopeForSeller($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }
}
