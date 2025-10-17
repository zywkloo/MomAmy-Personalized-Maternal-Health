<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\FhirController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\VitalController;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function (): void {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
});

Route::middleware('auth:api')->group(function (): void {
    Route::get('fhir/patients/{patient}', [FhirController::class, 'patient']);
    Route::get('fhir/patients/{patient}/observations', [FhirController::class, 'observations']);
    Route::get('fhir/patients/{patient}/service-requests', [FhirController::class, 'serviceRequests']);
    Route::get('fhir/patients/{patient}/imaging-studies', [FhirController::class, 'imagingStudies']);

    Route::post('patients/{patient}/vitals', [VitalController::class, 'store']);

    Route::post('patients/{patient}/reports', [ReportController::class, 'store']);
    Route::get('patients/{patient}/reports', [ReportController::class, 'index']);
    Route::get('patients/{patient}/reports/{report}', [ReportController::class, 'show']);
});


