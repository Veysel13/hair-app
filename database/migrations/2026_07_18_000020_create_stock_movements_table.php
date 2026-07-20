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
        Schema::create('stock_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->enum('type', [
                'in',          // Stok girişi (satın alma)
                'out',         // Hizmette kullanım
                'sale',        // Müşteriye satış
                'adjustment',  // Manuel düzeltme
                'waste',       // Fire / israf
            ]);
            $table->decimal('quantity', 10, 3)->comment('Pozitif: giriş, Negatif: çıkış');
            $table->decimal('unit_price', 10, 2)->nullable()->comment('Birim fiyat (raporlama için)');
            $table->foreignId('related_appointment_id')
                ->nullable()
                ->constrained('appointments')
                ->nullOnDelete()
                ->comment('Kullanım randevusu (varsa)');
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->constrained('users')->restrictOnDelete();
            $table->timestamps();

            $table->index(['product_id', 'created_at', 'type']);
            $table->index(['business_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_movements');
    }
};
