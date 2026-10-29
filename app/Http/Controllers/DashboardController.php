<?php

namespace App\Http\Controllers;

use App\Models\Establishment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class DashboardController extends Controller
{
    private function dashboardTablesAvailable(): bool
    {
        $tables = [
            'academic_years',
            'notifications',
            'student_enrollments',
            'course_offerings',
            'establishment_memberships',
            'membership_roles',
            'roles',
            'payments',
            'invoices',
            'attendance_records',
            'attendance_sessions',
            'grades',
            'assessments',
            'grade_publications',
            'teacher_assignments',
            'assignment_submissions',
            'timetable_entries',
            'classrooms',
            'announcements',
            'program_enrollments',
            'programs',
            'levels',
            'courses',
        ];

        foreach ($tables as $table) {
            if (! Schema::hasTable($table)) {
                return false;
            }
        }

        return true;
    }

    public function index(Request $request): View|RedirectResponse
    {
        $user = $request->user();
        $establishmentId = session('active_establishment_id');

        if (! $establishmentId) {
            return redirect()->route('establishment.select');
        }

        $establishment = Establishment::find($establishmentId);
        if (! $establishment || $establishment->status !== 'ACTIF') {
            session()->forget('active_establishment_id');
            return redirect()->route('establishment.select')->withErrors(['establishment' => 'Établissement invalide ou inactif.']);
        }

        $membership = $user->memberships()->where('establishment_id', $establishment->id)->first();
        $roles = $membership ? $membership->roles()->pluck('code')->toArray() : [];

        if (! $this->dashboardTablesAvailable()) {
            $profile = [
                'Nom' => trim(($user->first_name ?? '') . ' ' . ($user->last_name ?? '')) ?: 'Utilisateur',
                'Matricule' => $membership ? ($membership->member_number ?: 'N/A') : 'N/A',
                'Filière' => 'Non définie',
                'Année académique' => 'Non définie',
                'Statut' => $membership ? ucfirst(strtolower($membership->status)) : 'Inactif',
            ];

            $navigation = [
                ['type' => 'heading', 'title' => 'Espace étudiant', 'action' => ''],
                ['type' => 'link', 'label' => 'Tableau de bord', 'icon' => 'layout-dashboard', 'href' => route('dashboard'), 'active' => request()->routeIs('dashboard')],
                ['type' => 'link', 'label' => 'Mon profil', 'icon' => 'user-round', 'href' => route('student.profile')],
            ];

            $quickLinks = ['Dossier académique', 'Inscriptions', 'Compte financier', 'Emploi du temps'];

            return view('dashboard', [
                'navigation' => $navigation,
                'quickLinks' => $quickLinks,
                'profile' => $profile,
                'courses' => [['name' => 'Aucun cours assigné pour le moment', 'term' => '-']],
                'news' => [['image' => 'nsu-campus.png', 'title' => 'Bienvenue sur ' . $establishment->name, 'date' => 'Données académiques non disponibles']],
                'studentNotes' => collect(),
                'establishment' => $establishment,
            ]);
        }

        // =====================================================================
        // 1. DONNÉES COMMUNES
        // =====================================================================
        $roleLabel = 'Utilisateur';
        if (in_array('ADMIN_PLATEFORME', $roles) || in_array('DIRECTEUR', $roles)) $roleLabel = 'Administrateur';
        elseif (in_array('ENSEIGNANT', $roles)) $roleLabel = 'Enseignant';
        elseif (in_array('ETUDIANT', $roles)) $roleLabel = 'Étudiant';

        $annee = DB::table('academic_years')->where('establishment_id', $establishment->id)->where('is_current', true)->value('name') ?? '2026-2027';

        // =====================================================================
        // 2. DONNÉES POUR DASHBOARD ADMIN (Stats, Frais, Présences, Examens)
        // =====================================================================
        $notifications = DB::table('notifications')->where('user_id', $user->id)->whereNull('read_at')->count();

        $totalEtudiants = DB::table('student_enrollments')->where('establishment_id', $establishment->id)->where('status', 'ACTIF')->count();
        $totalCours = DB::table('course_offerings')->where('establishment_id', $establishment->id)->where('status', 'ACTIF')->count();
        
        $totalFaculte = DB::table('establishment_memberships as em')
            ->join('membership_roles as mr', 'em.id', '=', 'mr.membership_id')
            ->join('roles as r', 'mr.role_id', '=', 'r.id')
            ->where('em.establishment_id', $establishment->id)
            ->where('r.code', 'ENSEIGNANT')
            ->count();

        $revenus = DB::table('payments')->where('establishment_id', $establishment->id)->whereIn('status', ['REUSSI', 'VALIDE', 'CONFIRME'])->sum('amount') ?? 0;

        $stats = [
            ['label' => 'Total Étudiants', 'valeur' => $totalEtudiants, 'icone' => 'graduation-cap', 'evolution' => '↑6.3%', 'periode' => 'vs. l’année précédente'],
            ['label' => 'Total Cours', 'valeur' => $totalCours, 'icone' => 'book-open', 'periode' => 'Tous les programmes'],
            ['label' => 'Membres Faculté', 'valeur' => $totalFaculte, 'icone' => 'users', 'periode' => 'Enseignants & chercheurs'],
            ['label' => 'Revenus', 'valeur' => $revenus, 'unite' => 'FCFA', 'icone' => 'wallet', 'evolution' => '↑8.6%', 'periode' => 'vs. le mois précédent'],
        ];

        $totalFactures = DB::table('invoices')->where('establishment_id', $establishment->id)->count();
        $facturesPayees = DB::table('invoices')->where('establishment_id', $establishment->id)->where('status', 'PAYEE')->count();
        $facturesAttente = DB::table('invoices')->where('establishment_id', $establishment->id)->where('status', 'EN_ATTENTE')->count();
        $facturesRetard = DB::table('invoices')->where('establishment_id', $establishment->id)->where('status', 'EN_RETARD')->count();

        $frais = [
            'collecte' => $totalFactures > 0 ? round(($facturesPayees / $totalFactures) * 100) : 0,
            'attente'  => $totalFactures > 0 ? round(($facturesAttente / $totalFactures) * 100) : 0,
            'retard'   => $totalFactures > 0 ? round(($facturesRetard / $totalFactures) * 100) : 0,
        ];
        if (($frais['collecte'] + $frais['attente'] + $frais['retard']) < 100 && $totalFactures > 0) {
            $frais['collecte'] += 100 - ($frais['collecte'] + $frais['attente'] + $frais['retard']);
        }

        $presences = DB::table('attendance_records as ar')
            ->join('attendance_sessions as asess', 'ar.attendance_session_id', '=', 'asess.id')
            ->join('establishment_memberships as em', 'ar.student_membership_id', '=', 'em.id')
            ->join('users as u', 'em.user_id', '=', 'u.id')
            ->where('em.establishment_id', $establishment->id)
            ->orderBy('ar.id', 'desc')
            ->limit(5)
            ->select('u.first_name', 'u.last_name', 'ar.attendance_status', 'asess.opened_at as recorded_at')
            ->get()
            ->map(function ($p) {
                return [
                    'nom' => $p->first_name . ' ' . $p->last_name,
                    'departement' => 'Informatique',
                    'heure' => $p->recorded_at ? \Carbon\Carbon::parse($p->recorded_at)->format('H:i') : '08:00',
                    'present' => $p->attendance_status === 'PRESENT',
                    'photo' => null,
                ];
            });

        if ($presences->isEmpty()) {
            $presences = [
                ['nom' => 'Alice Mbarga', 'departement' => 'Informatique', 'heure' => '07:45', 'present' => true, 'photo' => null],
                ['nom' => 'Jean Dupont', 'departement' => 'Gestion', 'heure' => '08:10', 'present' => true, 'photo' => null],
            ];
        }

        $moyenneGlobale = DB::table('grades')->avg('score') ?? 0;
        $meilleurEtudiant = DB::table('grades as g')
            ->join('establishment_memberships as em', 'g.student_membership_id', '=', 'em.id')
            ->join('users as u', 'em.user_id', '=', 'u.id')
            ->where('em.establishment_id', $establishment->id)
            ->select('u.first_name', 'u.last_name', DB::raw('AVG(g.score) as avg_score'))
            ->groupBy('u.id', 'u.first_name', 'u.last_name')
            ->orderBy('avg_score', 'desc')
            ->first();

        $examens = [
            'moyenne' => round($moyenneGlobale ?? 0, 1),
            'meilleur' => $meilleurEtudiant ? ($meilleurEtudiant->first_name . ' ' . $meilleurEtudiant->last_name) : 'Aucun',
            'sgpa' => $meilleurEtudiant ? round(($meilleurEtudiant->avg_score / 20) * 10, 2) : 0.00,
        ];

        $resultats = DB::table('grades as g')
            ->join('assessments as a', 'g.assessment_id', '=', 'a.id')
            ->join('establishment_memberships as em', 'g.student_membership_id', '=', 'em.id')
            ->join('users as u', 'em.user_id', '=', 'u.id')
            ->where('em.establishment_id', $establishment->id)
            ->orderBy('g.id', 'desc')
            ->limit(4)
            ->select('u.first_name', 'u.last_name', 'em.member_number', 'a.title as examen_title', 'g.score')
            ->get()
            ->map(function ($r) {
                return [
                    'nom' => $r->first_name . ' ' . $r->last_name,
                    'matricule' => $r->member_number ?? 'N/A',
                    'programme' => 'Informatique',
                    'examen' => $r->examen_title,
                    'note' => (float) $r->score,
                    'reussi' => (float) $r->score >= 10.0,
                ];
            });

        if ($resultats->isEmpty()) {
            $resultats = [
                ['nom' => 'Alice Mbarga', 'matricule' => 'SHK-2026-001', 'programme' => 'Informatique', 'examen' => 'Programmation Web', 'note' => 16.5, 'reussi' => true],
            ];
        }
        

        // =====================================================================
        // 3. DONNÉES POUR DASHBOARD ÉTUDIANT / ENSEIGNANT
        // =====================================================================
        $profile = [
            'Nom' => $user->first_name . ' ' . $user->last_name,
            'Matricule' => $membership ? ($membership->member_number ?: 'N/A') : 'N/A',
            'Filière' => 'Non définie',
            'Année académique' => 'Non définie',
            'Statut' => $membership ? ucfirst(strtolower($membership->status)) : 'Inactif',
        ];

        $enrollment = null;
        $programEnrollment = null;

        if (in_array('ETUDIANT', $roles, true)) {
            $enrollment = DB::table('student_enrollments as se')
                ->leftJoin('academic_years as ay', 'ay.id', '=', 'se.academic_year_id')
                ->where('se.student_membership_id', $membership->id)
                ->where('se.establishment_id', $establishment->id)
                ->select('se.id', 'se.enrollment_number', 'se.academic_year_id', 'ay.name as academic_year_name')
                ->first();

            if ($enrollment) {
                $profile['Matricule'] = $enrollment->enrollment_number ?: $profile['Matricule'];
                $profile['Année académique'] = $enrollment->academic_year_name ?: 'N/A';
                $programEnrollment = DB::table('program_enrollments as pe')
                    ->leftJoin('programs as p', 'p.id', '=', 'pe.program_id')
                    ->leftJoin('levels as l', 'l.id', '=', 'pe.level_id')
                    ->where('pe.student_enrollment_id', $enrollment->id)
                    ->select('pe.cohort_id', 'p.name as program_name', 'l.name as level_name')
                    ->first();

                if ($programEnrollment) {
                    $filiere = $programEnrollment->program_name ?: 'Programme';
                    $niveau = $programEnrollment->level_name ?: '';
                    $profile['Filière'] = trim($filiere . ' ' . $niveau);
                }
            }
        } elseif (in_array('ENSEIGNANT', $roles, true)) {
            $profile['Filière'] = 'Corps enseignant';
        } elseif (in_array('ADMIN_PLATEFORME', $roles, true) || in_array('DIRECTEUR', $roles, true)) {
            $profile['Filière'] = 'Administration';
        }

        $courses = [];
        if (in_array('ETUDIANT', $roles, true) && $enrollment) {
            $courseQuery = DB::table('course_offerings as co')
                ->leftJoin('courses as c', 'c.id', '=', 'co.course_id')
                ->where('co.establishment_id', $establishment->id)
                ->where('co.academic_year_id', $enrollment->academic_year_id);

            if ($programEnrollment) {
                $courseQuery->where('co.cohort_id', $programEnrollment->cohort_id);
            }

            $offerings = $courseQuery->select('c.title as course_title', 'co.term')->limit(3)->get();
            foreach ($offerings as $offering) {
                $courses[] = ['name' => $offering->course_title ?: 'Cours sans titre', 'term' => $offering->term ?: 'Semestre en cours'];
            }
        }        elseif (in_array('ENSEIGNANT', $roles, true)) {
            $profile['Filière'] = 'Corps enseignant';

            // 1. Récupérer les IDs des offres de cours de cet enseignant
            $teacherCourseOfferingIds = DB::table('teacher_assignments')
                ->where('teacher_membership_id', $membership->id)
                ->pluck('course_offering_id')
                ->toArray();

            // 2. Statistiques
            $totalCours = count($teacherCourseOfferingIds);
            
            $totalEtudiants = 0;
            if (!empty($teacherCourseOfferingIds)) {
                 $totalEtudiants = DB::table('attendance_records')
                    ->join('attendance_sessions', 'attendance_records.attendance_session_id', '=', 'attendance_sessions.id')
                    ->join('timetable_entries', 'attendance_sessions.timetable_entry_id', '=', 'timetable_entries.id')
                    ->whereIn('timetable_entries.course_offering_id', $teacherCourseOfferingIds)
                    ->distinct('attendance_records.student_membership_id')
                    ->count('attendance_records.student_membership_id');
            }

            $notesEnAttente = 0;
            if (!empty($teacherCourseOfferingIds)) {
                $notesEnAttente = DB::table('assignment_submissions as sub')
                    ->join('assignments as a', 'sub.assignment_id', '=', 'a.id')
                    ->whereIn('a.course_offering_id', $teacherCourseOfferingIds)
                    ->where('sub.status', 'SOUMIS')
                    ->count();
            }

            $presenceMoyenne = 0; // À calculer plus tard si besoin

            $stats = [
                'cours' => $totalCours,
                'etudiants' => $totalEtudiants,
                'notes_en_attente' => $notesEnAttente,
                'presence_moyenne' => $presenceMoyenne,
            ];

            // 3. Emploi du temps du jour
            $today = now()->toDateString();
            $emploi = collect();
            if (!empty($teacherCourseOfferingIds)) {
                $emploi = DB::table('timetable_entries as te')
                    ->join('course_offerings as co', 'te.course_offering_id', '=', 'co.id')
                    ->join('courses as c', 'co.course_id', '=', 'c.id')
                    ->leftJoin('classrooms as cr', 'te.classroom_id', '=', 'cr.id')
                    ->whereIn('te.course_offering_id', $teacherCourseOfferingIds)
                    ->whereDate('te.starts_at', '<=', $today)
                    ->whereDate('te.ends_at', '>=', $today)
                    ->orderBy('te.starts_at')
                    ->select('te.starts_at', 'te.ends_at', 'c.title as course_title', 'cr.name as classroom_name')
                    ->get()
                    ->map(function ($item) {
                        $now = now();
                        $start = \Carbon\Carbon::parse($item->starts_at);
                        $end = \Carbon\Carbon::parse($item->ends_at);
                        $statut = null;
                        if ($now >= $start && $now <= $end) $statut = 'en_cours';
                        elseif ($now > $end) $statut = 'termine';

                        return [
                            'debut' => $start->format('H:i'),
                            'fin' => $end->format('H:i'),
                            'titre' => $item->course_title,
                            'lieu' => $item->classroom_name ?: 'Salle non assignée',
                            'statut' => $statut
                        ];
                    });
            }

            // 4. Devoirs à corriger
            $devoirs = collect();
            if (!empty($teacherCourseOfferingIds)) {
                $devoirs = DB::table('assignments as a')
                    ->join('course_offerings as co', 'a.course_offering_id', '=', 'co.id')
                    ->join('courses as c', 'co.course_id', '=', 'c.id')
                    ->join('assignment_submissions as sub', 'a.id', '=', 'sub.assignment_id')
                    ->whereIn('a.course_offering_id', $teacherCourseOfferingIds)
                    ->where('sub.status', 'SOUMIS')
                    ->select('a.title', 'c.title as course_name', DB::raw('COUNT(sub.id) as pending_count'))
                    ->groupBy('a.id', 'a.title', 'c.title')
                    ->limit(4)
                    ->get()
                    ->map(function ($item) {
                        return [
                            'titre' => $item->title,
                            'details' => $item->pending_count . ' copie(s) à noter · ' . $item->course_name,
                            'badge' => 'À corriger'
                        ];
                    });
            }
            // Récupérer les notes PUBLIÉES de l'étudiant connecté
            $studentNotes = DB::table('grades as g')
                ->join('assessments as a', 'g.assessment_id', '=', 'a.id')
                ->join('course_offerings as co', 'a.course_offering_id', '=', 'co.id')
                ->join('courses as c', 'co.course_id', '=', 'c.id')
                // ✅ CORRECTION : Jointure sur course_offering_id (niveau cours)
                ->join('grade_publications as gp', function ($join) {
                    $join->on('gp.course_offering_id', '=', 'co.id')
                        ->where('gp.status', '=', 'PUBLIE');
                })
                ->where('g.student_membership_id', $membership->id)
                ->select(
                    'c.title as course_name', 
                    'a.title as assessment_name', 
                    'g.score', 
                    'a.max_score',
                    'gp.published_at as graded_at'
                )
                ->orderBy('gp.published_at', 'desc')
                ->limit(5)
                ->get();
            // 5. Dernières Activités
            $activites = DB::table('announcements')
                ->where('author_membership_id', $membership->id)
                ->orderBy('published_at', 'desc')
                ->limit(3)
                ->get()
                ->map(function ($item) {
                    return [
                        'titre' => 'Annonce publiée : ' . $item->title,
                        'details' => \Carbon\Carbon::parse($item->published_at)->diffForHumans()
                    ];
                });

            if ($activites->isEmpty()) {
                $activites = collect([
                    ['titre' => 'Bienvenue sur votre espace', 'details' => 'Configurez vos premiers cours et devoirs.']
                ]);
            }

            // 6. Retourner la vue spécifique Enseignant
            return view('DashboardTeacher', [
                'user' => (object) ['name' => $user->first_name . ' ' . $user->last_name],
                'etablissementNom' => $establishment->name,
                'stats' => $stats,
                'emploi' => $emploi,
                'devoirs' => $devoirs,
                'activites' => $activites,
                'studentNotes' => $studentNotes,
            ]);
        }

        if (empty($courses)) {
            $courses = [['name' => 'Aucun cours assigné pour le moment', 'term' => '-']];
        }

        $news = DB::table('announcements')
            ->where('establishment_id', $establishment->id)
            ->orderBy('published_at', 'desc')
            ->select('title', 'published_at')
            ->limit(2)
            ->get()
            ->map(function ($item) {
                return [
                    'image' => 'nsu-campus.png',
                    'title' => $item->title,
                    'date' => $item->published_at ? \Carbon\Carbon::parse($item->published_at)->format('d M Y') : 'Récent',
                ];
            });

        if ($news->isEmpty()) {
            $news = [['image' => 'nsu-campus.png', 'title' => 'Bienvenue sur ' . $establishment->name, 'date' => 'Année académique en cours']];
        }

        $navigation = [];
        if (in_array('ETUDIANT', $roles, true)) {
            $navigation[] = ['type' => 'heading', 'title' => 'Espace étudiant', 'action' => ''];
            $navigation[] = ['type' => 'link', 'label' => 'Tableau de bord', 'icon' => 'layout-dashboard', 'href' => route('dashboard'), 'active' => request()->routeIs('dashboard')];
            $navigation[] = ['type' => 'link', 'label' => 'Mon profil', 'icon' => 'user-round', 'href' => route('student.profile')];
            $navigation[] = ['type' => 'link', 'label' => 'Mes cours', 'icon' => 'book-open', 'href' => route('student.courses'), 'chevron' => true];
            $navigation[] = ['type' => 'link', 'label' => 'Mes notes', 'icon' => 'clipboard-list', 'href' => route('student.grades')];
            $navigation[] = ['type' => 'link', 'label' => 'Emploi du temps', 'icon' => 'calendar-days', 'href' => route('student.timetable')];
            $navigation[] = ['type' => 'heading', 'title' => 'Vie académique', 'action' => ''];
            $navigation[] = ['type' => 'link', 'label' => 'Inscriptions', 'icon' => 'graduation-cap', 'href' => route('student.enrollments')];
            $navigation[] = ['type' => 'link', 'label' => 'Compte financier', 'icon' => 'wallet', 'href' => route('student.finances')];
            $navigation[] = ['type' => 'link', 'label' => 'Annonces', 'icon' => 'bell', 'href' => route('student.announcements')];
        } elseif (in_array('ENSEIGNANT', $roles, true)) {
            $navigation[] = ['type' => 'heading', 'title' => 'Espace enseignant', 'action' => ''];
            $navigation[] = ['type' => 'link', 'label' => 'Tableau de bord', 'icon' => 'layout-dashboard', 'href' => route('dashboard'), 'active' => request()->routeIs('dashboard')];
            $navigation[] = ['type' => 'link', 'label' => 'Mes cours', 'icon' => 'book-open', 'href' => route('teacher.courses')];
            $navigation[] = ['type' => 'link', 'label' => 'Saisie des notes', 'icon' => 'clipboard-list', 'href' => route('teacher.grades')];
            $navigation[] = ['type' => 'link', 'label' => 'Emploi du temps', 'icon' => 'calendar-days', 'href' => route('teacher.timetable')];
            $navigation[] = ['type' => 'link', 'label' => 'Annonces', 'icon' => 'bell', 'href' => route('teacher.announcements')];
        } else {
            $navigation[] = ['type' => 'heading', 'title' => 'Administration', 'action' => ''];
            $navigation[] = ['type' => 'link', 'label' => 'Tableau de bord', 'icon' => 'layout-dashboard', 'href' => route('dashboard'), 'active' => request()->routeIs('dashboard')];
            $navigation[] = ['type' => 'link', 'label' => 'Établissements', 'icon' => 'building', 'href' => '#'];
            $navigation[] = ['type' => 'link', 'label' => 'Utilisateurs', 'icon' => 'users', 'href' => '#'];
        }

        $quickLinks = ['Dossier académique', 'Inscriptions', 'Compte financier', 'Emploi du temps'];

              // =====================================================================
        // 4. REDIRECTION FINALE SELON LE RÔLE
        // =====================================================================
        
        // 1. Administrateurs
        if (in_array('ADMIN_PLATEFORME', $roles, true) || in_array('DIRECTEUR', $roles, true) || in_array('ADMIN_ETABLISSEMENT', $roles, true)) {
            return view('DashboardAdmin', compact(
                'user', 'roleLabel', 'annee', 'notifications', 'stats', 'frais', 'presences', 'examens', 'resultats'
            ));
        } 
        
        // 2. Scolarité et Secrétariat (Office)
        if (in_array('AGENT_SCOLARITE', $roles, true) || in_array('SECRETAIRE_ACADEMIQUE', $roles, true)) {
            return view('DashboardOffice', [
                'user' => $user,
                'establishment' => $establishment,
            ]);
        }

        // 3. Responsables Pédagogiques -> Dashboard Pédagogique dédié
        if (in_array('RESPONSABLE_PEDAGOGIQUE', $roles, true)) {
            return redirect()->route('pedagogy.dashboard');
        }

        // 4. Étudiants (Fallback par défaut)
        $studentNotes = collect();
        if (in_array('ETUDIANT', $roles, true) && $membership) {
          $studentNotes = DB::table('grades as g')
            ->join('assessments as a', 'g.assessment_id', '=', 'a.id')
            ->join('course_offerings as co', 'a.course_offering_id', '=', 'co.id')
            ->join('courses as c', 'co.course_id', '=', 'c.id')
            ->where('g.student_membership_id', $membership->id)
            ->whereNotNull('g.score')
            ->whereExists(function ($q) {
                $q->select(DB::raw(1))
                ->from('grade_publications as gp')
                ->whereColumn('gp.course_offering_id', 'co.id')
                ->where('gp.status', 'PUBLIE');
            })
            ->orderByDesc('g.validated_at')
            ->limit(5)
            ->select('a.title as assessment_name', 'c.title as course_name',
                    'g.score', 'a.max_score', 'g.validated_at as graded_at')
            ->get();
        }

        return view('dashboard', compact(
            'navigation', 'quickLinks', 'profile', 'courses', 'news', 'studentNotes', 'establishment'
        ));
        
      
    }
}