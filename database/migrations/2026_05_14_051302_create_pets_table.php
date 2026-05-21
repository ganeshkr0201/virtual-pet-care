<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('species'); // dog, cat, bird, etc.
            $table->string('breed')->nullable();
            $table->enum('gender', ['male', 'female', 'unknown'])->default('unknown');
            $table->date('date_of_birth')->nullable();
            $table->decimal('weight', 6, 2)->nullable(); // in kg
            $table->string('color')->nullable();
            $table->string('microchip_id')->nullable();
            $table->text('allergies')->nullable();
            $table->text('medical_history')->nullable();
            $table->text('emergency_notes')->nullable();
            $table->enum('activity_level', ['low', 'moderate', 'high'])->default('moderate');
            $table->json('feeding_schedule')->nullable(); // times per day
            $table->string('vet_name')->nullable();
            $table->string('vet_phone')->nullable();
            $table->string('vet_email')->nullable();
            $table->string('vet_clinic')->nullable();
            $table->string('avatar')->nullable();
            $table->boolean('is_active')->default(true);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pets');
    }
};
