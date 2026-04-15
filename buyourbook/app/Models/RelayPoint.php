<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RelayPoint extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'address',
        'city',
        'district',
        'contact_phone',
        'schedule',
        'is_active',
        'coordinates',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'coordinates' => 'array',
        ];
    }

    // --- Relations ---

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    // --- Scopes ---

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeInCity($query, string $city)
    {
        return $query->where('city', $city);
    }
}
