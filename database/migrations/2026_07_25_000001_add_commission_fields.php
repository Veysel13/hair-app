<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('businesses', function (Blueprint $table) {
            $table->boolean('enable_commission')->default(false)->after('deposit_percentage');
        });

        Schema::table('staff', function (Blueprint $table) {
            $table->decimal('commission_rate', 5, 2)->default(30.00)->after('is_active');
        });

        Schema::table('appointment_services', function (Blueprint $table) {
            $table->decimal('commission_rate', 5, 2)->nullable()->after('price');
            $table->decimal('commission_amount', 10, 2)->nullable()->after('commission_rate');
        });
    }

    public function down(): void
    {
        Schema::table('businesses', function (Blueprint $table) {
            $table->dropColumn('enable_commission');
        });

        Schema::table('staff', function (Blueprint $table) {
            $table->dropColumn('commission_rate');
        });

        Schema::table('appointment_services', function (Blueprint $table) {
            $table->dropColumn(['commission_rate', 'commission_amount']);
        });
    }
};
