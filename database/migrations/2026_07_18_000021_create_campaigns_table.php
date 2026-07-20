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
        Schema::create('campaigns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->text('description')->nullable();
            $table->enum('type', [
                'discount',      // Genel indirim
                'free_service',  // Ücretsiz hizmet ekle
                'package',       // Paket fiyatı
                'birthday',      // Doğum günü kampanyası
                'win_back',      // Geri kazanma (uzun süredir gelmeyen)
                'flash',         // Anlık kampanya
            ]);
            $table->enum('discount_type', ['percentage', 'fixed'])->nullable();
            $table->decimal('discount_value', 10, 2)->nullable();
            $table->decimal('min_booking_amount', 10, 2)->nullable()->comment('Minimum sepet tutarı');
            $table->json('applicable_service_ids')->nullable()->comment('Geçerli hizmet id\'leri. NULL = tümü');
            $table->json('applicable_days')->nullable()->comment('Geçerli haftanın günleri. Örn: [2, 3] = Salı, Çarşamba');
            $table->time('applicable_time_start')->nullable()->comment('Geçerli saat aralığı başlangıcı');
            $table->time('applicable_time_end')->nullable()->comment('Geçerli saat aralığı bitişi');
            $table->dateTime('starts_at');
            $table->dateTime('ends_at')->nullable();
            $table->unsignedInteger('max_uses')->nullable()->comment('Maksimum kullanım sayısı');
            $table->unsignedInteger('max_uses_per_customer')->nullable()->default(1);
            $table->unsignedInteger('used_count')->default(0);
            $table->boolean('is_active')->default(true);
            $table->string('code', 50)->nullable()->unique()->comment('Kampanya kodu (varsa)');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['business_id', 'is_active', 'starts_at', 'ends_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('campaigns');
    }
};
