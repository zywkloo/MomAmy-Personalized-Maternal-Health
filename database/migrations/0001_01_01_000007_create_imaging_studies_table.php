<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('imaging_studies', function (Blueprint $table): void {
            $table->id();
            $table->uuid('fhir_id')->unique();
            $table->foreignId('patient_id')->constrained()->cascadeOnDelete();
            $table->string('study_uid');
            $table->string('modality')->nullable();
            $table->string('description')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->json('series')->nullable();
            $table->json('identifiers')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('imaging_studies');
    }
};

