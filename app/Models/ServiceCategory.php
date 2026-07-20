<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ServiceCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'business_id',
        'name',
        'icon',
        'color',
        'sort_order',
    ];

    // ----------------------------------------------------------------
    // Relationships
    // ----------------------------------------------------------------

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    public function services(): HasMany
    {
        return $this->hasMany(Service::class, 'category_id')->orderBy('sort_order');
    }

    public function activeServices(): HasMany
    {
        return $this->hasMany(Service::class, 'category_id')
            ->where('is_active', true)
            ->orderBy('sort_order');
    }
}
