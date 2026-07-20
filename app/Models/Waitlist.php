<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Waitlist extends Model
{
    use HasFactory;

    protected $table = 'waitlist';

    protected $fillable = [
        'business_id',
        'customer_id',
        'staff_id',
        'service_ids',
        'preferred_date',
        'preferred_time_start',
        'preferred_time_end',
        'status',
        'notified_at',
        'expires_at',
    ];

    protected function casts(): array
    {
        return [
            'service_ids'   => 'array',
            'preferred_date' => 'date',
            'notified_at'   => 'datetime',
            'expires_at'    => 'datetime',
        ];
    }

    // ----------------------------------------------------------------
    // Relationships
    // ----------------------------------------------------------------

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

    public function scopeWaiting($query)
    {
        return $query->where('status', 'waiting');
    }

    public function scopeNotExpired($query)
    {
        return $query->where(function ($q) {
            $q->whereNull('expires_at')->orWhere('expires_at', '>', now());
        });
    }

    // ----------------------------------------------------------------
    // Helpers
    // ----------------------------------------------------------------

    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }
}
