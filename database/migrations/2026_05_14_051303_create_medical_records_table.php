<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('medical_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pet_id')->constrained()->cascadeOnDelete();
            $table->enum('type', ['checkup', 'illness', 'surgery', 'prescription', 'weight_log', 'symptom', 'other'])->default('checkup');
            $table->string('title');
            $table->text('description')->nullable();
            $table->date('record_date');
            $table->decimal('weight', 6, 2)->nullable();
            $table->string('vet_name')->nullable();
            $table->text('diagnosis')->nullable();
            $table->text('treatment')->nullable();
            $table->text('medications')->nullable();
            $table->string('attachment_path')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('medical_records');
    }
};
