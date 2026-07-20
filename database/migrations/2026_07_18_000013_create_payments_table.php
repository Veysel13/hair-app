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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('appointment_id')->constrained()->cascadeOnDelete();
            $table->foreignId('business_id')->constrained()->cascadeOnDelete();
            $table->foreignId('customer_id')->constrained('users')->cascadeOnDelete();
            $table->decimal('amount', 10, 2);
            $table->enum('type', [
                'deposit',   // Kapora
                'full',      // Tam ödeme
                'partial',   // Kısmi ödeme
                'refund',    // İade
            ]);
            $table->enum('method', [
                'cash',
                'credit_card',
                'debit_card',
                'bank_transfer',
                'online',        // Iyzico, Stripe vs.
            ])->default('cash');
            $table->enum('status', [
                'pending',
                'completed',
                'failed',
                'refunded',
            ])->default('pending');
            $table->string('transaction_id')->nullable()->comment('Ödeme sistemi referans numarası');
            $table->json('gateway_response')->nullable()->comment('Ödeme gateway ham yanıtı');
            $table->timestamp('paid_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['business_id', 'status', 'paid_at']);
            $table->index(['customer_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
