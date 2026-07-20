<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CustomerProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'business_id',
        'customer_id',
        'allergies',
        'skin_type',
        'hair_type',
        'hair_color',
        'notes',
        'last_visit_at',
        'total_visits',
        'total_spent',
        'is_vip',
    ];

    protected function casts(): array
    {
        return [
            'last_visit_at' => 'datetime',
            'total_spent'   => 'float',
            'is_vip'        => 'boolean',
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

    /** Müşterinin işlem reçeteleri (boya formülleri vb.) */
    public function treatmentRecords(): HasMany
    {
        return $this->hasMany(TreatmentRecord::class)->latest();
    }

    // ----------------------------------------------------------------
    // Scopes
    // ----------------------------------------------------------------

    public function scopeVip($query)
    {
        return $query->where('is_vip', true);
    }

    /**
     * Belirtilen gün sayısından daha uzun süredir gelmeyen müşteriler.
     * Pazarlama otomasyonu (win-back kampanyaları) için kullanılır.
     */
    public function scopeInactive($query, int $days = 90)
    {
        return $query->where('last_visit_at', '<', now()->subDays($days))
            ->orWhereNull('last_visit_at');
    }

    // ----------------------------------------------------------------
    // Helpers
    // ----------------------------------------------------------------

    public function getFormattedTotalSpentAttribute(): string
    {
        return number_format($this->total_spent, 2, ',', '.') . ' ₺';
    }

    public function getDaysSinceLastVisitAttribute(): ?int
    {
        return $this->last_visit_at
            ? (int) $this->last_visit_at->diffInDays(now())
            : null;
    }
}
