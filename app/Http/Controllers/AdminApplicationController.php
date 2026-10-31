<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\ApplicationDocument;
use App\Models\User;
use App\Models\EstablishmentMembership;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class AdminApplicationController extends Controller
{
    /**
     * Liste des candidatures avec filtres
     */
    public function index(Request $request)
    {
        $query = Application::with(['program', 'level'])
            ->orderBy('created_at', 'desc');

        // Filtres
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('first_name', 'like', "%{$request->search}%")
                  ->orWhere('last_name', 'like', "%{$request->search}%")
                  ->orWhere('email', 'like', "%{$request->search}%");
            });
        }

        $applications = $query->paginate(20);
        
        // Stats rapides
        $stats = [
            'total' => Application::count(),
            'pending' => Application::where('status', 'SOUMIS')->count(),
            'accepted' => Application::where('status', 'ACCEPTE')->count(),
            'rejected' => Application::where('status', 'REJETE')->count(),
        ];

        return view('admin.applications.index', compact('applications', 'stats'));
    }

    /**
     * Détails d'une candidature
     */
    public function show($id)
    {
        $application = Application::with(['program', 'level', 'documents'])->findOrFail($id);
        $generated_at = now()->format('d/m/Y');

        return view('admin.applications.show', compact('application', 'generated_at'));
    }

    /**
     * Changer le statut d'une candidature
     */
    public function updateStatus(Request $request, $id)
    {
        $validated = $request->validate([
            'status' => 'required|in:BROUILLON,SOUMIS,EN_COURS,ACCEPTE,REJETE,INSCRIT',
            'admin_comment' => 'nullable|string|max:500',
        ]);

        $application = Application::findOrFail($id);
        $application->update($validated);

        // TODO: Envoyer un email au candidat si statut changé

        return back()->with('success', 'Statut mis à jour avec succès.');
    }

    /**
     * Générer une lettre d'admission PDF
     */
    public function admissionLetter($id)
    {
        $application = Application::with(['program', 'level'])->findOrFail($id);
        
        $data = [
            'application' => $application,
            'generated_at' => now()->format('d/m/Y'),
        ];

        $pdf = Pdf::loadView('admin.applications.show', $data)
            ->setPaper('a4', 'portrait');

        return $pdf->download('Lettre_Admission_' . $application->last_name . '.pdf');
    }
}