<?php

namespace App\Http\Controllers;

use App\Models\Establishment;
use App\Models\User;
use Illuminate\Http\Request;

class SuperAdminController extends Controller
{
    public function dashboard()
    {
        $stats = [
            'total_establishments' => Establishment::count(),
            'active_establishments' => Establishment::where('status', 'ACTIF')->count(),
            'total_users' => User::count(),
            'monthly_revenue' => 0, // À calculer selon ton modèle de facturation
        ];

        $establishments = Establishment::withCount('memberships')->orderBy('created_at', 'desc')->paginate(10);

        return view('super-admin.dashboard', compact('stats', 'establishments'));
    }

    public function establishments()
    {
        $establishments = Establishment::all();
        return view('super-admin.establishments', compact('establishments'));
    }

    public function storeEstablishment(Request $request)
    {
        // Logique pour créer un nouvel établissement client
    }
}