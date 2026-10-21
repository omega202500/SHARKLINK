<?php

namespace App\Http\Controllers;

use App\Models\Establishment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AdminController extends Controller
{
    /**
     * Vérifie l'accès de l'administrateur à l'établissement actif.
     */
    private function getAdminContext(Request $request): array
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

        // Vérifie si l'utilisateur a un rôle d'administration
        $hasAdminRole = $membership->roles()
            ->whereIn('code', ['ADMIN_PLATEFORME', 'DIRECTEUR', 'ADMIN_ETABLISSEMENT'])
            ->exists();

        abort_unless($hasAdminRole, 403);

        return [
            'user' => $user,
            'establishment' => $establishment,
            'membership' => $membership,
        ];
    }

    /**
     * Pages provisoires pour les sections administrateur
     */
    public function users(Request $request): View
    {
        $context = $this->getAdminContext($request);
        return view('admin.users', compact('context'));
    }

    public function establishments(Request $request): View
    {
        $context = $this->getAdminContext($request);
        return view('admin.establishments', compact('context'));
    }

    public function programs(Request $request): View
    {
        $context = $this->getAdminContext($request);
        return view('admin.programs', compact('context'));
    }

    public function roles(Request $request): View
    {
        $context = $this->getAdminContext($request);
        return view('admin.roles', compact('context'));
    }

    public function settings(Request $request): View
    {
        $context = $this->getAdminContext($request);
        return view('admin.settings', compact('context'));
    }

    public function reports(Request $request): View
    {
        $context = $this->getAdminContext($request);
        return view('admin.reports', compact('context'));
    }
}