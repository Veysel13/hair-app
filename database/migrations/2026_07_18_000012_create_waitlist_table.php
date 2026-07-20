<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('waitlist', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_id')->constrained()->cascadeOnDelete();
            $table->foreignId('customer_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('staff_id')
                ->nullable()
                ->constrained('staff')
                ->nullOnDelete()
                ->comment('Belirli bir personel talebi varsa');
            $table->json('service_ids')->comment('İstenen hizmet id\'leri');
            $table->date('preferred_date')->nullable();
            $table->time('preferred_time_start')->nullable();
            $table->time('preferred_time_end')->nullable();
            $table->enum('status', [
                'waiting',   // Bekleme listesinde
                'notified',  // Yer açıldığında bildirim gönderildi
                'booked',    // Randevu oluşturuldu
                'expired',   // Süre doldu
            ])->default('waiting');
            $table->timestamp('notified_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();

            $table->index(['business_id', 'status', 'preferred_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('waitlist');
    }
};
