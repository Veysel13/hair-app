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
        Schema::create('customer_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_id')->constrained()->cascadeOnDelete();
            $table->foreignId('customer_id')->constrained('users')->cascadeOnDelete();
            $table->text('allergies')->nullable()->comment('Alerji ve hassasiyet notları');
            $table->string('skin_type', 50)->nullable();
            $table->string('hair_type', 100)->nullable()->comment('Örn: kuru, yağlı, kıvırcık, boyalı');
            $table->string('hair_color', 50)->nullable()->comment('Mevcut saç rengi');
            $table->text('notes')->nullable()->comment('Genel müşteri notları');
            $table->timestamp('last_visit_at')->nullable();
            $table->unsignedInteger('total_visits')->default(0);
            $table->decimal('total_spent', 10, 2)->default(0);
            $table->boolean('is_vip')->default(false);
            $table->timestamps();

            $table->unique(['business_id', 'customer_id']);
            $table->index(['business_id', 'last_visit_at']);
            $table->index(['business_id', 'is_vip']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_profiles');
    }
};
