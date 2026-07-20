<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password', 'phone', 'avatar', 'role', 'birth_date', 'notification_preferences'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, SoftDeletes;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at'          => 'datetime',
            'password'                   => 'hashed',
            'birth_date'                 => 'date',
            'notification_preferences'   => 'array',
        ];
    }

    // ----------------------------------------------------------------
    // Relationships
    // ----------------------------------------------------------------

    /** İşletme sahibinin işletmeleri */
    public function businesses(): HasMany
    {
        return $this->hasMany(Business::class, 'owner_id');
    }

    /** Personel hesabı bağlantısı */
    public function staffProfiles(): HasMany
    {
        return $this->hasMany(Staff::class);
    }

    /** Müşteri randevuları */
    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class, 'customer_id');
    }

    /** Müşteri CRM profilleri (işletme başına bir profil) */
    public function customerProfiles(): HasMany
    {
        return $this->hasMany(CustomerProfile::class, 'customer_id');
    }

    /** Yaptığı yorumlar */
    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class, 'customer_id');
    }

    /** Ödemeler */
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class, 'customer_id');
    }

    /** Bekleme listesi kayıtları */
    public function waitlistEntries(): HasMany
    {
        return $this->hasMany(Waitlist::class, 'customer_id');
    }

    /** Gönderilen bildirim kayıtları */
    public function notificationLogs(): HasMany
    {
        return $this->hasMany(NotificationLog::class);
    }

    // ----------------------------------------------------------------
    // Helpers
    // ----------------------------------------------------------------

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isBusinessOwner(): bool
    {
        return $this->role === 'business_owner';
    }

    public function isCustomer(): bool
    {
        return $this->role === 'customer';
    }

    public function isStaff(): bool
    {
        return $this->role === 'staff';
    }
}
