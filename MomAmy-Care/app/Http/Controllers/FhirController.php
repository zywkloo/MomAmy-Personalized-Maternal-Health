<?php

namespace App\Http\Controllers;

use App\Models\ImagingStudy;
use App\Models\Observation;
use App\Models\Patient;
use App\Models\Report;
use App\Models\ServiceRequest;
use Illuminate\Http\JsonResponse;

class FhirController extends Controller
{
    public function patient(Patient $patient): JsonResponse
    {
        return response()->json([
            'resourceType' => 'Patient',
            'id' => $patient->fhir_id,
            'name' => [[
                'given' => [$patient->first_name],
                'family' => $patient->last_name,
            ]],
            'gender' => $patient->gender,
            'birthDate' => optional($patient->date_of_birth)->toDateString(),
            'telecom' => array_filter([
                $patient->contact_phone
                    ? ['system' => 'phone', 'value' => $patient->contact_phone]
                    : null,
                $patient->email
                    ? ['system' => 'email', 'value' => $patient->email]
                    : null,
            ]),
            'address' => $patient->address ?? [],
            'extension' => array_filter([
                $patient->estimated_due_date
                    ? [
                        'url' => 'http://hl7.org/fhir/StructureDefinition/patient-estimatedAge',
                        'valueDate' => $patient->estimated_due_date->toDateString(),
                    ]
                    : null,
            ]),
        ]);
    }

    public function observations(Patient $patient): JsonResponse
    {
        $observations = Observation::where('patient_id', $patient->id)
            ->latest('effective_datetime')
            ->get()
            ->map(fn (Observation $observation) => $this->mapObservation($observation));

        return response()->json([
            'resourceType' => 'Bundle',
            'type' => 'collection',
            'entry' => $observations->all(),
        ]);
    }

    public function serviceRequests(Patient $patient): JsonResponse
    {
        $serviceRequests = ServiceRequest::where('patient_id', $patient->id)
            ->orderByDesc('authored_on')
            ->get()
            ->map(fn (ServiceRequest $serviceRequest) => $this->mapServiceRequest($serviceRequest));

        return response()->json([
            'resourceType' => 'Bundle',
            'type' => 'collection',
            'entry' => $serviceRequests->all(),
        ]);
    }

    public function imagingStudies(Patient $patient): JsonResponse
    {
        $studies = ImagingStudy::where('patient_id', $patient->id)
            ->orderByDesc('started_at')
            ->get()
            ->map(fn (ImagingStudy $study) => $this->mapImagingStudy($study));

        return response()->json([
            'resourceType' => 'Bundle',
            'type' => 'collection',
            'entry' => $studies->all(),
        ]);
    }

    protected function mapObservation(Observation $observation): array
    {
        return [
            'resource' => [
                'resourceType' => 'Observation',
                'id' => $observation->fhir_id,
                'status' => $observation->status,
                'code' => [
                    'coding' => [[
                        'system' => $observation->system,
                        'code' => $observation->code,
                        'display' => $observation->display,
                    ]],
                ],
                'subject' => [
                    'reference' => sprintf('Patient/%s', $observation->patient->fhir_id),
                ],
                'effectiveDateTime' => optional($observation->effective_datetime)->toAtomString(),
                'value' => $observation->value,
                'interpretation' => $observation->reference_range,
            ],
        ];
    }

    protected function mapServiceRequest(ServiceRequest $serviceRequest): array
    {
        return [
            'resource' => [
                'resourceType' => 'ServiceRequest',
                'id' => $serviceRequest->fhir_id,
                'status' => $serviceRequest->status,
                'intent' => $serviceRequest->intent,
                'code' => [
                    'coding' => [[
                        'system' => $serviceRequest->system,
                        'code' => $serviceRequest->code,
                        'display' => $serviceRequest->display,
                    ]],
                ],
                'authoredOn' => optional($serviceRequest->authored_on)->toAtomString(),
                'subject' => [
                    'reference' => sprintf('Patient/%s', $serviceRequest->patient->fhir_id),
                ],
                'reasonCode' => $serviceRequest->reason,
                'performer' => $serviceRequest->performer,
                'note' => $serviceRequest->notes,
            ],
        ];
    }

    protected function mapImagingStudy(ImagingStudy $study): array
    {
        return [
            'resource' => [
                'resourceType' => 'ImagingStudy',
                'id' => $study->fhir_id,
                'subject' => [
                    'reference' => sprintf('Patient/%s', $study->patient->fhir_id),
                ],
                'started' => optional($study->started_at)->toAtomString(),
                'description' => $study->description,
                'series' => $study->series,
            ],
        ];
    }
}

