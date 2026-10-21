<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EstablishmentSelectionController;
use App\Http\Controllers\TeacherController;
// =============================================================================
// 1. ROUTES PUBLIQUES (Accessibles sans être connecté)
// =============================================================================

// Redirection de la page d'accueil vers le tableau de bord (sera intercepté par le middleware auth si non connecté)
Route::get('/', function () {
    return redirect()->route('dashboard');
});

// Affichage du formulaire de connexion
Route::view('/login', 'auth.login')->name('login');

// Traitement du formulaire de connexion (POST)
Route::post('/login', function (Request $request) {
    $credentials = $request->validate([
        'email' => ['required', 'email'],
        'password' => ['required'],
    ]);

    if (Auth::attempt([
        'email' => $credentials['email'], 
        'password' => $credentials['password'], 
        'status' => 'ACTIF'
    ], $request->boolean('remember'))) {
        
        $request->session()->regenerate();
        $user = Auth::user();
        $user->update(['last_login_at' => now()]);

        // ✅ NOUVEAU : Redirection SYSTÉMATIQUE vers la sélection d'établissement
        // Cela garantit que l'utilisateur passe toujours par l'étape de choix/confirmation
        return redirect()->route('establishment.select');
    }

    return back()->withErrors([
        'email' => 'Les informations d\'identification fournies ne correspondent pas à nos enregistrements ou votre compte est inactif.',
    ])->onlyInput('email');
})->name('login.store');

// =============================================================================
// 2. ROUTES PROTÉGÉES (Nécessite d'être connecté - Middleware 'auth')
// =============================================================================

Route::middleware(['auth'])->group(function () {
    
    // --- Gestion du Tableau de Bord ---
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->middleware('establishment.active')
        ->name('dashboard');
    // --- Gestion du choix d'établissement (Multi-établissements) ---
    // Affiche la page de choix (vue auth.selection-etablissement alimentée par le contrôleur)
    Route::get('/select-establishment', [EstablishmentSelectionController::class, 'index'])
        ->name('establishment.select');

    // Enregistre l'établissement choisi (formulaire POST de chaque carte)
    Route::post('/select-establishment', [EstablishmentSelectionController::class, 'store'])
        ->name('establishment.switch');

    // --- Déconnexion ---
    Route::post('/logout', function (Request $request) {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    })->name('logout');

    // --- Gestion des routes pour les enseignants ---
    Route::get('/teacher/courses', [TeacherController::class, 'courses'])
    ->middleware('establishment.active')
    ->name('teacher.courses.index');

    Route::prefix('teacher')->name('teacher.')->middleware('establishment.active')->group(function () {
        Route::get('/courses', [\App\Http\Controllers\TeacherController::class, 'courses'])->name('courses');
        Route::get('/grades', [\App\Http\Controllers\TeacherController::class, 'grades'])->name('grades');
        Route::get('/timetable', [\App\Http\Controllers\TeacherController::class, 'timetable'])->name('timetable');
        Route::get('/attendance', [\App\Http\Controllers\TeacherController::class, 'attendance'])->name('attendance');
        Route::get('/assignments', [\App\Http\Controllers\TeacherController::class, 'assignments'])->name('assignments');
        Route::get('/announcements', [\App\Http\Controllers\TeacherController::class, 'announcements'])->name('announcements');
        Route::get('/reports', [\App\Http\Controllers\TeacherController::class, 'reports'])->name('reports');
    });

        // ✅ --- Gestion des routes pour les étudiants ---
    Route::prefix('student')->name('student.')->middleware('establishment.active')->group(function () {
        Route::get('/profile', [\App\Http\Controllers\StudentController::class, 'profile'])->name('profile');
        Route::get('/courses', [\App\Http\Controllers\StudentController::class, 'courses'])->name('courses');
        Route::get('/grades', [\App\Http\Controllers\StudentController::class, 'grades'])->name('grades');
        Route::get('/timetable', [\App\Http\Controllers\StudentController::class, 'timetable'])->name('timetable');
        Route::get('/enrollments', [\App\Http\Controllers\StudentController::class, 'enrollments'])->name('enrollments');
        Route::get('/finances', [\App\Http\Controllers\StudentController::class, 'finances'])->name('finances');
        Route::get('/announcements', [\App\Http\Controllers\StudentController::class, 'announcements'])->name('announcements');
    });

    // Tu ajouteras ici toutes les autres routes protégées plus tard (ex: /etudiants, /notes, etc.)
});