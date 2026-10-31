@extends('layouts.app')

@section('title', 'Dossier de candidature #' . $application->id)

@section('content')

@if(session('success'))
    <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg text-sm flex items-start gap-3">
        <x-icon name="check-circle" :size="20" class="text-green-600 mt-0.5" />
        <div>
            <p class="font-semibold">Succès !</p>
            <p class="text-xs mt-1">{{ session('success') }}</p>
            <p class="text-xs mt-2 text-green-700">📧 N'oubliez pas de communiquer le mot de passe temporaire à l'étudiant.</p>
        </div>
    </div>
@endif

@if(session('error'))
    <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg text-sm">
        {{ session('error') }}
    </div>
@endif
<div class="flex flex-col gap-6">
    
    {{-- Header avec retour --}}
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.applications.index') }}" class="text-muted hover:text-ink transition">
                <x-icon name="arrow-left" :size="20" />
            </a>
            <div>
                <h1 class="text-2xl font-bold text-ink">Dossier de candidature</h1>
                <p class="text-sm text-muted">N°{{ str_pad($application->id, 6, '0', STR_PAD_LEFT) }}</p>
            </div>
        </div>
        <a href="{{ route('admin.applications.admission-letter', $application->id) }}" 
           class="px-4 py-2 bg-navy text-white rounded-lg text-sm font-semibold hover:bg-opacity-90 transition flex items-center gap-2">
            <x-icon name="download" :size="16" />
            Lettre d'admission
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        {{-- Colonne gauche : Infos --}}
        <div class="lg:col-span-2 space-y-6">
            
            {{-- Informations personnelles --}}
            <div class="bg-white rounded-xl border border-line p-6">
                <h2 class="text-lg font-bold text-ink mb-4">Informations personnelles</h2>
                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <p class="text-xs text-muted">Prénom</p>
                        <p class="font-semibold text-ink">{{ $application->first_name }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-muted">Nom</p>
                        <p class="font-semibold text-ink">{{ $application->last_name }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-muted">Email</p>
                        <p class="font-semibold text-ink">{{ $application->email }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-muted">Téléphone</p>
                        <p class="font-semibold text-ink">{{ $application->phone }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-muted">Date de naissance</p>
                        <p class="font-semibold text-ink">{{ \Carbon\Carbon::parse($application->birth_date)->format('d/m/Y') }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-muted">Lieu de naissance</p>
                        <p class="font-semibold text-ink">{{ $application->birth_place }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-muted">Genre</p>
                        <p class="font-semibold text-ink">{{ $application->gender === 'M' ? 'Masculin' : 'Féminin' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-muted">Nationalité</p>
                        <p class="font-semibold text-ink">{{ $application->nationality }}</p>
                    </div>
                </div>
            </div>

            {{-- Parcours académique --}}
            <div class="bg-white rounded-xl border border-line p-6">
                <h2 class="text-lg font-bold text-ink mb-4">Parcours académique</h2>
                <div class="space-y-3 text-sm">
                    <div>
                        <p class="text-xs text-muted">Dernier établissement</p>
                        <p class="font-semibold text-ink">{{ $application->previous_school }}</p>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-xs text-muted">Diplôme</p>
                            <p class="font-semibold text-ink">{{ $application->previous_diploma }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-muted">Année</p>
                            <p class="font-semibold text-ink">{{ $application->graduation_year }}</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Formation souhaitée --}}
            <div class="bg-white rounded-xl border border-line p-6">
                <h2 class="text-lg font-bold text-ink mb-4">Formation souhaitée</h2>
                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <p class="text-xs text-muted">Programme</p>
                        <p class="font-semibold text-ink">{{ $application->program->name ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-muted">Niveau</p>
                        <p class="font-semibold text-ink">{{ $application->level->name ?? 'N/A' }}</p>
                    </div>
                </div>
            </div>

            {{-- Documents uploadés --}}
            <div class="bg-white rounded-xl border border-line p-6">
                <h2 class="text-lg font-bold text-ink mb-4">Documents joints</h2>
                <div class="grid grid-cols-2 gap-3">
                    @foreach($application->documents as $doc)
                        <a href="{{ Storage::url($doc->file_path) }}" target="_blank" 
                           class="flex items-center gap-3 p-3 rounded-lg border border-line hover:bg-slate-50 transition">
                            <x-icon name="file-text" :size="24" class="text-navy" />
                            <div class="min-w-0 flex-1">
                                <p class="text-xs font-semibold text-ink truncate">{{ ucfirst($doc->document_type) }}</p>
                                <p class="text-[10px] text-muted">Voir le fichier</p>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Colonne droite : Statut et actions --}}
        <div class="space-y-6">
            
            {{-- Changer le statut --}}
            <div class="bg-white rounded-xl border border-line p-6">
                <h2 class="text-lg font-bold text-ink mb-4">Statut du dossier</h2>
                
                <form action="{{ route('admin.applications.update-status', $application->id) }}" method="POST" class="space-y-4">
                    @csrf
                    @method('PATCH')
                    
                    <div>
                        <label class="block text-xs font-semibold text-muted mb-2">Nouveau statut</label>
                        <select name="status" class="w-full px-3 py-2 rounded-lg border border-line focus:border-navy outline-none text-sm bg-white">
                            <option value="SOUMIS" {{ $application->status == 'SOUMIS' ? 'selected' : '' }}>Soumis</option>
                            <option value="EN_COURS" {{ $application->status == 'EN_COURS' ? 'selected' : '' }}>En cours d'examen</option>
                            <option value="ACCEPTE" {{ $application->status == 'ACCEPTE' ? 'selected' : '' }}>Accepté</option>
                            <option value="REJETE" {{ $application->status == 'REJETE' ? 'selected' : '' }}>Rejeté</option>
                            <option value="INSCRIT" {{ $application->status == 'INSCRIT' ? 'selected' : '' }}>Inscrit</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-muted mb-2">Commentaire admin</label>
                        <textarea name="admin_comment" rows="3" class="w-full px-3 py-2 rounded-lg border border-line focus:border-navy outline-none text-sm" placeholder="Notes internes...">{{ $application->admin_comment }}</textarea>
                    </div>

                    <button type="submit" class="w-full py-2.5 bg-navy text-white rounded-lg text-sm font-semibold hover:bg-opacity-90 transition">
                        Mettre à jour
                    </button>
                </form>
            </div>

            {{-- Actions rapides --}}
         <div class="bg-white rounded-xl border border-line p-6">
            <h2 class="text-lg font-bold text-ink mb-4">Actions rapides</h2>
            <div class="space-y-2">
                
                @if($application->status === 'ACCEPTE')
                    {{-- Bouton de conversion (seulement si accepté) --}}
                    <form action="{{ route('admin.applications.convert', $application->id) }}" method="POST" 
                        onsubmit="return confirm('⚠️ Cette action va créer un compte étudiant avec les informations du candidat. Continuer ?')">
                        @csrf
                        <button type="submit" class="w-full flex items-center justify-center gap-2 px-4 py-2.5 bg-[#22d3ee] text-navy rounded-lg text-sm font-bold hover:bg-cyan-400 transition">
                            <x-icon name="user-check" :size="16" />
                            Convertir en étudiant inscrit
                        </button>
                    </form>
                @elseif($application->status === 'INSCRIT')
                    <div class="w-full flex items-center justify-center gap-2 px-4 py-2.5 bg-green-100 text-green-700 rounded-lg text-sm font-bold">
                        <x-icon name="check-circle" :size="16" />
                        Déjà inscrit comme étudiant
                    </div>
                @endif

                @if(in_array($application->status, ['ACCEPTE', 'INSCRIT']))
                    <a href="{{ route('admin.applications.admission-letter', $application->id) }}" 
                    class="flex items-center justify-center gap-2 px-4 py-2.5 border border-line rounded-lg text-sm font-semibold text-ink hover:bg-slate-50 transition">
                        <x-icon name="download" :size="16" />
                        Lettre d'admission PDF
                    </a>
                @endif

                <button type="button" class="w-full flex items-center justify-center gap-2 px-4 py-2.5 border border-line rounded-lg text-sm font-semibold text-ink hover:bg-slate-50 transition">
                    <x-icon name="mail" :size="16" />
                    Envoyer un email
                </button>
            </div>
        </div>
            {{-- Timeline --}}
            <div class="bg-white rounded-xl border border-line p-6">
                <h2 class="text-lg font-bold text-ink mb-4">Historique</h2>
                <div class="space-y-3 text-xs">
                    <div class="flex items-start gap-2">
                        <div class="w-2 h-2 rounded-full bg-navy mt-1"></div>
                        <div>
                            <p class="font-semibold text-ink">Candidature soumise</p>
                            <p class="text-muted">{{ $application->created_at->format('d M Y à H:i') }}</p>
                        </div>
                    </div>
                    @if($application->updated_at != $application->created_at)
                        <div class="flex items-start gap-2">
                            <div class="w-2 h-2 rounded-full bg-cyan mt-1"></div>
                            <div>
                                <p class="font-semibold text-ink">Dernière modification</p>
                                <p class="text-muted">{{ $application->updated_at->format('d M Y à H:i') }}</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection