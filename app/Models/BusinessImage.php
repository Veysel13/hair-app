<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BusinessImage extends Model
{
    use HasFactory;

    protected $fillable = [
        'business_id',
        'image_path',
        'caption',
        'sort_order',
    ];

    // ----------------------------------------------------------------
    // Relationships
    // ----------------------------------------------------------------

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }
}
