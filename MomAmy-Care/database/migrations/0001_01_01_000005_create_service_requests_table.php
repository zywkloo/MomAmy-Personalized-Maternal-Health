<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('service_requests', function (Blueprint $table): void {
            $table->id();
            $table->uuid('fhir_id')->unique();
            $table->foreignId('patient_id')->constrained()->cascadeOnDelete();
            $table->string('code');
            $table->string('system')->nullable();
            $table->string('display')->nullable();
            $table->string('status')->default('active');
            $table->string('intent')->default('order');
            $table->timestamp('authored_on')->nullable();
            $table->json('performer')->nullable();
            $table->json('reason')->nullable();
            $table->json('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('service_requests');
    }
};

