<?php

namespace App\Services;

use App\Models\Observation;
use App\Models\Patient;
use Illuminate\Support\Str;

class FhirService
{
    public function patientToResource(Patient $patient): array
    {
        return [
            'resourceType' => 'Patient',
            'id' => $patient->fhir_id,
            'text' => [
                'status' => 'generated',
                'div' => sprintf(
                    '<div><p>%s %s</p><p>Due: %s</p></div>',
                    $patient->first_name,
                    $patient->last_name,
                    optional($patient->estimated_due_date)->toDateString()
                ),
            ],
            'name' => [[
                'use' => 'official',
                'family' => $patient->last_name,
                'given' => [$patient->first_name],
            ]],
            'telecom' => array_filter([
                $patient->contact_phone ? ['system' => 'phone', 'value' => $patient->contact_phone] : null,
                $patient->email ? ['system' => 'email', 'value' => $patient->email] : null,
            ]),
            'gender' => $patient->gender,
            'birthDate' => optional($patient->date_of_birth)->toDateString(),
            'address' => $patient->address ?? null,
            'extension' => array_filter([
                $patient->blood_type ? [
                    'url' => 'http://hl7.org/fhir/StructureDefinition/patient-birthPlace',
                    'valueString' => $patient->blood_type,
                ] : null,
                $patient->rh_factor ? [
                    'url' => 'http://hl7.org/fhir/StructureDefinition/patient-rh',
                    'valueString' => $patient->rh_factor,
                ] : null,
            ]),
        ];
    }

    public function observationFromVital(Patient $patient, array $vital, string $type): Observation
    {
        $code = match ($type) {
            'blood_pressure_systolic' => ['code' => '8480-6', 'system' => 'http://loinc.org', 'display' => 'Systolic blood pressure'],
            'blood_pressure_diastolic' => ['code' => '8462-4', 'system' => 'http://loinc.org', 'display' => 'Diastolic blood pressure'],
            'weight' => ['code' => '29463-7', 'system' => 'http://loinc.org', 'display' => 'Body weight'],
            'heart_rate' => ['code' => '8867-4', 'system' => 'http://loinc.org', 'display' => 'Heart rate'],
            'glucose' => ['code' => '2345-7', 'system' => 'http://loinc.org', 'display' => 'Glucose [Mass/volume] in Blood'],
            default => ['code' => $type, 'system' => 'http://momamy-care/fhir', 'display' => Str::title(str_replace('_', ' ', $type))],
        };

        return Observation::create([
            'fhir_id' => Str::uuid()->toString(),
            'patient_id' => $patient->id,
            'code' => $code['code'],
            'system' => $code['system'],
            'display' => $code['display'],
            'value_type' => is_array($vital['value'] ?? null) ? 'SampledData' : 'Quantity',
            'value' => $vital,
            'unit' => $vital['unit'] ?? null,
            'effective_datetime' => $vital['recorded_at'] ?? now(),
        ]);
    }
}

