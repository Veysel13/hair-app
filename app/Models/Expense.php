<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Expense extends Model
{
    use HasFactory;

    protected $fillable = [
        'business_id',
        'created_by',
        'category',
        'description',
        'amount',
        'expense_date',
        'receipt_image',
        'is_recurring',
        'recurring_period',
    ];

    protected function casts(): array
    {
        return [
            'amount'       => 'float',
            'expense_date' => 'date',
            'is_recurring' => 'boolean',
        ];
    }

    // ----------------------------------------------------------------
    // Relationships
    // ----------------------------------------------------------------

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // ----------------------------------------------------------------
    // Scopes
    // ----------------------------------------------------------------

    public function scopeInMonth($query, int $year, int $month)
    {
        return $query->whereYear('expense_date', $year)
            ->whereMonth('expense_date', $month);
    }

    public function scopeInCategory($query, string $category)
    {
        return $query->where('category', $category);
    }

    // ----------------------------------------------------------------
    // Helpers
    // ----------------------------------------------------------------

    public function getFormattedAmountAttribute(): string
    {
        return number_format($this->amount, 2, ',', '.') . ' ₺';
    }

    public function getCategoryLabelAttribute(): string
    {
        return match ($this->category) {
            'rent'        => 'Kira',
            'utilities'   => 'Faturalar',
            'salary'      => 'Personel Maaşı',
            'supplies'    => 'Sarf Malzemesi',
            'equipment'   => 'Ekipman',
            'marketing'   => 'Reklam / Pazarlama',
            'tax'         => 'Vergi',
            'insurance'   => 'Sigorta',
            'other'       => 'Diğer',
            default       => $this->category,
        };
    }
}
