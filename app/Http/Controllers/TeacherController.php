<?php

namespace App\Http\Controllers;

use App\Models\Establishment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class TeacherController extends Controller
{
    /**
     * Vérifie l'accès du professeur à l'établissement actif.
     */
    private function getTeacherContext(Request $request): array
    {
        $user = $request->user();
        $establishmentId = $request->session()->get('active_establishment_id');

        abort_unless($user && $establishmentId, 403);

        $establishment = Establishment::query()
            ->whereKey($establishmentId)
            ->where('status', 'ACTIF')
            ->firstOrFail();

        $membership = $user->memberships()
            ->where('establishment_id', $establishment->id)
            ->where('status', 'ACTIF')
            ->firstOrFail();

        $hasTeacherRole = $membership->roles()
            ->where('code', 'ENSEIGNANT')
            ->exists();

        abort_unless($hasTeacherRole, 403);

        return [
            'user' => $user,
            'establishment' => $establishment,
            'membership' => $membership,
        ];
    }

    /**
     * Affiche les cours affectés au professeur.
     */
    public function courses(Request $request): View
    {
        $context = $this->getTeacherContext($request);

        $courses = DB::table('teacher_assignments as ta')
            ->join('course_offerings as co', 'ta.course_offering_id', '=', 'co.id')
            ->join('courses as c', 'co.course_id', '=', 'c.id')
            ->where('ta.teacher_membership_id', $context['membership']->id)
            ->where('co.establishment_id', $context['establishment']->id)
            ->select('co.id as offering_id', 'c.code as course_code', 'c.title as course_title', 'co.term')
            ->distinct()
            ->orderBy('c.title')
            ->get();

        return view('teacher.courses', [
            'user' => $context['user'],
            'establishment' => $context['establishment'],
            'courses' => $courses,
        ]);
    }

       /**
     * Page de saisie des notes
     */
    public function grades(Request $request): View
    {
        $context = $this->getTeacherContext($request);
        $membershipId = $context['membership']->id;
        $establishmentId = $context['establishment']->id;

        // 1. Récupérer les cours (offerings) assignés à cet enseignant
        $offerings = DB::table('teacher_assignments as ta')
            ->join('course_offerings as co', 'ta.course_offering_id', '=', 'co.id')
            ->join('courses as c', 'co.course_id', '=', 'c.id')
            ->where('ta.teacher_membership_id', $membershipId)
            ->where('co.establishment_id', $establishmentId)
            ->select('co.id', 'c.title', 'co.term', 'co.cohort_id')
            ->orderBy('c.title')
            ->get();

        // 2. Sélection du cours (par défaut le premier)
        $selectedOfferingId = $request->get('offering_id', $offerings->first()->id ?? null);
        $selectedOffering = $offerings->firstWhere('id', $selectedOfferingId);

        $students = collect();
        $assessments = collect();

        if ($selectedOffering) {
            // 3. Récupérer les étudiants inscrits dans la cohorte de ce cours
            $students = DB::table('program_enrollments as pe')
                ->join('student_enrollments as se', 'pe.student_enrollment_id', '=', 'se.id')
                ->join('establishment_memberships as em', 'se.student_membership_id', '=', 'em.id')
                ->join('users as u', 'em.user_id', '=', 'u.id')
                ->where('pe.cohort_id', $selectedOffering->cohort_id)
                ->where('se.establishment_id', $establishmentId)
                ->where('pe.status', 'ACTIF')
                ->select('em.id as membership_id', 'u.first_name', 'u.last_name', 'em.member_number')
                ->orderBy('u.last_name')
                ->get();

            // 4. Récupérer les évaluations (Assessments) pour ce cours
            $assessments = DB::table('assessments')
                ->where('course_offering_id', $selectedOfferingId)
                ->where('status', 'PUBLIE')
                ->orderBy('assessment_date', 'desc')
                ->get();
                
        }
        
        // Charger les notes déjà saisies pour cette évaluation (si sélectionnée)
        $existingGrades = collect();
        $selectedAssessmentId = $request->integer('assessment_id');
        if ($selectedOffering && $selectedAssessmentId) {
            $assessmentBelongsToOffering = $assessments
                ->contains('id', $selectedAssessmentId);

            if ($assessmentBelongsToOffering) {
                $existingGrades = DB::table('grades')
                    ->where('assessment_id', $selectedAssessmentId)
                    ->pluck('score', 'student_membership_id');
            } else {
                $selectedAssessmentId = null;
            }
        }

        return view('teacher.grades', compact(
            'context', 'offerings', 'selectedOffering', 'students', 'assessments', 'existingGrades', 'selectedAssessmentId'
        ));
    }

       /**
     * Enregistrer les notes saisies par l'enseignant
     */
    public function storeGrades(Request $request)
    {
        // Validation stricte des données entrantes
        $validated = $request->validate([
            'offering_id' => 'required|exists:course_offerings,id',
            'assessment_id' => 'required|exists:assessments,id',
            'grades' => 'required|array',
            'grades.*' => 'nullable|numeric|min:0|max:20',
        ]);
       
        $context = $this->getTeacherContext($request);
        $membershipId = $context['membership']->id;

        // Vérification de sécurité : L'enseignant est-il bien assigné à ce cours ?
        $isAssigned = DB::table('teacher_assignments')
            ->where('teacher_membership_id', $membershipId)
            ->where('course_offering_id', $validated['offering_id'])
            ->exists();

        abort_unless($isAssigned, 403, "Vous n'êtes pas autorisé à saisir des notes pour ce cours.");

        $assessmentBelongsToOffering = DB::table('assessments')
            ->where('id', $validated['assessment_id'])
            ->where('course_offering_id', $validated['offering_id'])
            ->where('status', 'PUBLIE')
            ->exists();

        abort_unless($assessmentBelongsToOffering, 404, "Cette évaluation n'appartient pas au cours sélectionné.");

        // Traitement de chaque note
        $savedCount = 0;
        foreach ($validated['grades'] as $studentMembershipId => $score) {
            if (!is_null($score) && $score !== '') {
                DB::table('grades')->updateOrInsert(
                    [
                        'assessment_id' => $validated['assessment_id'],
                        'student_membership_id' => $studentMembershipId,
                    ],
                    [
                        'score'       => $score,
                        'status'      => 'VALIDEE', // Ou 'BROUILLON' selon ton workflow
                        'entered_by'  => $membershipId,
                        'validated_at'=> now(),
                    ]
                );
                $savedCount++;
            }
        }

        return redirect()
            ->route('teacher.grades', [
                'offering_id' => $validated['offering_id'],
                'assessment_id' => $validated['assessment_id'],
            ])
            ->with('success', "{$savedCount} note(s) enregistrée(s) avec succès.");
    }

    /**
     * Publier les notes d'une évaluation (Rendre visibles aux étudiants)
     */
    public function publishGrades(Request $request)
    {
        $validated = $request->validate([
            'offering_id' => 'required|exists:course_offerings,id',
        ]);

        $context = $this->getTeacherContext($request);
        $membershipId = $context['membership']->id;

        // Sécurité : l'enseignant doit être assigné à ce cours
        $isAssigned = DB::table('teacher_assignments')
            ->where('teacher_membership_id', $membershipId)
            ->where('course_offering_id', $validated['offering_id'])
            ->exists();

        abort_unless($isAssigned, 403, "Vous n'êtes pas autorisé à publier les notes de ce cours.");

        // Déjà publié pour ce cours (peu importe qui a publié)
        $alreadyPublished = DB::table('grade_publications')
            ->where('course_offering_id', $validated['offering_id'])
            ->where('status', 'PUBLIE')
            ->exists();

        if ($alreadyPublished) {
            return back()->with('success', 'Les notes de ce cours sont déjà publiées.');
        }

        // Rien à publier si aucune note n'a été saisie
        $hasGrades = DB::table('grades as g')
            ->join('assessments as a', 'g.assessment_id', '=', 'a.id')
            ->where('a.course_offering_id', $validated['offering_id'])
            ->whereNotNull('g.score')
            ->exists();

        if (!$hasGrades) {
            return back()->withErrors("Aucune note saisie pour ce cours : rien à publier.");
        }

        DB::table('grade_publications')->insert([
            'course_offering_id' => $validated['offering_id'],
            'published_by'       => $membershipId,
            'published_at'       => now(),
            'status'             => 'PUBLIE',
        ]);

        return back()->with('success', 'Les notes de ce cours ont été publiées aux étudiants.');
    }

    /**
     * Page Emploi du Temps (Provisoire)
     */
    public function timetable(Request $request): View
    {
        $context = $this->getTeacherContext($request);
        return view('teacher.timetable', compact('context'));
    }

    /**
     * Page Présence (Provisoire)
     */
    public function attendance(Request $request): View
    {
        $context = $this->getTeacherContext($request);
        return view('teacher.attendance', compact('context'));
    }

    /**
     * Page Devoirs (Provisoire)
     */
    public function assignments(Request $request): View
    {
        $context = $this->getTeacherContext($request);
        return view('teacher.assignments', compact('context'));
    }

    /**
     * Page Annonces (Provisoire)
     */
    public function announcements(Request $request): View
    {
        $context = $this->getTeacherContext($request);
        return view('teacher.announcements', compact('context'));
    }

    /**
     * Page Rapports (Provisoire)
     */
    public function reports(Request $request): View
    {
        $context = $this->getTeacherContext($request);
        return view('teacher.reports', compact('context'));
    }
}