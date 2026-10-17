<?php
namespace App\Http\Middleware;

use App\Models\Establishment;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureActiveEstablishment
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user) {
            return redirect()->route('login');
        }

        $establishmentId = $request->session()->get(
            'active_establishment_id'
        );

        // Aucun établissement sélectionné
        if (! $establishmentId) {
            return redirect()->route('establishment.select');
        }

        // L'établissement doit exister et être actif
        $establishment = Establishment::query()
            ->whereKey($establishmentId)
            ->where('status', 'ACTIF')
            ->first();

        if (! $establishment) {
            $request->session()->forget('active_establishment_id');

            return redirect()
                ->route('establishment.select')
                ->withErrors([
                    'establishment' =>
                        'Cet établissement est indisponible. Veuillez en choisir un autre.',
                ]);
        }

        // L'utilisateur doit avoir une appartenance active
        // à cet établissement précis.
        $hasAccess = $user->establishments()
            ->whereKey($establishment->id)
            ->wherePivot('status', 'ACTIF')
            ->exists();

        if (! $hasAccess) {
            $request->session()->forget('active_establishment_id');

            abort(
                403,
                'Vous ne disposez pas d’un accès actif à cet établissement.'
            );
        }

        // Mettre l'établissement validé à disposition des contrôleurs.
        $request->attributes->set(
            'activeEstablishment',
            $establishment
        );

        return $next($request);
    }
}
