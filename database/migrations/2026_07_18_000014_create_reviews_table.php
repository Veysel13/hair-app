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
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('appointment_id')->unique()->constrained()->cascadeOnDelete();
            $table->foreignId('business_id')->constrained()->cascadeOnDelete();
            $table->foreignId('customer_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('staff_id')
                ->nullable()
                ->constrained('staff')
                ->nullOnDelete();
            $table->unsignedTinyInteger('rating')->comment('1-5 yıldız');
            $table->unsignedTinyInteger('staff_rating')->nullable()->comment('Personele özel puan (1-5)');
            $table->text('comment')->nullable();
            $table->text('business_reply')->nullable()->comment('İşletme yanıtı');
            $table->timestamp('business_replied_at')->nullable();
            $table->boolean('is_visible')->default(true)->comment('Gizlenebilir yorum');
            $table->timestamps();

            $table->index(['business_id', 'is_visible', 'created_at']);
            $table->index(['staff_id', 'rating']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};
