<?php

namespace App\Models;

use App\Enums\BannerPosition;
use App\Enums\BannerTarget;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Banner extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'image',
        'link_url',
        'position',
        'target_type',
        'school_id',
        'is_active',
        'starts_at',
        'ends_at',
    ];

    protected function casts(): array
    {
        return [
            'position' => BannerPosition::class,
            'target_type' => BannerTarget::class,
            'is_active' => 'boolean',
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
        ];
    }

    // --- Relations ---

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    // --- Scopes ---

    /**
     * Bannières actuellement visibles (actives + dans la période).
     */
    public function scopeVisible($query)
    {
        return $query->where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('starts_at')->orWhere('starts_at', '<=', now());
            })
            ->where(function ($q) {
                $q->whereNull('ends_at')->orWhere('ends_at', '>=', now());
            });
    }

    public function scopeAtPosition($query, BannerPosition $position)
    {
        return $query->where('position', $position);
    }

    public function scopeForAllOrSchool($query, ?int $schoolId = null)
    {
        return $query->where(function ($q) use ($schoolId) {
            $q->where('target_type', BannerTarget::All);
            if ($schoolId) {
                $q->orWhere(function ($sub) use ($schoolId) {
                    $sub->where('target_type', BannerTarget::School)
                        ->where('school_id', $schoolId);
                });
            }
        });
    }
}
