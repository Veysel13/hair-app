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
        Schema::create('businesses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('owner_id')->constrained('users')->cascadeOnDelete();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('phone', 20);
            $table->string('email')->nullable();
            $table->string('website')->nullable();
            $table->string('address');
            $table->string('city', 100);
            $table->string('district', 100)->nullable();
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->enum('category', [
                'hair_salon',
                'nail_salon',
                'spa',
                'barber',
                'beauty_center',
                'other',
            ])->default('hair_salon');
            $table->string('logo')->nullable();
            $table->string('cover_image')->nullable();
            $table->boolean('is_active')->default(true);
            $table->decimal('avg_rating', 3, 2)->nullable();
            $table->unsignedInteger('total_reviews')->default(0);
            $table->boolean('deposit_required')->default(false);
            $table->unsignedTinyInteger('deposit_percentage')->nullable()->comment('Kapora yüzdesi (0-100)');
            $table->unsignedTinyInteger('cancellation_hours')->default(24)->comment('İptal için gereken minimum saat');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['city', 'category', 'is_active']);
            $table->index(['latitude', 'longitude']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('businesses');
    }
};
