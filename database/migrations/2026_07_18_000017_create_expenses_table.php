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
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_id')->constrained()->cascadeOnDelete();
            $table->foreignId('created_by')->constrained('users')->restrictOnDelete();
            $table->enum('category', [
                'rent',        // Kira
                'utilities',   // Faturalar (elektrik, su, internet)
                'salary',      // Personel maaşı
                'supplies',    // Sarf malzemeleri
                'equipment',   // Ekipman
                'marketing',   // Reklam/pazarlama
                'tax',         // Vergi
                'insurance',   // Sigorta
                'other',       // Diğer
            ]);
            $table->string('description');
            $table->decimal('amount', 10, 2);
            $table->date('expense_date');
            $table->string('receipt_image')->nullable()->comment('Fiş veya fatura fotoğrafı');
            $table->boolean('is_recurring')->default(false)->comment('Yinelenen gider mi');
            $table->enum('recurring_period', ['weekly', 'monthly', 'yearly'])->nullable();
            $table->timestamps();

            $table->index(['business_id', 'expense_date', 'category']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expenses');
    }
};
