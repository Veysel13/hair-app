<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            $table->enum('payment_method', ['cash', 'card', 'split', 'online'])->default('cash')->after('deposit_paid_at');
            $table->decimal('cash_amount', 10, 2)->default(0)->after('payment_method');
            $table->decimal('card_amount', 10, 2)->default(0)->after('cash_amount');
            $table->decimal('tip_amount', 10, 2)->default(0)->after('card_amount');
            $table->timestamp('completed_at')->nullable()->after('cancelled_at');
        });
    }

    public function down(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            $table->dropColumn(['payment_method', 'cash_amount', 'card_amount', 'tip_amount', 'completed_at']);
        });
    }
};
