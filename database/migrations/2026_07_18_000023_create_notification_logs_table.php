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
        Schema::create('notification_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('type', 50)->comment('reminder, confirmation, waitlist, birthday, win_back, campaign, review_request');
            $table->enum('channel', ['sms', 'whatsapp', 'email', 'push'])->default('sms');
            $table->string('title');
            $table->text('message');
            $table->enum('status', ['pending', 'sent', 'failed'])->default('pending');
            $table->timestamp('sent_at')->nullable();
            $table->string('error_message')->nullable()->comment('Gönderim hatası (varsa)');
            // Polymorphic ilişki: hangi kaynakla ilgili olduğunu tutar
            $table->unsignedBigInteger('related_id')->nullable();
            $table->string('related_type', 100)->nullable()->comment('App\\Models\\Appointment gibi');
            $table->timestamps();

            $table->index(['user_id', 'type', 'status']);
            $table->index(['business_id', 'type', 'created_at']);
            $table->index(['related_type', 'related_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notification_logs');
    }
};
