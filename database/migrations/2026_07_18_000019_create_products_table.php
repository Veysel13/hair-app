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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('brand')->nullable()->comment('Marka adı');
            $table->string('sku', 100)->nullable()->comment('Stok kodu / barkod');
            $table->string('category', 100)->nullable()->comment('Ürün kategorisi (örn: boya, şampuan)');
            $table->string('unit', 20)->default('adet')->comment('Birim: adet, ml, gr, lt, kg');
            $table->decimal('current_stock', 10, 3)->default(0)->comment('Mevcut stok miktarı');
            $table->decimal('min_stock_level', 10, 3)->default(0)->comment('Kritik stok seviyesi');
            $table->decimal('purchase_price', 10, 2)->nullable()->comment('Alış fiyatı');
            $table->decimal('sale_price', 10, 2)->nullable()->comment('Satış fiyatı (müşteriye satış için)');
            $table->boolean('is_for_sale')->default(false)->comment('Müşteriye satışa sunuluyor mu');
            $table->boolean('is_active')->default(true);
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['business_id', 'is_active', 'category']);
            // Kritik seviye altındaki ürünler için sorgu index'i
            $table->index(['business_id', 'current_stock', 'min_stock_level']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
