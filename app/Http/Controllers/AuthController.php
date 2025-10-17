<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function register(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'patient.first_name' => 'required|string|max:255',
            'patient.last_name' => 'required|string|max:255',
            'patient.date_of_birth' => 'nullable|date',
            'patient.gender' => 'nullable|string|max:15',
            'patient.contact_phone' => 'nullable|string|max:50',
            'patient.email' => 'nullable|email',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        $patientData = $validated['patient'];
        $patientData['fhir_id'] = Str::uuid()->toString();
        $patientData['email'] = $patientData['email'] ?? $validated['email'];
        $patientData['user_id'] = $user->id;

        $patient = $user->patient()->create($patientData);

        $tokenResult = $user->createToken('authToken');

        return response()->json([
            'user' => $user,
            'patient' => $patient,
            'token' => $tokenResult->accessToken,
            'token_type' => 'Bearer',
            'expires_at' => $tokenResult->token->expires_at,
        ], 201);
    }

    public function login(Request $request): JsonResponse
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $credentials['email'])->first();

        if (! $user || ! Hash::check($credentials['password'], $user->password)) {
            return response()->json([
                'message' => 'Invalid credentials provided.',
            ], 401);
        }

        $tokenResult = $user->createToken('authToken');

        return response()->json([
            'user' => $user,
            'patient' => $user->patient,
            'token' => $tokenResult->accessToken,
            'token_type' => 'Bearer',
            'expires_at' => $tokenResult->token->expires_at,
        ]);
    }
}

