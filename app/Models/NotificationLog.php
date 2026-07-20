<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class NotificationLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'business_id',
        'user_id',
        'type',
        'channel',
        'title',
        'message',
        'status',
        'sent_at',
        'error_message',
        'related_id',
        'related_type',
    ];

    protected function casts(): array
    {
        return [
            'sent_at' => 'datetime',
        ];
    }

    // ----------------------------------------------------------------
    // Relationships
    // ----------------------------------------------------------------

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Polymorphic: hangi kaynakla ilgili olduğunu döner.
     * Örn: Appointment, Waitlist, Campaign ...
     */
    public function related(): MorphTo
    {
        return $this->morphTo('related');
    }

    // ----------------------------------------------------------------
    // Scopes
    // ----------------------------------------------------------------

    public function scopeSent($query)
    {
        return $query->where('status', 'sent');
    }

    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    public function scopeByChannel($query, string $channel)
    {
        return $query->where('channel', $channel);
    }

    public function scopeByType($query, string $type)
    {
        return $query->where('type', $type);
    }

    // ----------------------------------------------------------------
    // Helpers
    // ----------------------------------------------------------------

    public function isSent(): bool
    {
        return $this->status === 'sent';
    }

    public function isFailed(): bool
    {
        return $this->status === 'failed';
    }

    public function getChannelLabelAttribute(): string
    {
        return match ($this->channel) {
            'sms'       => 'SMS',
            'whatsapp'  => 'WhatsApp',
            'email'     => 'E-posta',
            'push'      => 'Bildirim',
            default     => $this->channel,
        };
    }

    public function getTypeLabelAttribute(): string
    {
        return match ($this->type) {
            'reminder'       => 'Hatırlatma',
            'confirmation'   => 'Onay',
            'waitlist'       => 'Bekleme Listesi',
            'birthday'       => 'Doğum Günü',
            'win_back'       => 'Geri Kazanım',
            'campaign'       => 'Kampanya',
            'review_request' => 'Yorum İsteği',
            default          => $this->type,
        };
    }
}
