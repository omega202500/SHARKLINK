<?php

namespace App\Http\Controllers;

use App\Models\Establishment;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TranscriptController extends Controller
{
    /**
     * Génère le relevé de notes PDF pour l'étudiant connecté
     */
    public function generate(Request $request)
    {
        $user = $request->user();
        $establishmentId = $request->session()->get('active_establishment_id');

        abort_unless($user && $establishmentId, 403);

        // 1. Récupérer l'établissement
        $establishment = Establishment::whereKey($establishmentId)
            ->where('status', 'ACTIF')
            ->firstOrFail();

        // 2. Récupérer le membership et le profil étudiant
        $membership = DB::table('establishment_memberships')
            ->where('user_id', $user->id)
            ->where('establishment_id', $establishmentId)
            ->where('status', 'ACTIF')
            ->firstOrFail();

        // 3. Le profil est lié au membership, et non directement à l'utilisateur.
        $studentProfile = DB::table('student_profiles')
            ->where('membership_id', $membership->id)
            ->first();

        $studentProfile = $studentProfile ?: (object) [
            'date_of_birth' => null,
            'gender' => 'M',
        ];

           // 5. Récupérer l'inscription active (Programme, Niveau, etc.)
        $enrollment = DB::table('program_enrollments as pe')
            ->join('student_enrollments as se', 'pe.student_enrollment_id', '=', 'se.id')
            ->join('programs as p', 'pe.program_id', '=', 'p.id')
            ->join('levels as l', 'pe.level_id', '=', 'l.id')
            ->join('departments as d', 'p.department_id', '=', 'd.id')
            ->join('faculties as f', 'd.faculty_id', '=', 'f.id')
            ->where('se.student_membership_id', $membership->id) 
            ->where('pe.status', 'ACTIF')
            ->select(
                'p.name as program_name',
                'l.name as level_name',
                'd.name as department_name',
                'f.name as faculty_name',
                'pe.cohort_id'
            )
            ->first();

        
        if (!$enrollment) {
            abort(404, 'Aucune inscription active trouvée pour cet étudiant.');
        }

        abort_unless($enrollment, 404, 'Aucune inscription active trouvée.');

        // 6. Récupérer les notes PUBLIÉES et DÉLIBÉRÉES de l'étudiant
        $grades = DB::table('grades as g')
            ->join('assessments as a', 'g.assessment_id', '=', 'a.id')
            ->join('course_offerings as co', 'a.course_offering_id', '=', 'co.id')
            ->join('courses as c', 'co.course_id', '=', 'c.id')
            ->join('grade_publications as gp', 'co.id', '=', 'gp.course_offering_id')
            ->where('g.student_membership_id', $membership->id)
            ->where('gp.status', 'PUBLIE')
            ->select(
                'c.code as course_code',
                'c.title as course_title',
                'a.title as assessment_title',
                'g.score',
                'a.max_score',
                'a.coefficient',
                'gp.published_at'
            )
            ->orderBy('c.code')
            ->orderBy('a.title')
            ->get()
            ->map(function ($item) {
                $maxScore = (float) $item->max_score;
                $item->score_5 = $maxScore > 0 ? ($item->score / $maxScore) * 5 : 0;
                $item->grade_letter = $this->getGradeLetter($item->score);
                $item->mention = $this->getMention($item->score);
                $item->decision = $item->score >= 10 ? 'ADMIS' : 'AJOURNÉ';
                return $item;
            });

        // 8. Calculer la moyenne générale
        $general_average = $grades->avg('score') ?? 0;
        $generalAverage5 = ($general_average / 20) * 5;
        $totalCredits = $grades->sum('coefficient');
        $generalMention = $this->getMention($general_average);

        // 9. Données pour la vue
        $data = [
            'establishment' => $establishment,
            'student' => [
                'full_name' => strtoupper($user->first_name . ' ' . $user->last_name),
                'birth_date' => $studentProfile->date_of_birth
                    ? \Carbon\Carbon::parse($studentProfile->date_of_birth)->format('d/m/Y')
                    : 'Non renseigné',
                'birth_place' => 'Non renseigné',
                'gender' => ($studentProfile->gender ?? 'M') === 'M' ? 'M' : 'F',
                'matricule' => $membership->member_number,
                'program' => $enrollment->program_name,
                'level' => $enrollment->level_name,
                'department' => $enrollment->department_name,
                'faculty' => $enrollment->faculty_name,
            ],
            'academic_year' => '2026-2027',
            'semester' => '1+2',
            'grades' => $grades,
            'general_average' => $general_average,
            'generalAverage' => $general_average,
            'generalAverage_5' => $generalAverage5,
            'totalCredits' => $totalCredits,
            'generalMention' => $generalMention,
            'decision' => $general_average >= 10 ? 'ADMIS' : 'AJOURNÉ',
            'generated_at' => now()->format('d/m/Y'),
        ];

        // 10. Générer le PDF
        $pdf = Pdf::loadView('pdf.transcript', $data)
            ->setPaper('a4', 'portrait')
            ->setOptions([
                'dpi' => 150,
                'defaultFont' => 'times',
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => true,
            ]);

        $filename = 'Releve_Notes_' . str_replace(' ', '_', $user->last_name) . '_' . date('Y') . '.pdf';

        return $pdf->download($filename);
    }

    /**
     * Attribue une grade lettre selon le barème MINESUP
     */
    private function getGradeLetter($score)
    {
        if ($score >= 16) return 'A';
        if ($score >= 14) return 'B+';
        if ($score >= 12) return 'B';
        if ($score >= 10) return 'C+';
        if ($score >= 8)  return 'C';
        return 'D';
    }

    /**
     * Attribue une mention selon le barème MINESUP
     */
    private function getMention($average)
    {
        if ($average >= 16) return 'Très Bien';
        if ($average >= 14) return 'Bien';
        if ($average >= 12) return 'Assez Bien';
        if ($average >= 10) return 'Passable';
        if ($average >= 8)  return 'Insuffisant';
        return 'Très Insuffisant';
    }
}