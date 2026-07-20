<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Review extends Model
{
    use HasFactory;

    protected $fillable = [
        'appointment_id',
        'business_id',
        'customer_id',
        'staff_id',
        'rating',
        'staff_rating',
        'comment',
        'business_reply',
        'business_replied_at',
        'is_visible',
    ];

    protected function casts(): array
    {
        return [
            'is_visible'          => 'boolean',
            'business_replied_at' => 'datetime',
        ];
    }

    // ----------------------------------------------------------------
    // Relationships
    // ----------------------------------------------------------------

    public function appointment(): BelongsTo
    {
        return $this->belongsTo(Appointment::class);
    }

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function staff(): BelongsTo
    {
        return $this->belongsTo(Staff::class);
    }

    // ----------------------------------------------------------------
    // Scopes
    // ----------------------------------------------------------------

    public function scopeVisible($query)
    {
        return $query->where('is_visible', true);
    }

    public function scopeHighRated($query, int $min = 4)
    {
        return $query->where('rating', '>=', $min);
    }

    // ----------------------------------------------------------------
    // Helpers
    // ----------------------------------------------------------------

    public function hasReply(): bool
    {
        return ! empty($this->business_reply);
    }

    /** Yıldız gösterimi (örn: ★★★★☆) */
    public function getStarsAttribute(): string
    {
        return str_repeat('★', $this->rating) . str_repeat('☆', 5 - $this->rating);
    }
}
