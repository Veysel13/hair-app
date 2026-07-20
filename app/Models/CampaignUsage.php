<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CampaignUsage extends Model
{
    use HasFactory;

    protected $fillable = [
        'campaign_id',
        'appointment_id',
        'customer_id',
        'discount_amount',
    ];

    protected function casts(): array
    {
        return [
            'discount_amount' => 'float',
        ];
    }

    // ----------------------------------------------------------------
    // Relationships
    // ----------------------------------------------------------------

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(Campaign::class);
    }

    public function appointment(): BelongsTo
    {
        return $this->belongsTo(Appointment::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    // ----------------------------------------------------------------
    // Helpers
    // ----------------------------------------------------------------

    public function getFormattedDiscountAttribute(): string
    {
        return number_format($this->discount_amount, 2, ',', '.') . ' ₺';
    }
}
