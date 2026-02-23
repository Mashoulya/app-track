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

        // création entreprise
        $company = Company::create([
            'name' => $validatedData['name'],
            'position' => $validatedData['position'],
            'location' => $validatedData['location'] ?? null,
            'source' => $validatedData['source'] ?? null,
            'contact' => $validatedData['contact'],
            'notes' => $validatedData['notes'] ?? null,
        ]);

        // création application
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

    public function update(Request $request, $id)
    {
        $application = Application::findOrFail($id);

        if ($application->user_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validatedData = $request->validate([
            // entreprise
            'name' => 'sometimes|string|max:191',
            'position' => 'sometimes|string|max:191',
            'location' => 'sometimes|string|max:191',
            'source' => 'sometimes|string|max:191',
            'contact' => 'sometimes|string',
            'notes' => 'sometimes|string',

            // candidature
            'type' => 'sometimes|in:Stage,Alternance,Emploi',
            'status' => 'sometimes|in:Candidature envoyée,En attente,Entretien,Refusée,Sans réponse',
        ]);

        // mise à jour entreprise
        $company = $application->company;
        $company->update(array_filter([
            'name' => $validatedData['name'],
            'position' => $validatedData['position'],
            'location' => $validatedData['location'] ?? null,
            'source' => $validatedData['source'] ?? null,
            'contact' => $validatedData['contact'],
            'notes' => $validatedData['notes'] ?? null,
        ]));

        // mise à jour candidature
        $application->update(array_filter([
            'type' => $validatedData['type'],
            'status' => $validatedData['status'],
        ]));

        return response()->json([
            'message' => 'Candidature mise à jour avec succès',
            'application' => $application,
        ], 200);
    }
}