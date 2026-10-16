<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class EstablishmentSelectionController extends Controller
{
    /**
     * Affiche la page de choix d'établissement.
     */
    public function index()
    {
        $user = Auth::user();

        // 1. Récupérer les établissements ACTIFS liés à cet utilisateur
        $etablissementsDB = $user->establishments()
            ->wherePivot('status', 'ACTIF')
            ->get();

        // 2. Formater les données pour la vue Blade
        $etablissements = $etablissementsDB->map(function ($etab) use ($user) {

            // Rôles de l'utilisateur dans cet établissement (méthode définie dans User.php)
            $roles = $user->rolesInEstablishment($etab->id)->pluck('name')->toArray();

            // Icône : "graduation-cap" pour une université, "school" sinon
            // (fichiers public/images/graduation-cap.svg et public/images/school.svg)
            $icone = Str::contains(Str::lower($etab->name), 'universit')
                ? 'graduation-cap'
                : 'school';

            return (object) [
                'id' => $etab->id,
                'nom' => $etab->name,
                'icone' => $icone,
                'roles' => empty($roles) ? ['Membre'] : $roles,
            ];
        });

        // 3. Envoyer les données à la vue
        return view('auth.selection-etablissement', [
            'user' => $user,
            'etablissements' => $etablissements,
        ]);
    }

    /**
     * Enregistre l'établissement choisi en session (appelé par le formulaire de chaque carte).
     */
    public function store(Request $request)
    {
        $request->validate([
            'establishment_id' => 'required|exists:establishments,id',
        ]);

        $user = Auth::user();
        $establishmentId = $request->input('establishment_id');

        // Vérification de sécurité : l'utilisateur a-t-il vraiment accès à cet établissement ?
        $hasAccess = $user->establishments()
            ->wherePivot('establishment_id', $establishmentId)
            ->wherePivot('status', 'ACTIF')
            ->exists();

        if (! $hasAccess) {
            abort(403, 'Vous n\'avez pas accès à cet établissement.');
        }

        // Stocker l'établissement actif en session
        session(['active_establishment_id' => $establishmentId]);

        return redirect()->route('dashboard');
    }
}
