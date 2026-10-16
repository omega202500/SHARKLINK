<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EstablishmentSelectionController;
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
    // 1. Validation des données
    $credentials = $request->validate([
        'email' => ['required', 'email'],
        'password' => ['required'],
    ]);

    // 2. Tentative d'authentification
    if (Auth::attempt([
        'email' => $credentials['email'], 
        'password' => $credentials['password'], 
        'status' => 'ACTIF' // On s'assure que le compte est actif
    ], $request->boolean('remember'))) {
        
        // 3. Sécurité : Régénérer la session pour éviter la fixation de session
        $request->session()->regenerate();
        $user = Auth::user();
        
        // 4. Mettre à jour la date de dernière connexion
        $user->update(['last_login_at' => now()]);

        // 5. LOGIQUE DE REDIRECTION INTELLIGENTE
        // On compte combien d'établissements actifs sont liés à cet utilisateur
        $establishmentsCount = $user->establishments()->wherePivot('status', 'ACTIF')->count();

        if ($establishmentsCount === 1) {
            // CAS ÉTUDIANT (ou employé mono-établissement) : 
            $uniqueEstablishment = $user->establishments()->wherePivot('status', 'ACTIF')->first();
            
            // On le définit automatiquement en session
            session(['active_establishment_id' => $uniqueEstablishment->id]);
            
            // Redirection directe vers le dashboard
            return redirect()->intended(route('dashboard'));
        }

        // CAS EMPLOYÉ MULTI-ÉTABLISSEMENTS :
        // On l'envoie choisir son espace de travail
        return redirect()->route('establishment.select');
    }

    // 6. Échec de l'authentification
    return back()->withErrors([
        'email' => 'Les informations d\'identification fournies ne correspondent pas à nos enregistrements ou votre compte est inactif.',
    ])->onlyInput('email');
})->name('login.store');


// =============================================================================
// 2. ROUTES PROTÉGÉES (Nécessite d'être connecté - Middleware 'auth')
// =============================================================================

Route::middleware(['auth'])->group(function () {
    
    // --- Gestion du Tableau de Bord ---
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

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

    // Tu ajouteras ici toutes les autres routes protégées plus tard (ex: /etudiants, /notes, etc.)
});