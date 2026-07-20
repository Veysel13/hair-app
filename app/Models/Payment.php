<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'appointment_id',
        'business_id',
        'customer_id',
        'amount',
        'type',
        'method',
        'status',
        'transaction_id',
        'gateway_response',
        'paid_at',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'amount'           => 'float',
            'gateway_response' => 'array',
            'paid_at'          => 'datetime',
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

    // ----------------------------------------------------------------
    // Scopes
    // ----------------------------------------------------------------

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeDeposits($query)
    {
        return $query->where('type', 'deposit');
    }

    // ----------------------------------------------------------------
    // Helpers
    // ----------------------------------------------------------------

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function isRefund(): bool
    {
        return $this->type === 'refund';
    }

    public function getFormattedAmountAttribute(): string
    {
        return number_format($this->amount, 2, ',', '.') . ' ₺';
    }

    public function getMethodLabelAttribute(): string
    {
        return match ($this->method) {
            'cash'          => 'Nakit',
            'credit_card'   => 'Kredi Kartı',
            'debit_card'    => 'Banka Kartı',
            'bank_transfer' => 'Banka Transferi',
            'online'        => 'Online Ödeme',
            default         => $this->method,
        };
    }
}
