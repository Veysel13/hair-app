<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockMovement extends Model
{
    use HasFactory;

    protected $fillable = [
        'business_id',
        'product_id',
        'type',
        'quantity',
        'unit_price',
        'related_appointment_id',
        'notes',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'quantity'   => 'float',
            'unit_price' => 'float',
        ];
    }

    // ----------------------------------------------------------------
    // Relationships
    // ----------------------------------------------------------------

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function relatedAppointment(): BelongsTo
    {
        return $this->belongsTo(Appointment::class, 'related_appointment_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // ----------------------------------------------------------------
    // Helpers
    // ----------------------------------------------------------------

    public function isEntry(): bool
    {
        return $this->type === 'in';
    }

    public function isExit(): bool
    {
        return in_array($this->type, ['out', 'sale', 'waste']);
    }

    public function getTypeLabelAttribute(): string
    {
        return match ($this->type) {
            'in'         => 'Stok Girişi',
            'out'        => 'Hizmette Kullanım',
            'sale'       => 'Satış',
            'adjustment' => 'Manuel Düzeltme',
            'waste'      => 'Fire / İsraf',
            default      => $this->type,
        };
    }

    public function getTotalValueAttribute(): ?float
    {
        return $this->unit_price
            ? round(abs($this->quantity) * $this->unit_price, 2)
            : null;
    }
}
