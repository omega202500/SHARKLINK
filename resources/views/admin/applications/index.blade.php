@extends('layouts.app')

@section('title', 'Gestion des Candidatures')

@section('content')
<div class="flex flex-col gap-6">
    
    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-ink">Candidatures</h1>
            <p class="text-sm text-muted mt-1">Gérez les dossiers de candidature et les admissions.</p>
        </div>
    </div>

    {{-- Stats rapides --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl border border-line p-4">
            <p class="text-xs text-muted">Total candidatures</p>
            <p class="text-2xl font-bold text-ink mt-1">{{ $stats['total'] }}</p>
        </div>
        <div class="bg-white rounded-xl border border-line p-4">
            <p class="text-xs text-muted">En attente</p>
            <p class="text-2xl font-bold text-orange-600 mt-1">{{ $stats['pending'] }}</p>
        </div>
        <div class="bg-white rounded-xl border border-line p-4">
            <p class="text-xs text-muted">Acceptées</p>
            <p class="text-2xl font-bold text-green-600 mt-1">{{ $stats['accepted'] }}</p>
        </div>
        <div class="bg-white rounded-xl border border-line p-4">
            <p class="text-xs text-muted">Rejetées</p>
            <p class="text-2xl font-bold text-red-600 mt-1">{{ $stats['rejected'] }}</p>
        </div>
    </div>

    {{-- Filtres et recherche --}}
    <div class="bg-white rounded-xl border border-line p-4">
        <form action="{{ route('admin.applications.index') }}" method="GET" class="flex flex-col md:flex-row gap-3">
            <div class="flex-1">
                <input type="text" name="search" value="{{ request('search') }}" 
                       placeholder="Rechercher par nom, email..." 
                       class="w-full px-4 py-2 rounded-lg border border-line focus:border-navy focus:ring-1 focus:ring-navy outline-none text-sm">
            </div>
            <select name="status" class="px-4 py-2 rounded-lg border border-line focus:border-navy focus:ring-1 focus:ring-navy outline-none text-sm bg-white">
                <option value="">Tous les statuts</option>
                <option value="SOUMIS" {{ request('status') == 'SOUMIS' ? 'selected' : '' }}>Soumis</option>
                <option value="EN_COURS" {{ request('status') == 'EN_COURS' ? 'selected' : '' }}>En cours</option>
                <option value="ACCEPTE" {{ request('status') == 'ACCEPTE' ? 'selected' : '' }}>Accepté</option>
                <option value="REJETE" {{ request('status') == 'REJETE' ? 'selected' : '' }}>Rejeté</option>
            </select>
            <button type="submit" class="px-6 py-2 bg-navy text-white rounded-lg text-sm font-semibold hover:bg-opacity-90 transition">
                Filtrer
            </button>
        </form>
    </div>

    {{-- Tableau des candidatures --}}
    <div class="bg-white rounded-xl border border-line overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-slate-50 border-b border-line">
                    <tr>
                        <th class="px-6 py-3 text-xs font-bold text-muted uppercase">Candidat</th>
                        <th class="px-6 py-3 text-xs font-bold text-muted uppercase">Formation</th>
                        <th class="px-6 py-3 text-xs font-bold text-muted uppercase">Date</th>
                        <th class="px-6 py-3 text-xs font-bold text-muted uppercase">Statut</th>
                        <th class="px-6 py-3 text-xs font-bold text-muted uppercase text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-line">
                    @forelse($applications as $app)
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-navy/10 flex items-center justify-center text-xs font-bold text-navy">
                                        {{ substr($app->first_name, 0, 1) }}{{ substr($app->last_name, 0, 1) }}
                                    </div>
                                    <div>
                                        <p class="text-sm font-semibold text-ink">{{ $app->first_name }} {{ $app->last_name }}</p>
                                        <p class="text-xs text-muted">{{ $app->email }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <p class="text-sm text-ink">{{ $app->program->name ?? 'N/A' }}</p>
                                <p class="text-xs text-muted">{{ $app->level->name ?? 'N/A' }}</p>
                            </td>
                            <td class="px-6 py-4 text-sm text-muted">
                                {{ $app->created_at->format('d M Y') }}
                            </td>
                            <td class="px-6 py-4">
                                @php
                                    $statusColors = [
                                        'SOUMIS' => 'bg-orange-100 text-orange-700',
                                        'EN_COURS' => 'bg-blue-100 text-blue-700',
                                        'ACCEPTE' => 'bg-green-100 text-green-700',
                                        'REJETE' => 'bg-red-100 text-red-700',
                                        'INSCRIT' => 'bg-navy text-white',
                                    ];
                                    $statusLabels = [
                                        'SOUMIS' => 'Soumis',
                                        'EN_COURS' => 'En cours',
                                        'ACCEPTE' => 'Accepté',
                                        'REJETE' => 'Rejeté',
                                        'INSCRIT' => 'Inscrit',
                                    ];
                                @endphp
                                <span class="px-2.5 py-1 rounded-full text-xs font-semibold {{ $statusColors[$app->status] ?? 'bg-gray-100 text-gray-700' }}">
                                    {{ $statusLabels[$app->status] ?? $app->status }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <a href="{{ route('admin.applications.show', $app->id) }}" 
                                   class="text-sm font-semibold text-navy hover:text-cyan transition">
                                    Voir dossier
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-10 text-center text-muted text-sm">
                                Aucune candidature trouvée.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        {{-- Pagination --}}
        @if($applications->hasPages())
            <div class="px-6 py-4 border-t border-line">
                {{ $applications->links() }}
            </div>
        @endif
    </div>
</div>
@endsection