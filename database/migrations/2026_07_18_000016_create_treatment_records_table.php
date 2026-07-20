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
        Schema::create('treatment_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('appointment_id')->constrained()->cascadeOnDelete();
            $table->foreignId('customer_profile_id')->constrained('customer_profiles')->cascadeOnDelete();
            $table->foreignId('staff_id')->constrained('staff')->restrictOnDelete();
            $table->foreignId('service_id')->constrained()->restrictOnDelete();
            $table->json('formula')->nullable()->comment('Boya formülü: {"base": "7.1", "mix": "20vol", "ratio": "1:2"}');
            $table->json('products_used')->nullable()->comment('Kullanılan ürünler: [{"product_id": 1, "amount": "50ml"}]');
            $table->text('notes')->nullable()->comment('İşlem notu, özel durumlar');
            $table->string('before_image')->nullable()->comment('İşlem öncesi fotoğraf');
            $table->string('after_image')->nullable()->comment('İşlem sonrası fotoğraf');
            $table->timestamps();

            $table->index(['customer_profile_id', 'created_at']);
            $table->index(['staff_id', 'service_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('treatment_records');
    }
};
