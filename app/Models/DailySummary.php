<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DailySummary extends Model
{
    use HasFactory;

    protected $fillable = [
        'business_id',
        'summary_date',
        'total_revenue',
        'total_expenses',
        'net_profit',
        'total_appointments',
        'completed_appointments',
        'cancelled_appointments',
        'no_show_appointments',
        'new_customers',
    ];

    protected function casts(): array
    {
        return [
            'summary_date'    => 'date',
            'total_revenue'   => 'float',
            'total_expenses'  => 'float',
            'net_profit'      => 'float',
        ];
    }

    // ----------------------------------------------------------------
    // Relationships
    // ----------------------------------------------------------------

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    // ----------------------------------------------------------------
    // Scopes
    // ----------------------------------------------------------------

    public function scopeInMonth($query, int $year, int $month)
    {
        return $query->whereYear('summary_date', $year)
            ->whereMonth('summary_date', $month);
    }

    public function scopeInRange($query, string $from, string $to)
    {
        return $query->whereBetween('summary_date', [$from, $to]);
    }

    // ----------------------------------------------------------------
    // Helpers
    // ----------------------------------------------------------------

    public function getFormattedRevenueAttribute(): string
    {
        return number_format($this->total_revenue, 2, ',', '.') . ' ₺';
    }

    public function getFormattedNetProfitAttribute(): string
    {
        return number_format($this->net_profit, 2, ',', '.') . ' ₺';
    }

    public function isProfitable(): bool
    {
        return $this->net_profit > 0;
    }
}
