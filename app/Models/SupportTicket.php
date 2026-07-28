<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SupportTicket extends Model
{
    use HasFactory;

    protected $fillable = [
        'business_id',
        'user_id',
        'subject',
        'category',
        'status',
        'last_message_at',
    ];

    protected $casts = [
        'last_message_at' => 'datetime',
    ];

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function messages(): HasMany
    {
        return $this->hasMany(SupportMessage::class)->orderBy('created_at', 'asc');
    }

    public function getCategoryLabelAttribute(): string
    {
        return match ($this->category) {
            'question' => '❓ Soru & Bilgi',
            'suggestion' => '💡 Görüş / Öneri',
            'technical' => '🔧 Teknik Destek',
            'billing' => '💳 Fatura / Ödeme',
            default => '📌 Genel Destek',
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'open' => 'Açık Talep',
            'replied' => 'Yanıtlandı',
            'closed' => 'Çözüldü / Kapatıldı',
            default => 'İşlemde',
        };
    }
}
