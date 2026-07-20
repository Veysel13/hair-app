<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'business_id',
        'name',
        'brand',
        'sku',
        'category',
        'unit',
        'current_stock',
        'min_stock_level',
        'purchase_price',
        'sale_price',
        'is_for_sale',
        'is_active',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'current_stock'  => 'float',
            'min_stock_level'=> 'float',
            'purchase_price' => 'float',
            'sale_price'     => 'float',
            'is_for_sale'    => 'boolean',
            'is_active'      => 'boolean',
        ];
    }

    // ----------------------------------------------------------------
    // Relationships
    // ----------------------------------------------------------------

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    public function stockMovements(): HasMany
    {
        return $this->hasMany(StockMovement::class)->latest();
    }

    // ----------------------------------------------------------------
    // Scopes
    // ----------------------------------------------------------------

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeForSale($query)
    {
        return $query->where('is_for_sale', true)->where('is_active', true);
    }

    /** Kritik stok seviyesinin altına düşmüş ürünler */
    public function scopeLowStock($query)
    {
        return $query->whereColumn('current_stock', '<=', 'min_stock_level')
            ->where('is_active', true);
    }

    // ----------------------------------------------------------------
    // Helpers
    // ----------------------------------------------------------------

    public function isLowStock(): bool
    {
        return $this->current_stock <= $this->min_stock_level;
    }

    public function isOutOfStock(): bool
    {
        return $this->current_stock <= 0;
    }

    public function getFormattedStockAttribute(): string
    {
        return number_format($this->current_stock, 2, ',', '.') . ' ' . $this->unit;
    }

    public function getFormattedSalePriceAttribute(): ?string
    {
        return $this->sale_price
            ? number_format($this->sale_price, 2, ',', '.') . ' ₺'
            : null;
    }
}
