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
        // Pivot: hangi personel hangi hizmetleri verebilir,
        // personele özel süre/fiyat overrides ile
        Schema::create('service_staff', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_id')->constrained()->cascadeOnDelete();
            $table->foreignId('staff_id')->constrained('staff')->cascadeOnDelete();
            $table->unsignedSmallInteger('custom_duration')->nullable()
                ->comment('Personele özel süre (dakika). NULL ise servis standart süre kullanılır.');
            $table->decimal('custom_price', 10, 2)->nullable()
                ->comment('Personele özel fiyat. NULL ise servis standart fiyatı kullanılır.');
            $table->timestamps();

            $table->unique(['service_id', 'staff_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_staff');
    }
};
