@extends('layouts.app')

@section('title', 'Dashboard — SHARKLINK')

@section('content')
    {{-- Bannière campus + liens rapides --}}
    <section class="flex flex-col gap-5">
        <div class="h-[266px] overflow-hidden rounded-xl bg-slate-300">
            <img src="{{ asset('images/nsu-campus.png') }}" alt="" class="size-full object-cover">
        </div>

        <div class="flex flex-wrap gap-[9px]">
            @foreach ($quickLinks as $link)
                <a href="#" class="flex items-center gap-[7px] rounded-full bg-navy px-[15px] py-2.5 text-[11px] font-semibold text-white">
                    {{ $link }}
                    <x-icon name="chevron-down" :size="10" />
                </a>
            @endforeach
        </div>
    </section>

    {{-- Widgets --}}
    <section class="flex flex-col gap-4">
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold text-ink">Tableau de bord</h1>
            <span class="text-sm text-muted">Établissement : {{ $establishment->name }}</span>
        </div>

        <div class="grid grid-cols-1 items-stretch gap-[18px] md:grid-cols-2 xl:grid-cols-4">
            {{-- Academic Profile --}}
            <article class="flex flex-col gap-5 rounded-xl bg-white p-5">
                <h2 class="text-[17px] font-bold text-ink">Profil Académique</h2>

                <dl class="flex flex-col gap-[9px] text-[10px] leading-[1.3]">
                    @foreach ($profile as $label => $value)
                        <div class="flex items-start gap-3">
                            <dt class="w-[90px] shrink-0 text-right text-muted">{{ $label }}</dt>
                            <dd class="min-w-0 flex-1 font-medium text-ink">{{ $value }}</dd>
                        </div>
                    @endforeach
                </dl>

                           <div class="mt-auto flex gap-2 pt-1">
                <a href="{{ route('student.grades') }}" class="flex-1 rounded-[5px] bg-navy p-2.5 text-center text-[11px] font-semibold text-white hover:bg-opacity-90 transition-colors">
                    Voir mes notes
                </a>
                <a href="{{ route('student.transcript') }}" class="flex-1 rounded-[5px] bg-[#22d3ee] p-2.5 text-center text-[11px] font-semibold text-[#0b1f3a] hover:bg-opacity-90 transition-colors">
                    Relevé PDF
                </a>
            </div>
            </article>

            {{-- My Courses --}}
            <article class="flex flex-col gap-[17px] rounded-xl bg-white p-5">
                <h2 class="text-[17px] font-bold text-ink">Mes Cours</h2>

                <div class="flex justify-between border-b border-line px-2.5 pb-2 text-muted">
                    <x-icon name="book-open" class="text-ink" />
                    <x-icon name="calendar-days" />
                    <x-icon name="clipboard-list" />
                    <x-icon name="settings" />
                </div>

                <ul class="flex flex-col">
                    @foreach ($courses as $course)
                        <li>
                            <a href="#" class="flex items-center gap-2.5 border-b border-line py-3 hover:bg-slate-50 transition-colors">
                                <div class="min-w-0 flex-1">
                                    <p class="text-[11px] font-medium text-ink">{{ $course['name'] }}</p>
                                    <p class="mt-[5px] text-[10px] text-muted">{{ $course['term'] }}</p>
                                </div>
                                <x-icon name="chevron-right" :size="12" class="text-ink" />
                            </a>
                        </li>
                    @endforeach
                </ul>

                <button type="button" class="mt-auto rounded-[5px] border border-line bg-white p-2.5 text-[11px] font-semibold text-muted hover:bg-slate-50 transition-colors">
                    Voir tous les cours
                </button>
            </article>

            {{-- News --}}
            <article class="flex flex-col gap-[18px] rounded-xl bg-white p-5">
                <h2 class="text-[17px] font-bold text-ink">Actualités</h2>

                @foreach ($news as $item)
                    <a href="#" class="flex items-center gap-3 group">
                        <img src="{{ asset('images/' . $item['image']) }}" alt=""
                             class="h-[65px] w-[67px] shrink-0 rounded-[5px] bg-slate-200 object-cover group-hover:opacity-80 transition-opacity">
                        <div class="min-w-0 flex-1">
                            <p class="text-[11px] font-semibold leading-[1.4] text-link group-hover:underline">{{ $item['title'] }}</p>
                            <p class="mt-[5px] text-[9px] text-muted">{{ $item['date'] }}</p>
                        </div>
                    </a>
                @endforeach

                <a href="#" class="mt-auto rounded-[5px] bg-navy p-2.5 text-center text-[11px] font-semibold text-white hover:bg-opacity-90 transition-colors">
                    Voir toutes les actualités
                </a>
            </article>
            {{-- Mes Résultats Récents --}}
            <article class="flex flex-col gap-[17px] rounded-xl bg-white p-5">
                <h2 class="text-[17px] font-bold text-ink">Mes Résultats Récents</h2>

                @if($studentNotes->isEmpty())
                    <div class="py-8 text-center text-xs text-muted">
                        <x-icon name="file-text" :size="32" class="mx-auto mb-2 opacity-50" />
                        <p>Aucune note publiée pour le moment.</p>
                    </div>
                @else
                    <ul class="flex flex-col">
                        @foreach ($studentNotes as $note)
                            <li class="flex items-center justify-between border-b border-line py-3 last:border-0">
                                <div class="min-w-0 flex-1">
                                    <p class="truncate text-[11px] font-medium text-ink">{{ $note->assessment_name }}</p>
                                    <p class="mt-[3px] text-[9px] text-muted">{{ $note->course_name }}</p>
                                </div>
                                <div class="ml-3 text-right">
                                    <span class="block text-[13px] font-bold text-ink">
                                        {{ number_format($note->score, 1) }} / {{ number_format($note->max_score, 0) }}
                                    </span>
                                    <span class="text-[8px] text-muted">{{ \Carbon\Carbon::parse($note->graded_at)->format('d M') }}</span>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @endif

                <a href="{{ route('student.grades') }}" class="mt-auto rounded-[5px] border border-line bg-white p-2.5 text-center text-[11px] font-semibold text-muted transition-colors hover:bg-slate-50">
                    Voir tous mes résultats
                </a>
            </article>
        </div>
    </section>
@endsection