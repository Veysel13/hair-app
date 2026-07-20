<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BusinessHour extends Model
{
    use HasFactory;

    protected $fillable = [
        'business_id',
        'day_of_week',
        'is_open',
        'open_time',
        'close_time',
    ];

    protected function casts(): array
    {
        return [
            'is_open' => 'boolean',
        ];
    }

    // ----------------------------------------------------------------
    // Relationships
    // ----------------------------------------------------------------

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    // ----------------------------------------------------------------
    // Helpers
    // ----------------------------------------------------------------

    /** Türkçe gün adı */
    public function getDayNameAttribute(): string
    {
        return match ($this->day_of_week) {
            0 => 'Pazar',
            1 => 'Pazartesi',
            2 => 'Salı',
            3 => 'Çarşamba',
            4 => 'Perşembe',
            5 => 'Cuma',
            6 => 'Cumartesi',
            default => '?',
        };
    }
}
