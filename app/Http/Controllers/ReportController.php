<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Models\Report;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ReportController extends Controller
{
    public function index(Patient $patient): JsonResponse
    {
        $reports = $patient->reports()->latest()->paginate(15);

        return response()->json($reports);
    }

    public function show(Patient $patient, Report $report): JsonResponse
    {
        abort_unless($report->patient_id === $patient->id, 404);

        return response()->json($report);
    }

    public function store(Request $request, Patient $patient): JsonResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'type' => 'nullable|string|max:100',
            'issued_at' => 'nullable|date',
            'conclusion' => 'nullable|array',
            'performers' => 'nullable|array',
            'presented_forms' => 'nullable|array',
            'file' => 'required|file|mimes:pdf,jpg,jpeg,png|max:10240',
        ]);

        $file = $validated['file'];
        $path = $file->store('reports', ['disk' => 'public']);

        $report = $patient->reports()->create([
            'fhir_id' => Str::uuid()->toString(),
            'title' => $validated['title'],
            'type' => $validated['type'] ?? $file->getClientOriginalExtension(),
            'status' => 'final',
            'file_path' => $path,
            'file_type' => $file->getClientMimeType(),
            'file_size' => $file->getSize(),
            'issued_at' => $validated['issued_at'] ?? now(),
            'conclusion' => $validated['conclusion'] ?? [],
            'performers' => $validated['performers'] ?? [],
            'presented_forms' => $validated['presented_forms'] ?? [],
        ]);

        return response()->json([
            'message' => 'Report uploaded successfully.',
            'data' => $report,
            'download_url' => Storage::disk('public')->url($report->file_path),
        ], 201);
    }
}

