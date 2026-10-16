<?php
namespace App\Http\Controllers;

use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
{
    $navigation = [
        ['type' => 'heading', 'title' => 'Espace étudiant', 'action' => ''],

        [
            'type' => 'link',
            'label' => 'Tableau de bord',
            'icon' => 'layout-dashboard',
            'href' => route('dashboard'),
            'active' => true,
        ],
        [
            'type' => 'link',
            'label' => 'Mon profil',
            'icon' => 'user-round',
            'href' => '#',
        ],
        [
            'type' => 'link',
            'label' => 'Mes cours',
            'icon' => 'book-open',
            'href' => '#',
            'chevron' => true,
        ],
        [
            'type' => 'link',
            'label' => 'Mes notes',
            'icon' => 'clipboard-list',
            'href' => '#',
        ],
        [
            'type' => 'link',
            'label' => 'Emploi du temps',
            'icon' => 'calendar-days',
            'href' => '#',
        ],

        ['type' => 'heading', 'title' => 'Vie académique', 'action' => ''],

        [
            'type' => 'link',
            'label' => 'Inscriptions',
            'icon' => 'graduation-cap',
            'href' => '#',
        ],
        [
            'type' => 'link',
            'label' => 'Compte financier',
            'icon' => 'wallet',
            'href' => '#',
        ],
        [
            'type' => 'link',
            'label' => 'Annonces',
            'icon' => 'bell',
            'href' => '#',
        ],
    ];

    $quickLinks = [
        'Dossier académique',
        'Inscriptions',
        'Compte financier',
        'Emploi du temps',
    ];

    $profile = [
        'Nom' => 'Étudiant SHARKLINK',
        'Matricule' => 'SHK-2026-001',
        'Filière' => 'Informatique',
        'Année académique' => '2026-2027',
        'Statut' => 'Actif',
    ];

    $courses = [
        ['name' => 'Programmation web', 'term' => 'Semestre 1'],
        ['name' => 'Bases de données', 'term' => 'Semestre 1'],
        ['name' => 'Génie logiciel', 'term' => 'Semestre 1'],
    ];

    $news = [
        [
            'image' => 'nsu-campus.png',
            'title' => 'Bienvenue sur SHARKLINK',
            'date' => 'Année académique 2026-2027',
        ],
        [
            'image' => 'nsu-campus.png',
            'title' => 'Actualités de votre établissement',
            'date' => 'Informations académiques',
        ],
    ];

    return view('dashboard', compact(
        'navigation',
        'quickLinks',
        'profile',
        'courses',
        'news'
    ));
}

}
