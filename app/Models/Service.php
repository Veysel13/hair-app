<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Service extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'business_id',
        'category_id',
        'name',
        'description',
        'duration_minutes',
        'price',
        'is_active',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'price'     => 'float',
            'is_active' => 'boolean',
        ];
    }

    // ----------------------------------------------------------------
    // Relationships
    // ----------------------------------------------------------------

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(ServiceCategory::class, 'category_id');
    }

    /**
     * Bu hizmeti verebilen personeller.
     * Pivot üzerinden personele özel süre/fiyat override'larına erişilebilir.
     */
    public function staff(): BelongsToMany
    {
        return $this->belongsToMany(Staff::class, 'service_staff')
            ->withPivot(['custom_duration', 'custom_price'])
            ->withTimestamps();
    }

    /** Randevu kalemleri */
    public function appointmentServices(): HasMany
    {
        return $this->hasMany(AppointmentService::class);
    }

    /** İşlem kayıtları */
    public function treatmentRecords(): HasMany
    {
        return $this->hasMany(TreatmentRecord::class);
    }

    // ----------------------------------------------------------------
    // Helpers
    // ----------------------------------------------------------------

    /**
     * Belirli bir personel için efektif süreyi döner.
     * Personele özel override yoksa standart süre kullanılır.
     */
    public function effectiveDurationFor(Staff $staff): int
    {
        $pivot = $this->staff->firstWhere('id', $staff->id)?->pivot;
        return $pivot?->custom_duration ?? $this->duration_minutes;
    }

    /**
     * Belirli bir personel için efektif fiyatı döner.
     */
    public function effectivePriceFor(Staff $staff): float
    {
        $pivot = $this->staff->firstWhere('id', $staff->id)?->pivot;
        return $pivot?->custom_price ?? $this->price;
    }

    /** Formatlanmış fiyat (örn: 350,00 ₺) */
    public function getFormattedPriceAttribute(): string
    {
        return number_format($this->price, 2, ',', '.') . ' ₺';
    }

    /** Süreyi saat:dakika formatında döner */
    public function getFormattedDurationAttribute(): string
    {
        $hours   = intdiv($this->duration_minutes, 60);
        $minutes = $this->duration_minutes % 60;

        if ($hours > 0 && $minutes > 0) {
            return "{$hours} sa {$minutes} dk";
        } elseif ($hours > 0) {
            return "{$hours} sa";
        }
        return "{$minutes} dk";
    }
}
