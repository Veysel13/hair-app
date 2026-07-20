<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Staff extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'staff';

    protected $fillable = [
        'business_id',
        'user_id',
        'name',
        'title',
        'bio',
        'avatar',
        'is_active',
        'display_order',
    ];

    protected function casts(): array
    {
        return [
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

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** Personelin çalışma saatleri (gün bazlı) */
    public function workingHours(): HasMany
    {
        return $this->hasMany(StaffWorkingHour::class)->orderBy('day_of_week');
    }

    /** Portfolyo fotoğrafları */
    public function portfolio(): HasMany
    {
        return $this->hasMany(StaffPortfolio::class)->orderBy('sort_order');
    }

    /** Personelin verebileceği hizmetler (pivot: service_staff) */
    public function services(): BelongsToMany
    {
        return $this->belongsToMany(Service::class, 'service_staff')
            ->withPivot(['custom_duration', 'custom_price'])
            ->withTimestamps();
    }

    /** Randevular */
    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class);
    }

    /** Yorumlar */
    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    /** Yapılan işlem kayıtları */
    public function treatmentRecords(): HasMany
    {
        return $this->hasMany(TreatmentRecord::class);
    }

    // ----------------------------------------------------------------
    // Helpers
    // ----------------------------------------------------------------

    /**
     * Verilen tarih ve saatte müsait mi?
     * (Basit kontrol — tam çakışma kontrolü AppointmentService'de yapılır.)
     */
    public function isWorkingOn(int $dayOfWeek): bool
    {
        $schedule = $this->workingHours->firstWhere('day_of_week', $dayOfWeek);
        return $schedule && $schedule->is_working;
    }
}
