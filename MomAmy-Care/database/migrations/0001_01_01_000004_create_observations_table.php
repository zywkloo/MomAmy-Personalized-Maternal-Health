<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('observations', function (Blueprint $table): void {
            $table->id();
            $table->uuid('fhir_id')->unique();
            $table->foreignId('patient_id')->constrained()->cascadeOnDelete();
            $table->string('code');
            $table->string('system')->nullable();
            $table->string('display')->nullable();
            $table->string('value_type')->nullable();
            $table->json('value')->nullable();
            $table->string('unit')->nullable();
            $table->timestamp('effective_datetime')->nullable();
            $table->string('status')->default('final');
            $table->json('reference_range')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('observations');
    }
};

