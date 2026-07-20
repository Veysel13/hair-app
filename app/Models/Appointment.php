<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Appointment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'business_id',
        'customer_id',
        'staff_id',
        'appointment_date',
        'start_time',
        'end_time',
        'status',
        'total_duration',
        'total_price',
        'deposit_amount',
        'deposit_paid',
        'deposit_paid_at',
        'notes',
        'internal_notes',
        'cancellation_reason',
        'cancelled_at',
        'reminded_at',
        'source',
    ];

    protected function casts(): array
    {
        return [
            'appointment_date' => 'date',
            'deposit_paid'     => 'boolean',
            'deposit_paid_at'  => 'datetime',
            'cancelled_at'     => 'datetime',
            'reminded_at'      => 'datetime',
            'total_price'      => 'float',
            'deposit_amount'   => 'float',
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

    /** Randevuya ait hizmet kalemleri */
    public function services(): HasMany
    {
        return $this->hasMany(AppointmentService::class)->orderBy('sort_order');
    }

    /** Randevunun yorumu (1:1) */
    public function review(): HasOne
    {
        return $this->hasOne(Review::class);
    }

    /** Ödemeler */
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    /** Kapora ödemesi */
    public function depositPayment(): HasOne
    {
        return $this->hasOne(Payment::class)->where('type', 'deposit');
    }

    /** Uygulanan kampanya */
    public function campaignUsage(): HasOne
    {
        return $this->hasOne(CampaignUsage::class);
    }

    /** İşlem reçeteleri */
    public function treatmentRecords(): HasMany
    {
        return $this->hasMany(TreatmentRecord::class);
    }

    // ----------------------------------------------------------------
    // Scopes
    // ----------------------------------------------------------------

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeConfirmed($query)
    {
        return $query->where('status', 'confirmed');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeCancelled($query)
    {
        return $query->where('status', 'cancelled');
    }

    public function scopeUpcoming($query)
    {
        return $query->whereIn('status', ['pending', 'confirmed'])
            ->where('appointment_date', '>=', now()->toDateString());
    }

    public function scopeToday($query)
    {
        return $query->where('appointment_date', now()->toDateString());
    }

    // ----------------------------------------------------------------
    // Helpers
    // ----------------------------------------------------------------

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isConfirmed(): bool
    {
        return $this->status === 'confirmed';
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function isCancelled(): bool
    {
        return $this->status === 'cancelled';
    }

    public function canBeCancelled(): bool
    {
        if (! in_array($this->status, ['pending', 'confirmed'])) {
            return false;
        }
        $hoursUntil = now()->diffInHours($this->appointment_date->setTimeFromTimeString($this->start_time), false);
        return $hoursUntil >= $this->business->cancellation_hours;
    }

    public function getFormattedTotalPriceAttribute(): string
    {
        return number_format($this->total_price, 2, ',', '.') . ' ₺';
    }

    public function getRemainingAmountAttribute(): float
    {
        $paid = $this->payments->where('status', 'completed')->sum('amount');
        return max(0, $this->total_price - $paid);
    }
}
