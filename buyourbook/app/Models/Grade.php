<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Grade extends Model
{
    use HasFactory;

    protected $fillable = [
        'school_id',
        'name',
        'level',
        'academic_year',
    ];

    // --- Relations ---

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    public function officialBooks(): HasMany
    {
        return $this->hasMany(OfficialBook::class);
    }

    // --- Scopes ---

    public function scopeForYear($query, string $year)
    {
        return $query->where('academic_year', $year);
    }

    public function scopeForLevel($query, string $level)
    {
        return $query->where('level', $level);
    }
}
