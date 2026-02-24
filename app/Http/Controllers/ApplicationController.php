<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ApplicationController extends Controller
{

    public function index()
    {
        $applications = Application::with('company')
            ->where('user_id', Auth::id())
            ->get();

        return response()->json($applications);
    }

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

        // création candidature
        $application = Application::create([
            'type' => $validatedData['type'],
            'status' => $validatedData['status'],
            'user_id' => Auth::id(),
            'company_id' => $company->id,
        ]);

        return response()->json([
            'message' => 'Candidature créée avec succès',
            'application' => $application,
            'company' => $company,
        ], 201);
    }

   public function update(Request $request, $id)
    {
        // Récupérer la candidature
        $application = Application::findOrFail($id);

        // Vérifier que l'utilisateur est propriétaire
        if ($application->user_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validatedData = $request->validate([
            // entreprise
            'name' => 'sometimes|string|max:191',
            'position' => 'sometimes|string|max:191',
            'location' => 'sometimes|string|max:191',
            'source' => 'sometimes|string|max:191',
            'contact' => 'sometimes|string|max:191',
            'notes' => 'sometimes|string|nullable',

            // candidature
            'type' => 'sometimes|in:Stage,Alternance,Emploi',
            'status' => 'sometimes|in:Candidature envoyée,En attente,Entretien,Refusée,Sans réponse',
        ]);

        // Mise à jour entreprise (uniquement les champs envoyés)
        $companyData = [];
        if ($application->company) { // vérifier qu'il y a une company liée
            foreach (['name','position','location','source','contact','notes'] as $field) {
                if (array_key_exists($field, $validatedData)) {
                    $companyData[$field] = $validatedData[$field];
                }
            }
            if (!empty($companyData)) {
                $application->company->update($companyData);
            }
        }

        // Mise à jour candidature (uniquement les champs envoyés)
        $applicationData = [];
        foreach (['type','status'] as $field) {
            if (array_key_exists($field, $validatedData)) {
                $applicationData[$field] = $validatedData[$field];
            }
        }
        if (!empty($applicationData)) {
            $application->update($applicationData);
        }

        return response()->json([
            'message' => 'Candidature mise à jour avec succès',
            'application' => $application->load('company'),
        ], 200);
    }

    public function destroy($id){
        
        $application = Application::findOrFail($id);

        if($application->user_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $application->delete();

        return response()->json(['message' => 'Candidature supprimée avec succès'], 200);
    }
}