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

        return view('teacher.grades', compact(
            'context', 'offerings', 'selectedOffering', 'students', 'assessments'
        ));
    }

    /**
     * Enregistrer les notes (Placeholder pour la prochaine étape)
     */
    public function storeGrades(Request $request)
    {
        // Nous implémenterons la logique de sauvegarde dans la prochaine étape
        return back()->with('success', 'Notes enregistrées avec succès ! (Fonctionnalité en cours de développement)');
    }
}