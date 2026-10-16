<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\DashboardController;
use App\Models\User;

// 1. Redirection de la page d'accueil vers le tableau de bord
Route::get('/', function () {
    return redirect()->route('dashboard');
});

// 2. Affichage du formulaire de connexion
Route::view('/login', 'auth.login')->name('login');

// 3. Traitement du formulaire de connexion (POST)
Route::post('/login', function (Request $request) {
    // A. Validation des données reçues
    $credentials = $request->validate([
        'email' => ['required', 'email'],
        'password' => ['required'],
    ]);

    // B. Tentative d'authentification
    // On ajoute 'status' => 'ACTIF' pour empêcher les comptes suspendus de se connecter
    if (Auth::attempt([
        'email' => $credentials['email'], 
        'password' => $credentials['password'], 
        'status' => 'ACTIF'
    ], $request->boolean('remember'))) {
        
        // C. Succès : Régénérer la session (protection contre le vol de session)
        $request->session()->regenerate();

        // D. Mettre à jour la date de dernière connexion dans la base de données
        $user = Auth::user();
        $user->update(['last_login_at' => now()]);

        // E. Redirection vers la page demandée (ou le dashboard par défaut)
        return redirect()->intended(route('dashboard'));
    }

    // F. Échec : Message d'erreur générique (ne jamais dire si c'est l'email ou le mot de passe qui est faux)
    return back()->withErrors([
        'email' => 'Les informations d\'identification fournies ne correspondent pas à nos enregistrements ou votre compte est inactif.',
    ])->onlyInput('email');
})->name('login.store');

// 4. Route de déconnexion (POST est la norme de sécurité Laravel)
Route::post('/logout', function (Request $request) {
    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    return redirect('/login');
})->name('logout');

// 5. Groupe de routes protégées (nécessite d'être connecté)
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    // Tu ajouteras ici toutes les autres routes protégées plus tard
});