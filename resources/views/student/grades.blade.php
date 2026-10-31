@extends('layouts.app')

@section('title', 'Historique des Notes — SHARKLINK')

@section('content')
<div class="flex flex-col gap-5">
    
    {{-- En-tête de la page --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-ink">Historique de mes notes</h1>
            <p class="text-sm text-muted mt-1">Retrouvez l'ensemble de vos évaluations publiées.</p>
        </div>
        <a href="{{ route('student.transcript') }}" class="flex items-center gap-2 rounded-[5px] bg-navy px-4 py-2.5 text-[11px] font-semibold text-white hover:bg-opacity-90 transition-colors">
            <x-icon name="download" :size="14" />
            Télécharger le relevé PDF
        </a>
    </div>

    {{-- Liste des cours et notes --}}
    @forelse ($gradesHistory as $courseTitle => $grades)
        <article class="flex flex-col gap-4 rounded-xl bg-white p-5 shadow-sm">
            <div class="flex items-center justify-between border-b border-line pb-3">
                <div>
                    <h2 class="text-[15px] font-bold text-ink">{{ $courseTitle }}</h2>
                    <p class="text-[10px] text-muted mt-1">Code: {{ $grades->first()->course_code ?? 'N/A' }}</p>
                </div>
                <span class="rounded-full bg-navy/10 px-3 py-1 text-[10px] font-bold text-navy">
                    {{ $grades->count() }} évaluation(s)
                </span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="text-[10px] font-bold uppercase text-muted border-b border-line">
                            <th class="py-2 px-2">Intitulé de l'évaluation</th>
                            <th class="py-2 px-2 text-center">Note obtenue</th>
                            <th class="py-2 px-2 text-center">Note max</th>
                            <th class="py-2 px-2 text-center">Pourcentage</th>
                            <th class="py-2 px-2 text-right">Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($grades as $grade)
                            @php
                                $percentage = ($grade->score / $grade->max_score) * 100;
                                $statusColor = $percentage >= 50 ? 'text-[#16845b]' : 'text-[#ef4444]';
                            @endphp
                            <tr class="border-b border-line last:border-0 hover:bg-slate-50 transition-colors">
                                <td class="py-3 px-2 text-[11px] font-medium text-ink">{{ $grade->assessment_title }}</td>
                                <td class="py-3 px-2 text-center text-[12px] font-bold {{ $statusColor }}">
                                    {{ number_format($grade->score, 2) }}
                                </td>
                                <td class="py-3 px-2 text-center text-[11px] text-muted">{{ $grade->max_score }}</td>
                                <td class="py-3 px-2 text-center text-[11px] font-semibold {{ $statusColor }}">
                                    {{ number_format($percentage, 1) }}%
                                </td>
                                <td class="py-3 px-2 text-right text-[10px] text-muted">
                                    {{ \Carbon\Carbon::parse($grade->published_at)->format('d M Y') }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </article>
    @empty
        <div class="flex flex-col items-center justify-center rounded-xl bg-white p-10 text-center">
            <x-icon name="file-text" :size="40" class="text-muted opacity-50 mb-3" />
            <p class="text-sm font-medium text-ink">Aucune note publiée pour le moment.</p>
            <p class="text-xs text-muted mt-1">Dès qu'un enseignant publiera vos notes, elles apparaîtront ici.</p>
        </div>
    @endforelse

</div>
@endsection