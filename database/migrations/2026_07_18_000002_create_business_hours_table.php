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
        Schema::create('business_hours', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_id')->constrained()->cascadeOnDelete();
            $table->tinyInteger('day_of_week')->comment('0=Pazar, 1=Pazartesi, ..., 6=Cumartesi');
            $table->boolean('is_open')->default(true);
            $table->time('open_time')->nullable();
            $table->time('close_time')->nullable();
            $table->timestamps();

            $table->unique(['business_id', 'day_of_week']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('business_hours');
    }
};
