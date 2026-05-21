<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reminders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('pet_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->enum('type', [
                'feeding', 'walking', 'exercise', 'grooming',
                'medication', 'vet_appointment', 'vaccination',
                'training', 'water', 'other'
            ])->default('other');
            $table->time('reminder_time');
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->enum('repeat', ['none', 'daily', 'weekly', 'monthly'])->default('daily');
            $table->json('repeat_days')->nullable(); // [0,1,2,3,4,5,6] for weekly
            $table->boolean('is_active')->default(true);
            $table->boolean('email_notify')->default(true);
            $table->boolean('push_notify')->default(true);
            $table->integer('snooze_minutes')->default(10);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reminders');
    }
};
