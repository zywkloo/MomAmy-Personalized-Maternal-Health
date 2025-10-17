<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class VitalController extends Controller
{
    public function store(Request $request, Patient $patient): JsonResponse
    {
        $validated = $request->validate([
            'type' => 'required|string|max:100',
            'value' => 'nullable|numeric',
            'unit' => 'nullable|string|max:50',
            'metadata' => 'nullable|array',
            'recorded_at' => 'nullable|date',
        ]);

        $vital = $patient->vitals()->create($validated);

        return response()->json([
            'message' => 'Vital recorded successfully.',
            'data' => $vital,
        ], 201);
    }
}

