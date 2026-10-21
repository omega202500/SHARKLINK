<?php

namespace App\Http\Controllers;

use App\Models\Establishment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class OfficeController extends Controller
{
    /**
     * Vérifie l'accès de l'agent de scolarité/secrétaire à l'établissement actif.
     */
    private function getOfficeContext(Request $request): array
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

        // Vérifie si l'utilisateur a un rôle de scolarité ou secrétariat
        $hasOfficeRole = $membership->roles()
            ->whereIn('code', ['AGENT_SCOLARITE', 'SECRETAIRE_ACADEMIQUE'])
            ->exists();

        abort_unless($hasOfficeRole, 403);

        return [
            'user' => $user,
            'establishment' => $establishment,
            'membership' => $membership,
        ];
    }

    /**
     * Pages provisoires pour les sections de scolarité
     */
    public function students(Request $request): View
    {
        $context = $this->getOfficeContext($request);
        return view('office.students', compact('context'));
    }

    public function enrollments(Request $request): View
    {
        $context = $this->getOfficeContext($request);
        return view('office.enrollments', compact('context'));
    }

    public function documents(Request $request): View
    {
        $context = $this->getOfficeContext($request);
        return view('office.documents', compact('context'));
    }

    public function timetable(Request $request): View
    {
        $context = $this->getOfficeContext($request);
        return view('office.timetable', compact('context'));
    }

    public function announcements(Request $request): View
    {
        $context = $this->getOfficeContext($request);
        return view('office.announcements', compact('context'));
    }
}