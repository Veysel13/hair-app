<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StaffWorkingHour extends Model
{
    use HasFactory;

    protected $fillable = [
        'staff_id',
        'day_of_week',
        'is_working',
        'start_time',
        'end_time',
    ];

    protected function casts(): array
    {
        return [
            'is_working' => 'boolean',
        ];
    }

    // ----------------------------------------------------------------
    // Relationships
    // ----------------------------------------------------------------

    public function staff(): BelongsTo
    {
        return $this->belongsTo(Staff::class);
    }

    // ----------------------------------------------------------------
    // Helpers
    // ----------------------------------------------------------------

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
