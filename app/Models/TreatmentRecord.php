<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TreatmentRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'appointment_id',
        'customer_profile_id',
        'staff_id',
        'service_id',
        'formula',
        'products_used',
        'notes',
        'before_image',
        'after_image',
    ];

    protected function casts(): array
    {
        return [
            'formula'       => 'array',
            'products_used' => 'array',
        ];
    }

    // ----------------------------------------------------------------
    // Relationships
    // ----------------------------------------------------------------

    public function appointment(): BelongsTo
    {
        return $this->belongsTo(Appointment::class);
    }

    public function customerProfile(): BelongsTo
    {
        return $this->belongsTo(CustomerProfile::class);
    }

    public function staff(): BelongsTo
    {
        return $this->belongsTo(Staff::class);
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    // ----------------------------------------------------------------
    // Helpers
    // ----------------------------------------------------------------

    /** Formülü okunabilir metin olarak döner. Örn: "7.1 + 20vol (1:2)" */
    public function getFormulaSummaryAttribute(): ?string
    {
        if (empty($this->formula)) {
            return null;
        }

        $f = $this->formula;
        $parts = [];

        if (! empty($f['base'])) {
            $parts[] = $f['base'];
        }
        if (! empty($f['mix'])) {
            $parts[] = $f['mix'];
        }
        if (! empty($f['ratio'])) {
            $parts[] = "({$f['ratio']})";
        }

        return implode(' + ', $parts) ?: null;
    }
}
