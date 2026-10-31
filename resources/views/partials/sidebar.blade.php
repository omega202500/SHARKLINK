{{-- Fond sombre derrière le menu (mobile / tablette uniquement) --}}
<div id="sidebar-overlay" class="fixed inset-0 z-30 hidden bg-black/50 lg:hidden" aria-hidden="true"></div>

<aside id="sidebar"
       class="fixed inset-y-0 left-0 z-40 flex h-screen w-[270px] -translate-x-full flex-col overflow-y-auto bg-navy px-3.5 py-6 transition-transform duration-300 ease-in-out lg:static lg:h-auto lg:min-h-screen lg:shrink-0 lg:translate-x-0 lg:overflow-visible">
    <div class="flex items-center justify-between px-2 pb-[22px]">
        <a href="{{ route('dashboard') }}" class="flex items-center gap-[3px]">
            <img src="{{ asset('images/Shark-mark.svg') }}" alt="" class="h-[31px] w-[29px]">
            <span class="text-[21px] font-extrabold text-white">SHARKLINK</span>
        </a>
        <button type="button" class="text-white" aria-label="Réduire le menu" data-sidebar-close>
            <x-icon name="panel-left-close" :size="16" />
        </button>
    </div>

    <nav class="flex flex-col">
        @php
            $navigation = $navigation ?? [
                ['type' => 'heading', 'title' => 'Espace étudiant', 'action' => ''],
                ['type' => 'link', 'label' => 'Tableau de bord', 'icon' => 'layout-dashboard', 'href' => route('dashboard'), 'active' => request()->routeIs('dashboard')],
                ['type' => 'link', 'label' => 'Mon profil', 'icon' => 'user-round', 'href' => route('student.profile')],
                ['type' => 'link', 'label' => 'Mes cours', 'icon' => 'book-open', 'href' => route('student.courses'), 'chevron' => true],
                ['type' => 'link', 'label' => 'Mes notes', 'icon' => 'clipboard-list', 'href' => route('student.grades')],
                ['type' => 'link', 'label' => 'Emploi du temps', 'icon' => 'calendar-days', 'href' => route('student.timetable')],
                ['type' => 'heading', 'title' => 'Vie académique', 'action' => ''],
                ['type' => 'link', 'label' => 'Inscriptions', 'icon' => 'graduation-cap', 'href' => route('student.enrollments')],
                ['type' => 'link', 'label' => 'Compte financier', 'icon' => 'wallet', 'href' => route('student.finances')],
                ['type' => 'link', 'label' => 'Annonces', 'icon' => 'bell', 'href' => route('student.announcements')],
            ];
        @endphp
        @foreach ($navigation as $entry)
            @if ($entry['type'] === 'heading')
                <div class="flex items-start justify-between px-3 pb-2.5 pt-[22px]">
                    <p class="text-sm font-bold text-white">{{ $entry['title'] }}</p>
                    <a href="#" class="text-[10px] text-navy-soft">{{ $entry['action'] }}</a>
                </div>
            @else
                <a href="{{ $entry['href'] ?? '#' }}"
                   @class([
                       'flex items-center gap-[13px] rounded-[5px] py-[13px] text-[13px]',
                       'px-[22px]' => ! empty($entry['indent']),
                       'px-3' => empty($entry['indent']),
                       'bg-white font-bold text-navy' => ! empty($entry['active']),
                       'font-medium text-white' => empty($entry['active']),
                   ])
                   @if (! empty($entry['active'])) aria-current="page" @endif>
                    <x-icon :name="$entry['icon']" />
                    <span class="min-w-0 flex-1">{{ $entry['label'] }}</span>
                    @if (! empty($entry['chevron']))
                        <x-icon name="chevron-down" :size="12" />
                    @endif
                </a>
            @endif
        @endforeach
    </nav>
</aside>