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
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_id')->constrained()->cascadeOnDelete();
            $table->foreignId('customer_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('staff_id')->constrained('staff')->restrictOnDelete();
            $table->date('appointment_date');
            $table->time('start_time');
            $table->time('end_time')->comment('Tüm seçilen hizmetlerin toplam süresine göre hesaplanır');
            $table->enum('status', [
                'pending',       // Oluşturuldu, kapora bekleniyor
                'confirmed',     // Onaylandı
                'in_progress',   // Hizmet başladı
                'completed',     // Tamamlandı
                'cancelled',     // İptal edildi
                'no_show',       // Müşteri gelmedi
            ])->default('pending');
            $table->unsignedSmallInteger('total_duration')->comment('Dakika cinsinden toplam süre');
            $table->decimal('total_price', 10, 2);
            $table->decimal('deposit_amount', 10, 2)->nullable();
            $table->boolean('deposit_paid')->default(false);
            $table->timestamp('deposit_paid_at')->nullable();
            $table->text('notes')->nullable()->comment('Müşteri notu');
            $table->text('internal_notes')->nullable()->comment('İşletme iç notu (müşteriye görünmez)');
            $table->text('cancellation_reason')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->timestamp('reminded_at')->nullable()->comment('Son hatırlatma gönderilme zamanı');
            $table->enum('source', ['app', 'walk_in', 'phone'])->default('app');
            $table->timestamps();
            $table->softDeletes();

            // Çakışma kontrolü ve takvim sorgular için
            $table->index(['staff_id', 'appointment_date', 'status']);
            $table->index(['business_id', 'appointment_date', 'status']);
            $table->index(['customer_id', 'appointment_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};
