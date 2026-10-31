<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\ApplicationDocument;
use App\Models\User;
use App\Models\EstablishmentMembership;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Mail\WelcomeStudentMail;

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



    /**
     * Convertir une candidature acceptée en étudiant inscrit
     */
    public function convertToStudent($id)
    {
        $application = Application::with(['program', 'level'])->findOrFail($id);

        // Sécurité : ne convertir que si le statut est ACCEPTE
        if ($application->status !== 'ACCEPTE') {
            return back()->with('error', 'Seules les candidatures acceptées peuvent être converties.');
        }

        // Vérifier si un utilisateur existe déjà avec cet email
        $existingUser = User::where('email', $application->email)->first();
        if ($existingUser) {
            return back()->with('error', 'Un compte utilisateur existe déjà avec cet email.');
        }

        // Récupérer l'établissement actif de l'admin
        $establishmentId = request()->session()->get('active_establishment_id');
        abort_unless($establishmentId, 403);

        // Générer un mot de passe temporaire
        $tempPassword = Str::random(10);

        // 1. Créer l'utilisateur
        $user = User::create([
            'first_name' => $application->first_name,
            'last_name' => $application->last_name,
            'email' => $application->email,
            'password' => Hash::make($tempPassword),
            'status' => 'ACTIF',
            'email_verified_at' => now(),
        ]);

        // 2. Créer le membership dans l'établissement
        $membership = \App\Models\EstablishmentMembership::create([
            'user_id' => $user->id,
            'establishment_id' => $establishmentId,
            'member_number' => $this->generateMatricule($establishmentId),
            'status' => 'ACTIF',
            'joined_at' => now()->toDateString(),
        ]);

        // 3. Créer le profil étudiant
        \App\Models\StudentProfile::create([
            'membership_id' => $membership->id,
            'birth_date' => $application->birth_date,
            'birth_place' => $application->birth_place,
            'gender' => $application->gender,
            'nationality' => $application->nationality,
            'address' => $application->address,
            'student_number' => $membership->member_number,
        ]);

        // 4. Créer l'inscription étudiant (student_enrollments)
        $studentEnrollment = \App\Models\StudentEnrollment::create([
            'student_membership_id' => $membership->id,
            'establishment_id' => $establishmentId,
            'enrollment_date' => now()->toDateString(),
            'status' => 'ACTIF',
        ]);

        // 5. Créer l'inscription au programme (program_enrollments)
        \App\Models\ProgramEnrollment::create([
            'student_enrollment_id' => $studentEnrollment->id,
            'program_id' => $application->program_id,
            'level_id' => $application->level_id,
            'cohort_id' => $this->getCurrentCohortId($establishmentId),
            'status' => 'ACTIF',
        ]);

        // 6. Mettre à jour le statut de la candidature
        $application->update([
            'status' => 'INSCRIT',
            'admin_comment' => ($application->admin_comment ?? '') . "\n[Converti le " . now()->format('d/m/Y H:i') . ']',
        ]);

        // 7. Envoyer l'email de bienvenue
        Mail::to($user->email)->send(new WelcomeStudentMail($user, $tempPassword, $membership->member_number));

        return back()->with('success', "✅ Étudiant créé avec succès ! Un email avec les identifiants a été envoyé à {$user->email}.");
    }

    /**
     * Générer un matricule unique (format: SHK-2026-XXX)
     */
    private function generateMatricule($establishmentId)
    {
        $year = date('Y');
        $prefix = 'SHK';
        $lastMember = \App\Models\EstablishmentMembership::where('establishment_id', $establishmentId)
            ->whereYear('created_at', $year)
            ->orderBy('id', 'desc')
            ->first();
        
        $nextNumber = $lastMember ? (intval(explode('-', $lastMember->member_number)[2] ?? 0) + 1) : 1;
        return "{$prefix}-{$year}-" . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
    }

    /**
     * Récupérer ou créer la cohorte actuelle
     */
    private function getCurrentCohortId($establishmentId)
    {
        $cohort = \App\Models\Cohort::where('establishment_id', $establishmentId)
            ->where('academic_year', date('Y'))
            ->first();
        
        if (!$cohort) {
            $cohort = \App\Models\Cohort::create([
                'establishment_id' => $establishmentId,
                'academic_year' => date('Y'),
                'name' => 'Cohorte ' . date('Y'),
                'status' => 'ACTIF',
            ]);
        }
        
        return $cohort->id;
    }
}