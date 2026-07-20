<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Campaign extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'business_id',
        'name',
        'description',
        'type',
        'discount_type',
        'discount_value',
        'min_booking_amount',
        'applicable_service_ids',
        'applicable_days',
        'applicable_time_start',
        'applicable_time_end',
        'starts_at',
        'ends_at',
        'max_uses',
        'max_uses_per_customer',
        'used_count',
        'is_active',
        'code',
    ];

    protected function casts(): array
    {
        return [
            'applicable_service_ids' => 'array',
            'applicable_days'        => 'array',
            'starts_at'              => 'datetime',
            'ends_at'                => 'datetime',
            'is_active'              => 'boolean',
            'discount_value'         => 'float',
            'min_booking_amount'     => 'float',
        ];
    }

    // ----------------------------------------------------------------
    // Relationships
    // ----------------------------------------------------------------

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    public function usages(): HasMany
    {
        return $this->hasMany(CampaignUsage::class);
    }

    // ----------------------------------------------------------------
    // Scopes
    // ----------------------------------------------------------------

    public function scopeActive($query)
    {
        return $query->where('is_active', true)
            ->where('starts_at', '<=', now())
            ->where(fn ($q) => $q->whereNull('ends_at')->orWhere('ends_at', '>=', now()));
    }

    // ----------------------------------------------------------------
    // Helpers
    // ----------------------------------------------------------------

    public function isActive(): bool
    {
        if (! $this->is_active) {
            return false;
        }
        if (now()->lt($this->starts_at)) {
            return false;
        }
        if ($this->ends_at && now()->gt($this->ends_at)) {
            return false;
        }
        if ($this->max_uses && $this->used_count >= $this->max_uses) {
            return false;
        }
        return true;
    }

    /**
     * Belirli bir tutar için indirim miktarını hesaplar.
     */
    public function calculateDiscount(float $amount): float
    {
        if (! $this->discount_value) {
            return 0;
        }
        if ($this->discount_type === 'percentage') {
            return round($amount * $this->discount_value / 100, 2);
        }
        return min($this->discount_value, $amount);
    }

    /**
     * Kampanya belirli bir gün ve saatte geçerli mi?
     */
    public function isApplicableAt(\DateTimeInterface $dateTime): bool
    {
        if (! $this->isActive()) {
            return false;
        }

        // Gün kısıtlaması
        if ($this->applicable_days && ! in_array((int) date('w', $dateTime->getTimestamp()), $this->applicable_days)) {
            return false;
        }

        // Saat kısıtlaması
        $time = date('H:i:s', $dateTime->getTimestamp());
        if ($this->applicable_time_start && $time < $this->applicable_time_start) {
            return false;
        }
        if ($this->applicable_time_end && $time > $this->applicable_time_end) {
            return false;
        }

        return true;
    }

    public function getTypeLabelAttribute(): string
    {
        return match ($this->type) {
            'discount'     => 'İndirim',
            'free_service' => 'Ücretsiz Hizmet',
            'package'      => 'Paket',
            'birthday'     => 'Doğum Günü',
            'win_back'     => 'Geri Kazanım',
            'flash'        => 'Anlık Kampanya',
            default        => $this->type,
        };
    }
}
