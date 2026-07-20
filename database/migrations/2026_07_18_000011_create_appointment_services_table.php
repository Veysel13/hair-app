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
        // Bir randevuda birden fazla hizmet seçilebilir.
        // Fiyat ve süre, randevu oluşturulurken snapshot olarak kaydedilir
        // (ileride fiyat değişse bile geçmiş randevu etkilenmez).
        Schema::create('appointment_services', function (Blueprint $table) {
            $table->id();
            $table->foreignId('appointment_id')->constrained()->cascadeOnDelete();
            $table->foreignId('service_id')->constrained()->restrictOnDelete();
            $table->foreignId('staff_id')
                ->nullable()
                ->constrained('staff')
                ->nullOnDelete()
                ->comment('Farklı bir personel bu hizmeti verebilir');
            $table->unsignedSmallInteger('duration_minutes')->comment('Snapshot: randevu anındaki süre');
            $table->decimal('price', 10, 2)->comment('Snapshot: randevu anındaki fiyat');
            $table->unsignedTinyInteger('sort_order')->default(0)->comment('İşlemlerin yapılış sırası');
            $table->timestamps();

            $table->index(['appointment_id', 'sort_order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appointment_services');
    }
};
