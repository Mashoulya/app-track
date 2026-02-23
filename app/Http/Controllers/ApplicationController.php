<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ApplicationController extends Controller
{
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            // entreprise
            'name' => 'required|string|max:191',
            'position' => 'required|string|max:191',
            'location' => 'nullable|string|max:191',
            'source' => 'nullable|string|max:191',
            'contact' => 'required|string',
            'notes' => 'nullable|string',

            // candidature
            'type' => 'required|in:Stage,Alternance,Emploi',
            'status' => 'required|in:Candidature envoyée,En attente,Entretien,Refusée,Sans réponse',
        ]);

        // 1️⃣ création company
        $company = Company::create([
            'name' => $validatedData['name'],
            'position' => $validatedData['position'],
            'location' => $validatedData['location'] ?? null,
            'source' => $validatedData['source'] ?? null,
            'contact' => $validatedData['contact'],
            'notes' => $validatedData['notes'] ?? null,
        ]);

        // 2️⃣ création application
        $application = Application::create([
            'type' => $validatedData['type'],
            'status' => $validatedData['status'],
            'user_id' => Auth::id(),
            'company_id' => $company->id,
        ]);

        return response()->json([
            'message' => 'Candidature créée avec succès',
            'application' => $application,
        ], 201);
    }
}