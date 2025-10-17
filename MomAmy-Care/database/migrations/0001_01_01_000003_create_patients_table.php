<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('patients', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->uuid('fhir_id')->unique();
            $table->string('first_name');
            $table->string('last_name');
            $table->date('date_of_birth')->nullable();
            $table->string('gender', 15)->nullable();
            $table->string('contact_phone')->nullable();
            $table->string('email')->nullable();
            $table->string('blood_type')->nullable();
            $table->string('rh_factor')->nullable();
            $table->json('allergies')->nullable();
            $table->json('address')->nullable();
            $table->string('gravidity')->nullable();
            $table->string('parity')->nullable();
            $table->date('estimated_due_date')->nullable();
            $table->date('last_menstrual_period')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('patients');
    }
};

