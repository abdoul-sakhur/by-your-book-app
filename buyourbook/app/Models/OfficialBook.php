<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OfficialBook extends Model
{
    use HasFactory;

    protected $fillable = [
        'grade_id',
        'subject_id',
        'title',
        'author',
        'isbn',
        'publisher',
        'cover_image',
        'description',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    // --- Relations ---

    public function grade(): BelongsTo
    {
        return $this->belongsTo(Grade::class);
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    public function sellerBooks(): HasMany
    {
        return $this->hasMany(SellerBook::class);
    }

    // --- Scopes ---

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeForGrade($query, int $gradeId)
    {
        return $query->where('grade_id', $gradeId);
    }

    /**
     * Retourne le prix minimum parmi les livres vendeurs approuvés.
     */
    public function getMinPriceAttribute(): ?int
    {
        return $this->sellerBooks()
            ->where('status', 'approved')
            ->where('quantity', '>', 0)
            ->min('price');
    }
}
