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
        // Günlük özet tablosu: raporlar için pahalı sorguların önüne geçmek amacıyla
        // her gün sonunda job ile hesaplanıp önbelleğe alınır.
        Schema::create('daily_summaries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_id')->constrained()->cascadeOnDelete();
            $table->date('summary_date');
            $table->decimal('total_revenue', 10, 2)->default(0)->comment('Toplam gelir');
            $table->decimal('total_expenses', 10, 2)->default(0)->comment('Toplam gider');
            $table->decimal('net_profit', 10, 2)->default(0)->comment('Net kâr (gelir - gider)');
            $table->unsignedInteger('total_appointments')->default(0);
            $table->unsignedInteger('completed_appointments')->default(0);
            $table->unsignedInteger('cancelled_appointments')->default(0);
            $table->unsignedInteger('no_show_appointments')->default(0);
            $table->unsignedInteger('new_customers')->default(0)->comment('İlk kez gelen müşteri sayısı');
            $table->timestamps();

            $table->unique(['business_id', 'summary_date']);
            $table->index(['business_id', 'summary_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('daily_summaries');
    }
};
