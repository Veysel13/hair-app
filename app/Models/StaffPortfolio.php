<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StaffPortfolio extends Model
{
    use HasFactory;

    protected $table = 'staff_portfolio';

    protected $fillable = [
        'staff_id',
        'image_path',
        'service_category',
        'caption',
        'sort_order',
    ];

    // ----------------------------------------------------------------
    // Relationships
    // ----------------------------------------------------------------

    public function staff(): BelongsTo
    {
        return $this->belongsTo(Staff::class);
    }
}
