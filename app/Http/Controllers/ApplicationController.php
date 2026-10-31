<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\ApplicationDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ApplicationController extends Controller
{
    /**
     * Afficher le formulaire de candidature
     */
    public function create()
    {
        $programs = DB::table('programs')->get();
        $levels = DB::table('levels')->get();
        
        return view('public.apply', compact('programs', 'levels'));
    }

    /**
     * Enregistrer la candidature
     */
    public function store(Request $request)
    {
        // 1. Validation stricte
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:applications,email',
            'phone' => 'required|string|max:20',
            'birth_date' => 'required|date',
            'birth_place' => 'required|string|max:255',
            'gender' => 'required|in:M,F',
            'previous_school' => 'required|string|max:255',
            'previous_diploma' => 'required|string|max:255',
            'graduation_year' => 'required|integer|min:1990|max:2030',
            'program_id' => 'required|exists:programs,id',
            'level_id' => 'required|exists:levels,id',
            'documents.*' => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048', // Max 2MB par fichier
        ]);

        // 2. Créer le dossier de candidature
        $application = Application::create([
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'birth_date' => $validated['birth_date'],
            'birth_place' => $validated['birth_place'],
            'gender' => $validated['gender'],
            'previous_school' => $validated['previous_school'],
            'previous_diploma' => $validated['previous_diploma'],
            'graduation_year' => $validated['graduation_year'],
            'program_id' => $validated['program_id'],
            'level_id' => $validated['level_id'],
            'status' => 'SOUMIS',
        ]);

        // 3. Gérer l'upload des documents
        if ($request->hasFile('documents')) {
            foreach ($request->file('documents') as $type => $file) {
                // Ex: $type = 'bac', $file = le fichier uploadé
                $path = $file->store('applications/' . $application->id, 'public');
                
                ApplicationDocument::create([
                    'application_id' => $application->id,
                    'document_type' => $type,
                    'file_path' => $path,
                ]);
            }
        }

        return redirect()->route('apply.success', ['id' => $application->id]);
    }

    /**
     * Page de succès après soumission
     */
    public function success($id)
    {
        $application = Application::findOrFail($id);
        return view('public.apply-success', compact('application'));
    }
}