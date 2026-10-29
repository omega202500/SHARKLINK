<?php

namespace App\Http\Controllers;

use App\Models\Establishment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DeliberationController extends Controller
{
    /**
     * Vérifie l'accès du responsable pédagogique à l'établissement actif
     */
    private function getPedagogicalContext(Request $request): array
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

        // Vérifie si l'utilisateur a un rôle pédagogique ou administratif
        $hasRole = $membership->roles()
            ->whereIn('code', ['RESPONSABLE_PEDAGOGIQUE', 'ADMIN_ETABLISSEMENT', 'DIRECTEUR'])
            ->exists();

        abort_unless($hasRole, 403, "Accès réservé aux responsables pédagogiques.");

        return [
            'user' => $user,
            'establishment' => $establishment,
            'membership' => $membership,
        ];
    }
    /**
     * Tableau de bord principal de l'Espace Pédagogique
     */
    public function dashboard(Request $request): View
    {
        $context = $this->getPedagogicalContext($request);
        $establishmentId = $context['establishment']->id;

        // 1. Récupérer les KPIs (Indicateurs Clés)
        $totalCourses = DB::table('course_offerings')->where('establishment_id', $establishmentId)->count();
        $publishedCourses = DB::table('grade_publications as gp')
            ->join('course_offerings as co', 'co.id', '=', 'gp.course_offering_id')
            ->where('co.establishment_id', $establishmentId)
            ->distinct()
            ->count('gp.course_offering_id');
        
        // Moyenne globale (simplifiée)
        $globalAverage = DB::table('grades')->avg('score') ?? 0;
        
        $kpis = [
            ['icon' => 'trending-up',   'label' => 'Taux de réussite',   'value' => '82%',     'helper' => '+4,2% ce mois'], // À calculer précisément plus tard
            ['icon' => 'book-open-check','label' => 'Cours à délibérer', 'value' => str_pad($totalCourses - $publishedCourses, 2, '0', STR_PAD_LEFT), 'helper' => $totalCourses . ' au total'],
            ['icon' => 'sigma',         'label' => 'Moyenne globale',    'value' => number_format($globalAverage, 1) . '/20', 'helper' => 'Semestre en cours'],
            ['icon' => 'circle-alert',  'label' => 'Alertes critiques',  'value' => '03',      'helper' => 'À traiter'],
        ];

        // 2. Récupérer la liste des cours et leur statut
        $coursesData = DB::table('course_offerings as co')
            ->join('courses as c', 'co.course_id', '=', 'c.id')
            ->leftJoin('teacher_assignments as ta', 'co.id', '=', 'ta.course_offering_id')
            ->leftJoin('establishment_memberships as em', 'ta.teacher_membership_id', '=', 'em.id')
            ->leftJoin('users as u', 'em.user_id', '=', 'u.id')
            ->leftJoin('grade_publications as gp', 'co.id', '=', 'gp.course_offering_id')
            ->where('co.establishment_id', $establishmentId)
            ->select(
                'c.title as name', 
                'c.code', 
                DB::raw("CONCAT(u.first_name, ' ', u.last_name) as teacher"), 
                DB::raw("CONCAT(SUBSTRING(u.first_name, 1, 1), SUBSTRING(u.last_name, 1, 1)) as initials"), 
                'gp.published_at as date'
            )
            ->orderBy('gp.published_at', 'desc')
            ->limit(5)
            ->get()
            ->map(function($item) {
                // Déterminer le statut
                if ($item->date) {
                    // Pour l'instant, on considère "Publié" si une date existe. 
                    // Tu pourras ajouter une vérification 'Délibéré' plus tard via la table deliberation_results
                    $item->status = 'Publié'; 
                } else {
                    $item->status = 'En attente';
                    $item->date = '-';
                }
                $item->code = $item->code ?: 'CODE · L1'; // Fallback si null
                return $item;
            });

        // 3. Calendrier (Peut être rendu dynamique plus tard via une table 'events')
        $calendar = [
            ['day' => '15', 'month' => 'OCT', 'title' => 'Fin de saisie des notes L1', 'meta' => 'Toutes les filières'],
            ['day' => '20', 'month' => 'OCT', 'title' => 'Conseil de classe Info',     'meta' => 'Salle du conseil · 09:00'],
            ['day' => '24', 'month' => 'OCT', 'title' => 'Jury de délibération M1',    'meta' => 'Amphithéâtre A · 14:30'],
        ];

        // 4. Performance par filière (Programmes)
        $programsData = DB::table('programs as p')
            ->leftJoin('program_enrollments as pe', 'p.id', '=', 'pe.program_id')
            ->where('p.establishment_id', $establishmentId)
            ->select('p.name', DB::raw('COUNT(pe.id) as students'))
            ->groupBy('p.id', 'p.name')
            ->get()
            ->map(function($item) {
                // Simulation d'un taux d'admission pour l'exemple (à remplacer par un vrai calcul)
                $item->value = rand(75, 95); 
                return $item;
            });

        return view('pedagogy.dashboard', [
            'currentUser' => [
                'name' => $context['user']->first_name . ' ' . $context['user']->last_name,
                'first_name' => $context['user']->first_name,
                'initials' => strtoupper(substr($context['user']->first_name, 0, 1) . substr($context['user']->last_name, 0, 1)),
                'role' => 'Resp. Pédagogique'
            ],
            'activeItem' => 'Tableau de bord',
            'academicYear' => '2026-2027',
            'semester' => 'Semestre 1 · 2026-2027',
            'kpis' => $kpis,
            'courses' => $coursesData,
            'calendar' => $calendar,
            'programs' => $programsData,
            'alertCount' => 3,
            'systemAlert' => [
                'title' => 'Retard de saisie détecté',
                'text' => '3 enseignants n’ont pas finalisé la saisie des notes.',
            ]
        ]);
    }

    /**
     * Page principale de délibération
     */
    public function index(Request $request): View
    {
        $context = $this->getPedagogicalContext($request);
        
        // Récupérer les offres de cours avec leurs publications
        $offerings = DB::table('course_offerings as co')
            ->join('courses as c', 'co.course_id', '=', 'c.id')
            ->leftJoin('grade_publications as gp', 'co.id', '=', 'gp.course_offering_id')
            ->where('co.establishment_id', $context['establishment']->id)
            ->select(
                'co.id', 
                'c.title', 
                'co.term',
                'gp.published_at',
                DB::raw('COUNT(DISTINCT g.student_membership_id) as student_count')
            )
            ->leftJoin('grades as g', function($join) {
                $join->on('g.assessment_id', '=', DB::raw('(SELECT id FROM assessments WHERE course_offering_id = co.id LIMIT 1)'));
            })
            ->groupBy('co.id', 'c.title', 'co.term', 'gp.published_at')
            ->orderBy('gp.published_at', 'desc')
            ->get();

        return view('deliberation.index', compact('context', 'offerings'));
    }

    /**
     * Calculer et afficher les résultats de délibération pour un cours
     */
    public function calculate(Request $request): View
    {
        $context = $this->getPedagogicalContext($request);
        $validated = $request->validate(['offering_id' => 'required|exists:course_offerings,id']);
        
        $offeringId = $validated['offering_id'];

        // Récupérer tous les étudiants et leurs notes pour ce cours
        $results = DB::table('program_enrollments as pe')
            ->join('student_enrollments as se', 'pe.student_enrollment_id', '=', 'se.id')
            ->join('establishment_memberships as em', 'se.student_membership_id', '=', 'em.id')
            ->join('users as u', 'em.user_id', '=', 'u.id')
            ->leftJoin('grades as g', function($join) use ($offeringId) {
                $join->on('g.student_membership_id', '=', 'em.id')
                     ->whereIn('g.assessment_id', function($q) use ($offeringId) {
                         $q->select('id')->from('assessments')->where('course_offering_id', $offeringId);
                     });
            })
            ->where('pe.cohort_id', function($q) use ($offeringId) {
                $q->select('cohort_id')->from('course_offerings')->where('id', $offeringId);
            })
            ->where('pe.status', 'ACTIF')
            ->select(
                'em.id as membership_id',
                'u.first_name',
                'u.last_name',
                'em.member_number',
                DB::raw('AVG(g.score) as average'),
                DB::raw('COUNT(g.id) as graded_count'),
                DB::raw('(SELECT COUNT(*) FROM assessments WHERE course_offering_id = ?) as total_assessments', [$offeringId])
            )
            ->groupBy('em.id', 'u.first_name', 'u.last_name', 'em.member_number')
            ->orderBy('average', 'desc')
            ->get()
            ->map(function($student) {
                // Calcul de la mention selon le barème standard
                $avg = $student->average ?? 0;
                $mention = '';
                $color = '';
                
                if ($avg >= 16) { $mention = 'Très Bien'; $color = 'text-green-700 bg-green-100'; }
                elseif ($avg >= 14) { $mention = 'Bien'; $color = 'text-blue-700 bg-blue-100'; }
                elseif ($avg >= 12) { $mention = 'Assez Bien'; $color = 'text-cyan-700 bg-cyan-100'; }
                elseif ($avg >= 10) { $mention = 'Passable'; $color = 'text-yellow-700 bg-yellow-100'; }
                elseif ($avg >= 8) { $mention = 'Insuffisant'; $color = 'text-orange-700 bg-orange-100'; }
                else { $mention = 'Échec'; $color = 'text-red-700 bg-red-100'; }

                $student->mention = $mention;
                $student->mention_color = $color;
                $student->decision = $avg >= 10 ? 'ADMIS' : 'AJOURNÉ';
                $student->decision_color = $avg >= 10 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800';
                
                return $student;
            });

        // Statistiques globales
        $stats = [
            'total_students' => $results->count(),
            'admitted' => $results->where('decision', 'ADMIS')->count(),
            'failed' => $results->where('decision', 'AJOURNÉ')->count(),
            'class_average' => $results->avg('average') ?? 0,
        ];

        return view('deliberation.results', compact('context', 'results', 'stats', 'offeringId'));
    }

    /**
     * Enregistrer officiellement les décisions de délibération
     */
    public function store(Request $request)
    {
        $context = $this->getPedagogicalContext($request);
        $validated = $request->validate([
            'offering_id' => 'required|exists:course_offerings,id',
            'decisions' => 'required|array',
        ]);

        foreach ($validated['decisions'] as $membershipId => $decision) {
            DB::table('deliberation_results')->updateOrInsert(
                [
                    'course_offering_id' => $validated['offering_id'],
                    'student_membership_id' => $membershipId,
                ],
                [
                    'decision' => $decision['status'], // ADMIS ou AJOURNÉ
                    'average_score' => $decision['average'],
                    'mention' => $decision['mention'],
                    'deliberated_by' => $context['membership']->id,
                    'deliberated_at' => now(),
                ]
            );
        }

        return back()->with('success', 'Délibération enregistrée avec succès ! Les relevés sont maintenant disponibles.');
    }
}