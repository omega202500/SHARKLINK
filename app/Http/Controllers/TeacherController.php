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
     * Pages provisoires pour les autres sections (à développer ensuite)
     */
    public function grades(Request $request): View
    {
        $context = $this->getTeacherContext($request);
        return view('teacher.grades', compact('context'));
    }

    public function timetable(Request $request): View
    {
        $context = $this->getTeacherContext($request);
        return view('teacher.timetable', compact('context'));
    }

    public function attendance(Request $request): View
    {
        $context = $this->getTeacherContext($request);
        return view('teacher.attendance', compact('context'));
    }

    public function assignments(Request $request): View
    {
        $context = $this->getTeacherContext($request);
        return view('teacher.assignments', compact('context'));
    }

    public function announcements(Request $request): View
    {
        $context = $this->getTeacherContext($request);
        return view('teacher.announcements', compact('context'));
    }

    public function reports(Request $request): View
    {
        $context = $this->getTeacherContext($request);
        return view('teacher.reports', compact('context'));
    }
}