<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reminder_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reminder_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->date('scheduled_date');
            $table->time('scheduled_time');
            $table->enum('status', ['pending', 'completed', 'missed', 'snoozed'])->default('pending');
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('snoozed_until')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reminder_logs');
    }
};
