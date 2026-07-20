<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Business extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'owner_id',
        'name',
        'slug',
        'description',
        'phone',
        'email',
        'website',
        'address',
        'city',
        'district',
        'latitude',
        'longitude',
        'category',
        'logo',
        'cover_image',
        'is_active',
        'avg_rating',
        'total_reviews',
        'deposit_required',
        'deposit_percentage',
        'cancellation_hours',
    ];

    protected function casts(): array
    {
        return [
            'latitude'          => 'float',
            'longitude'         => 'float',
            'avg_rating'        => 'float',
            'is_active'         => 'boolean',
            'deposit_required'  => 'boolean',
        ];
    }

    // ----------------------------------------------------------------
    // Lifecycle
    // ----------------------------------------------------------------

    protected static function booted(): void
    {
        static::creating(function (Business $business) {
            if (empty($business->slug)) {
                $business->slug = Str::slug($business->name);
            }
        });
    }

    // ----------------------------------------------------------------
    // Relationships
    // ----------------------------------------------------------------

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function hours(): HasMany
    {
        return $this->hasMany(BusinessHour::class)->orderBy('day_of_week');
    }

    public function images(): HasMany
    {
        return $this->hasMany(BusinessImage::class)->orderBy('sort_order');
    }

    public function staff(): HasMany
    {
        return $this->hasMany(Staff::class)->orderBy('display_order');
    }

    public function activeStaff(): HasMany
    {
        return $this->hasMany(Staff::class)->where('is_active', true)->orderBy('display_order');
    }

    public function serviceCategories(): HasMany
    {
        return $this->hasMany(ServiceCategory::class)->orderBy('sort_order');
    }

    public function services(): HasMany
    {
        return $this->hasMany(Service::class)->orderBy('sort_order');
    }

    public function activeServices(): HasMany
    {
        return $this->hasMany(Service::class)->where('is_active', true)->orderBy('sort_order');
    }

    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function visibleReviews(): HasMany
    {
        return $this->hasMany(Review::class)->where('is_visible', true)->latest();
    }

    public function customerProfiles(): HasMany
    {
        return $this->hasMany(CustomerProfile::class);
    }

    public function expenses(): HasMany
    {
        return $this->hasMany(Expense::class);
    }

    public function dailySummaries(): HasMany
    {
        return $this->hasMany(DailySummary::class);
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function campaigns(): HasMany
    {
        return $this->hasMany(Campaign::class);
    }

    public function waitlist(): HasMany
    {
        return $this->hasMany(Waitlist::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function notificationLogs(): HasMany
    {
        return $this->hasMany(NotificationLog::class);
    }

    // ----------------------------------------------------------------
    // Helpers
    // ----------------------------------------------------------------

    /** Haversine formülüyle verilen koordinata mesafeyi km cinsinden döner */
    public function distanceTo(float $lat, float $lng): float
    {
        $earthRadius = 6371;
        $dLat = deg2rad($lat - $this->latitude);
        $dLng = deg2rad($lng - $this->longitude);
        $a = sin($dLat / 2) ** 2
            + cos(deg2rad($this->latitude)) * cos(deg2rad($lat)) * sin($dLng / 2) ** 2;
        return $earthRadius * 2 * asin(sqrt($a));
    }

    public function isOpen(): bool
    {
        $today = now()->dayOfWeek; // 0=Pazar
        $hour  = $this->hours->firstWhere('day_of_week', $today);

        if (! $hour || ! $hour->is_open) {
            return false;
        }

        $now = now()->format('H:i:s');
        return $now >= $hour->open_time && $now <= $hour->close_time;
    }
}
