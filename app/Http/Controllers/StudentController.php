<?php

namespace App\Http\Controllers;

use App\Models\Establishment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class StudentController extends Controller
{
    /**
     * Vérifie l'accès de l'étudiant à l'établissement actif.
     */
    private function getStudentContext(Request $request): array
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

        $hasStudentRole = $membership->roles()
            ->where('code', 'ETUDIANT')
            ->exists();

        abort_unless($hasStudentRole, 403);

        return [
            'user' => $user,
            'establishment' => $establishment,
            'membership' => $membership,
        ];
    }
    
    public function grades(Request $request)
    {
        $user = $request->user();
        $establishmentId = $request->session()->get('active_establishment_id');
        abort_unless($user && $establishmentId, 403);

        $membership = DB::table('establishment_memberships')
            ->where('user_id', $user->id)
            ->where('establishment_id', $establishmentId)
            ->firstOrFail();

        // Récupérer toutes les notes publiées, groupées par cours
        $gradesHistory = DB::table('grades as g')
            ->join('assessments as a', 'g.assessment_id', '=', 'a.id')
            ->join('course_offerings as co', 'a.course_offering_id', '=', 'co.id')
            ->join('courses as c', 'co.course_id', '=', 'c.id')
            ->join('grade_publications as gp', 'co.id', '=', 'gp.course_offering_id')
            ->where('g.student_membership_id', $membership->id)
            ->where('gp.status', 'PUBLIE')
            ->select(
                'c.title as course_title',
                'c.code as course_code',
                'a.title as assessment_title',
                'g.score',
                'a.max_score',
                'gp.published_at'
            )
            ->orderBy('gp.published_at', 'desc')
            ->get()
            ->groupBy('course_title');

        return view('student.grades', compact('gradesHistory', 'user'));
    }

    /**
     * Pages provisoires pour les sections étudiant
     */
    public function profile(Request $request): View
    {
        $context = $this->getStudentContext($request);
        return view('student.profile', compact('context'));
    }

    public function courses(Request $request): View
    {
        $context = $this->getStudentContext($request);
        return view('student.courses', compact('context'));
    }



    public function timetable(Request $request): View
    {
        $context = $this->getStudentContext($request);
        return view('student.timetable', compact('context'));
    }

    public function enrollments(Request $request): View
    {
        $context = $this->getStudentContext($request);
        return view('student.enrollments', compact('context'));
    }

    public function finances(Request $request): View
    {
        $context = $this->getStudentContext($request);
        return view('student.finances', compact('context'));
    }

    public function announcements(Request $request): View
    {
        $context = $this->getStudentContext($request);
        return view('student.announcements', compact('context'));
    }
}