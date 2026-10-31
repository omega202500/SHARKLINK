{{--
|--------------------------------------------------------------------------
| resources/views/dashboardPedagogy.blade.php
|--------------------------------------------------------------------------
| Dashboard pédagogique SHARKLINK (converti depuis Figma Make : React -> Blade + Tailwind CSS)
|
| Variables optionnelles (toutes ont une valeur par défaut, la vue fonctionne seule) :
|   $currentUser  : ['name','first_name','initials','role']
|   $activeItem   : libellé du menu actif (défaut : "Tableau de bord")
|   $navigation   : [['label','icon','route'], ...]
|   $semester     : ex. "Semestre 1 · 2026-2027"
|   $academicYear : ex. "2026-2027"
|   $kpis         : [['icon','label','value','helper'], ...]
|   $courses      : [['name','code','teacher','initials','date','status'], ...]
|                   status : "Publié" | "En attente" | "Délibéré"
|   $calendar     : [['day','month','title','meta'], ...]
|   $alertCount, $systemAlert : ['title','text']
|   $programs     : [['name','students','value'], ...]
|   $admissionGoal: objectif d'admission en % (défaut 80)
|
| Tailwind : seules des classes utilitaires + valeurs arbitraires sont utilisées
| (aucun tailwind.config nécessaire). Compatible Tailwind v3.2+ et v4.
--}}
@php
    /* ------------------------------------------------------------------ */
    /*  Données par défaut (à remplacer par celles du contrôleur)           */
    /* ------------------------------------------------------------------ */
    $currentUser   = $currentUser   ?? ['name' => 'Sophie Mendy', 'first_name' => 'Sophie', 'initials' => 'SM', 'role' => 'Resp. Pédagogique'];
    $activeItem    = $activeItem    ?? 'Tableau de bord';
    $academicYear  = $academicYear  ?? '2026-2027';
    $semester      = $semester      ?? 'Semestre 1 · ' . $academicYear;
    $admissionGoal = $admissionGoal ?? 80;
    $alertCount    = $alertCount    ?? 3;

    $navigation = $navigation ?? [
        ['label' => 'Tableau de bord',       'icon' => 'layout-dashboard',      'route' => 'pedagogy.dashboard'],
        ['label' => 'Délibérations & PV',    'icon' => 'clipboard-check',       'route' => 'pedagogy.deliberations'],
        ['label' => 'Validation des Notes',  'icon' => 'badge-check',           'route' => 'pedagogy.validation'],
        ['label' => 'Programmes & Filières', 'icon' => 'graduation-cap',        'route' => 'pedagogy.programs'],
        ['label' => 'Enseignants',           'icon' => 'users-round',           'route' => 'pedagogy.teachers'],
        ['label' => 'Rapports Académiques',  'icon' => 'chart-no-axes-combined','route' => 'pedagogy.reports'],
    ];

    $kpis = $kpis ?? [
        ['icon' => 'trending-up',   'label' => 'Taux de réussite',   'value' => '82%',     'helper' => '+4,2% ce mois'],
        ['icon' => 'book-open-check','label' => 'Cours à délibérer', 'value' => '05',      'helper' => '12 au total'],
        ['icon' => 'sigma',         'label' => 'Moyenne globale',    'value' => '12.4/20', 'helper' => '+0,8 point'],
        ['icon' => 'circle-alert',  'label' => 'Alertes critiques',  'value' => '03',      'helper' => 'À traiter'],
    ];

    $courses = $courses ?? [
        ['name' => 'Algorithmique avancée',    'code' => 'INFO · L3',    'teacher' => 'Dr. Amadou Diallo', 'initials' => 'AD', 'date' => '12 oct. 2026', 'status' => 'Publié'],
        ['name' => 'Droit des affaires',       'code' => 'DROIT · M1',   'teacher' => 'Pr. Fatou Ndiaye',  'initials' => 'FN', 'date' => '14 oct. 2026', 'status' => 'En attente'],
        ['name' => 'Comptabilité analytique',  'code' => 'GESTION · L2', 'teacher' => 'M. Ibrahima Sow',   'initials' => 'IS', 'date' => '10 oct. 2026', 'status' => 'Délibéré'],
        ['name' => 'Architecture logicielle',  'code' => 'INFO · M1',    'teacher' => 'Mme Aminata Ba',    'initials' => 'AB', 'date' => '15 oct. 2026', 'status' => 'En attente'],
    ];

    $calendar = $calendar ?? [
        ['day' => '15', 'month' => 'OCT', 'title' => 'Fin de saisie des notes L1', 'meta' => 'Toutes les filières'],
        ['day' => '20', 'month' => 'OCT', 'title' => 'Conseil de classe Info',     'meta' => 'Salle du conseil · 09:00'],
        ['day' => '24', 'month' => 'OCT', 'title' => 'Jury de délibération M1',    'meta' => 'Amphithéâtre A · 14:30'],
    ];

    $systemAlert = $systemAlert ?? [
        'title' => 'Retard de saisie détecté',
        'text'  => '3 enseignants n’ont pas finalisé la saisie des notes.',
    ];

    $programs = $programs ?? [
        ['name' => 'Informatique', 'students' => 186, 'value' => 88],
        ['name' => 'Droit',        'students' => 142, 'value' => 76],
        ['name' => 'Gestion',      'students' => 164, 'value' => 81],
    ];

    /* ------------------------------------------------------------------ */
    /*  Icônes Lucide (SVG inline) : {!! $icon('bell', 19) !!}              */
    /* ------------------------------------------------------------------ */
    $iconPaths = [
        'layout-dashboard'      => '<rect width="7" height="9" x="3" y="3" rx="1"/><rect width="7" height="5" x="14" y="3" rx="1"/><rect width="7" height="9" x="14" y="12" rx="1"/><rect width="7" height="5" x="3" y="16" rx="1"/>',
        'clipboard-check'       => '<rect width="8" height="4" x="8" y="2" rx="1" ry="1"/><path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"/><path d="m9 14 2 2 4-4"/>',
        'badge-check'           => '<path d="M3.85 8.62a4 4 0 0 1 4.78-4.77 4 4 0 0 1 6.74 0 4 4 0 0 1 4.78 4.78 4 4 0 0 1 0 6.74 4 4 0 0 1-4.77 4.78 4 4 0 0 1-6.75 0 4 4 0 0 1-4.78-4.77 4 4 0 0 1 0-6.76Z"/><path d="m16 9-5.5 5.5L8 12"/>',
        'graduation-cap'        => '<path d="M21.42 10.922a1 1 0 0 0-.019-1.838L12.83 5.18a2 2 0 0 0-1.66 0L2.6 9.08a1 1 0 0 0 0 1.832l8.57 3.908a2 2 0 0 0 1.66 0z"/><path d="M22 10v6"/><path d="M6 12.5V16a6 3 0 0 0 12 0v-3.5"/>',
        'users-round'           => '<path d="M18 21a8 8 0 0 0-16 0"/><circle cx="10" cy="8" r="5"/><path d="M22 20c0-3.37-2-6.5-4-8a5 5 0 0 0-.45-8.3"/>',
        'chart-no-axes-combined'=> '<path d="M12 16v5"/><path d="M16 14.639V21"/><path d="M20 10.656V21"/><path d="m22 3-8.646 8.646a.5.5 0 0 1-.708 0L9.354 8.354a.5.5 0 0 0-.707 0L2 15"/><path d="M4 18.463V21"/><path d="M8 14.656V21"/>',
        'fish'                  => '<path d="M6.5 12c.94-3.46 4.94-6 8.5-6 3.56 0 6.06 2.54 7 6-.94 3.47-3.44 6-7 6s-7.56-2.53-8.5-6Z"/><path d="M18 12v.5"/><path d="M16 17.93a9.77 9.77 0 0 1 0-11.86"/><path d="M7 10.67C7 8 5.58 5.97 2.73 5.5c-1 1.5-1 5 .23 6.5-1.24 1.5-1.24 5-.23 6.5C5.58 18.03 7 16 7 13.33"/><path d="M10.46 7.26C10.2 5.88 9.17 4.24 8 3h5.8a2 2 0 0 1 1.98 1.67l.23 1.4"/><path d="m16.01 17.93-.23 1.4A2 2 0 0 1 13.8 21H9.5a5.96 5.96 0 0 0 1.49-3.98"/>',
        'book-open-check'       => '<path d="M12 5v16"/><path d="m16 12 2 2 4-4"/><path d="M22 6V5a2 2 0 00-1.999-2L16 3.002A5 5 0 0012 5a5 5 0 00-4-2H4a2 2 0 00-2 2v12a2 2 0 001.999 2H8a5 5 0 014 2 5 5 0 014-2h4.001A2 2 0 0022 17v-1.344"/>',
        'chevron-right'         => '<path d="m9 18 6-6-6-6"/>',
        'menu'                  => '<path d="M4 5h16"/><path d="M4 12h16"/><path d="M4 19h16"/>',
        'x'                     => '<path d="M18 6 6 18"/><path d="m6 6 12 12"/>',
        'search'                => '<path d="m21 21-4.34-4.34"/><circle cx="11" cy="11" r="8"/>',
        'bell'                  => '<path d="M10.268 21a2 2 0 0 0 3.464 0"/><path d="M3.262 15.326A1 1 0 0 0 4 17h16a1 1 0 0 0 .74-1.673C19.41 13.956 18 12.499 18 8A6 6 0 0 0 6 8c0 4.499-1.411 5.956-2.738 7.326"/>',
        'calendar-days'         => '<path d="M8 2v3"/><path d="M16 2v3"/><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M3 9h18"/><path d="M8 13h.01"/><path d="M12 13h.01"/><path d="M16 13h.01"/><path d="M8 17h.01"/><path d="M12 17h.01"/><path d="M16 17h.01"/>',
        'trending-up'           => '<path d="M16 7h6v6"/><path d="m22 7-8.5 8.5-5-5L2 17"/>',
        'sigma'                 => '<path d="M18 7V5a1 1 0 0 0-1-1H6.5a.5.5 0 0 0-.4.8l4.5 6a2 2 0 0 1 0 2.4l-4.5 6a.5.5 0 0 0 .4.8H17a1 1 0 0 0 1-1v-2"/>',
        'circle-alert'          => '<circle cx="12" cy="12" r="10"/><line x1="12" x2="12" y1="8" y2="12"/><line x1="12" x2="12.01" y1="16" y2="16"/>',
        'sliders-horizontal'    => '<path d="M10 5H3"/><path d="M12 19H3"/><path d="M14 3v4"/><path d="M16 17v4"/><path d="M21 12h-9"/><path d="M21 19h-5"/><path d="M21 5h-7"/><path d="M8 10v4"/><path d="M8 12H3"/>',
        'arrow-up-right'        => '<path d="M7 7h10v10"/><path d="M7 17 17 7"/>',
        'download'              => '<path d="M12 15V3"/><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><path d="m7 10 5 5 5-5"/>',
        'clock-3'               => '<circle cx="12" cy="12" r="10"/><path d="M12 6v6h4"/>',
        'triangle-alert'        => '<path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3"/><path d="M12 9v4"/><path d="M12 17h.01"/>',
    ];

    $icon = function (string $name, int $size = 19, float $stroke = 1.8, string $class = '') use ($iconPaths) {
        return '<svg xmlns="http://www.w3.org/2000/svg" width="' . $size . '" height="' . $size . '" viewBox="0 0 24 24"'
            . ' fill="none" stroke="currentColor" stroke-width="' . $stroke . '" stroke-linecap="round" stroke-linejoin="round"'
            . ' aria-hidden="true" class="shrink-0 ' . $class . '">' . ($iconPaths[$name] ?? '') . '</svg>';
    };

    /* ------------------------------------------------------------------ */
    /*  Classes réutilisées                                                 */
    /* ------------------------------------------------------------------ */
    $card = 'rounded-[14px] border border-[#e3edf3] bg-white shadow-[0_1px_2px_rgba(11,31,58,0.05),0_8px_28px_rgba(11,31,58,0.04)]';

    $iconButton = 'grid h-10 w-10 shrink-0 cursor-pointer place-items-center rounded-[10px] border border-[#e3edf3] bg-white text-[#5f6f84] transition hover:border-[#22d3ee] hover:bg-[#f4fbff] hover:text-[#0b1f3a]';

    $secondaryButton = 'inline-flex cursor-pointer items-center justify-center gap-[7px] rounded-lg border border-[#e3edf3] bg-white px-[11px] py-2 text-[10px] font-semibold text-[#0b1f3a] transition hover:border-[#22d3ee] hover:bg-[#22d3ee]/[0.07]';

    $statusClasses = [
        'Publié'     => 'bg-[#22d3ee]/10 text-[#0b1f3a]',
        'En attente' => 'border border-[#e3edf3] bg-[#f4fbff] text-[#5f6f84]',
        'Délibéré'   => 'bg-[#0b1f3a] text-white',
    ];
@endphp
<!DOCTYPE html>
<html lang="fr" class="bg-[#f4fbff]">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Espace Pédagogique · SHARKLINK</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    {{-- Option 1 (par défaut) : Tailwind Play CDN, pratique pour tester rapidement. --}}
    <script src="https://cdn.tailwindcss.com/3.4.17"></script>

    {{-- Option 2 (production) : décommentez @vite et supprimez le script CDN ci-dessus.
         Assurez-vous que resources/css/app.css contient bien Tailwind. --}}
    {{-- @vite(['resources/css/app.css', 'resources/js/app.js']) --}}
</head>

<body class="min-h-screen min-w-[320px] bg-[#f4fbff] font-['Inter',sans-serif] text-[#0b1f3a] antialiased">
<div class="min-h-screen">

    {{-- ============================== SIDEBAR ============================== --}}
    <button type="button" id="sidebarBackdrop" aria-label="Fermer le menu"
            class="fixed inset-0 z-[35] hidden border-0 bg-[#0b1f3a]/50 backdrop-blur-[2px]"></button>

    <aside id="sidebar"
           class="fixed inset-y-0 left-0 z-40 flex w-[264px] -translate-x-full flex-col overflow-y-auto bg-[#0b1f3a] px-5 pb-5 pt-[30px] text-white transition-transform duration-200 min-[861px]:translate-x-0">

        <div class="flex items-center justify-between px-2.5">
            <div class="flex items-center gap-[11px]" aria-label="SHARKLINK">
                <a href="{{ route('dashboard') }}" style="display: flex; align-items: center; gap: 10px; text-decoration: none;">
                    <img src="{{ asset('images/shark-mark-dark.svg') }}" alt="SHARKLINK" 
                        style="height: 30px; width: auto; filter: brightness(0) invert(1);" />
                    <span style="font-size: 18px; font-weight: 800; color: var(--cyan); letter-spacing: 0.05em;">SHARKLINK</span>
                </a>
            </div>
            <button type="button" id="sidebarClose" aria-label="Fermer le menu"
                    class="grid h-10 w-10 shrink-0 cursor-pointer place-items-center rounded-[10px] border border-white/10 bg-transparent text-white min-[861px]:hidden">
                {!! $icon('x', 19) !!}
            </button>
        </div>

        <nav class="mt-[52px] flex flex-col gap-[5px]" aria-label="Navigation principale">
            <p class="mb-2.5 px-[13px] text-[10px] font-bold tracking-[0.13em] text-white/40">MENU PRINCIPAL</p>

            @foreach ($navigation as $item)
                @php $active = $activeItem === $item['label']; @endphp
                <a href="{{ isset($item['route']) ? route($item['route']) : '#' }}"
                   @if($active) aria-current="page" @endif
                   class="relative flex w-full items-center gap-[13px] rounded-[10px] border px-[13px] py-3 text-left text-[13px] font-medium transition duration-150
                          {{ $active
                              ? 'border-[#22d3ee]/[0.13] bg-[#22d3ee]/10 text-[#22d3ee]'
                              : 'border-transparent text-white/70 hover:translate-x-0.5 hover:bg-white/5 hover:text-white' }}">
                    {!! $icon($item['icon'], 19, 1.6) !!}
                    <span>{{ $item['label'] }}</span>
                    @if ($active)
                        <span class="absolute -right-px h-[23px] w-[3px] rounded-l bg-[#22d3ee]"></span>
                    @endif
                </a>
            @endforeach
        </nav>

        <a href="#" class="mt-auto flex items-center gap-[11px] rounded-xl border border-[#22d3ee]/[0.14] bg-[#22d3ee]/[0.06] p-[13px] text-white/45">
            <span class="grid h-[34px] w-[34px] flex-none place-items-center rounded-[9px] bg-[#22d3ee]/10 text-[#22d3ee]">
                {!! $icon('book-open-check', 20, 1.7) !!}
            </span>
            <div class="flex min-w-0 flex-1 flex-col">
                <strong class="text-[11px] font-semibold text-white">Besoin d’aide ?</strong>
                <span class="mt-0.5 text-[10px]">Consulter le guide</span>
            </div>
            {!! $icon('chevron-right', 16, 2) !!}
        </a>

        <div class="mt-[17px] flex items-center gap-2.5 border-t border-white/10 px-1 pb-0.5 pt-[18px] text-white/45">
            <div class="grid h-[38px] w-[38px] flex-none place-items-center rounded-full bg-[#22d3ee] text-xs font-bold text-[#0b1f3a] shadow-[0_0_0_3px_rgba(34,211,238,0.12)]">
                {{ $currentUser['initials'] }}
            </div>
            <div class="flex min-w-0 flex-1 flex-col">
                <strong class="text-xs font-semibold text-white">{{ $currentUser['name'] }}</strong>
                <span class="mt-0.5 text-[10px]">{{ $currentUser['role'] }}</span>
            </div>
            {!! $icon('chevron-right', 16, 2) !!}
        </div>
    </aside>

    {{-- ============================== CONTENU ============================== --}}
    <div class="min-w-0 min-[861px]:ml-[264px]">

        {{-- ---------- Header ---------- --}}
        <header class="sticky top-0 z-30 flex min-h-[70px] items-center justify-between border-b border-[#e3edf3] bg-white/95 px-5 backdrop-blur-md min-[861px]:min-h-[78px] min-[861px]:px-8">
            <div class="flex items-center">
                <button type="button" id="sidebarOpen" aria-label="Ouvrir le menu"
                        class="{{ $iconButton }} mr-[13px] min-[861px]:hidden">
                    {!! $icon('menu', 19) !!}
                </button>
                <div>
                    <p class="text-[17px] font-bold tracking-[-0.02em]">Espace Pédagogique</p>
                    <p class="mt-[3px] hidden text-[11px] text-[#5f6f84] min-[661px]:block">
                        Université SHARKLINK · Année {{ $academicYear }}
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-3">
                <label class="hidden h-[41px] w-[265px] items-center gap-[9px] rounded-[10px] border border-[#e3edf3] bg-[#f4fbff] pl-[13px] pr-2.5 text-[#5f6f84] transition focus-within:border-[#22d3ee] focus-within:ring-[3px] focus-within:ring-[#22d3ee]/[0.12] min-[661px]:flex">
                    {!! $icon('search', 18) !!}
                    <input id="courseSearch" type="search" autocomplete="off"
                           placeholder="Rechercher un cours..." aria-label="Rechercher un cours"
                           class="min-w-0 flex-1 border-0 bg-transparent text-xs text-[#0b1f3a] outline-none placeholder:text-[#5f6f84]/70 focus:ring-0">
                    <kbd class="rounded-[5px] border border-[#e3edf3] bg-white px-[5px] py-0.5 font-['Inter',sans-serif] text-[9px] text-[#5f6f84]">⌘ K</kbd>
                </label>

                <button type="button" aria-label="Notifications, {{ $alertCount }} non lues" class="{{ $iconButton }} relative">
                    {!! $icon('bell', 19) !!}
                    <span class="absolute right-2 top-2 h-[7px] w-[7px] rounded-full border-2 border-white bg-[#22d3ee]"></span>
                </button>

                <div class="mx-0.5 hidden h-[27px] w-px bg-[#e3edf3] min-[661px]:block"></div>

                <div class="grid h-[38px] w-[38px] flex-none place-items-center rounded-full bg-[#22d3ee] text-xs font-bold text-[#0b1f3a]">
                    {{ $currentUser['initials'] }}
                </div>
            </div>
        </header>

        {{-- ---------- Main ---------- --}}
        <main class="mx-auto w-full max-w-[1536px] px-5 pb-[46px] pt-[23px] min-[661px]:pt-[30px] min-[861px]:px-8">

            {{-- Bienvenue --}}
            <div class="mb-[25px] flex flex-col items-start justify-between min-[661px]:flex-row min-[661px]:items-end">
                <div>
                    <p class="mb-2 text-[10px] font-extrabold tracking-[0.14em] text-[#22d3ee]">VUE D’ENSEMBLE</p>
                    <h1 class="text-[22px] font-bold leading-[1.2] tracking-[-0.035em] min-[661px]:text-[25px]">
                        Bonjour {{ $currentUser['first_name'] }},
                    </h1>
                    <p class="mt-[7px] text-xs text-[#5f6f84]">
                        Voici la situation académique de votre établissement aujourd’hui.
                    </p>
                </div>
                <div class="mt-[17px] flex items-center gap-2 rounded-[9px] border border-[#e3edf3] bg-white px-[13px] py-2.5 text-[11px] font-medium text-[#5f6f84] shadow-[0_1px_2px_rgba(11,31,58,0.05),0_8px_28px_rgba(11,31,58,0.04)] min-[661px]:mt-0">
                    {!! $icon('calendar-days', 16, 1.8, 'text-[#22d3ee]') !!}
                    {{ $semester }}
                </div>
            </div>

            {{-- KPI --}}
            <section class="grid grid-cols-1 gap-3 min-[661px]:grid-cols-2 min-[661px]:gap-[18px] min-[1161px]:grid-cols-4" aria-label="Indicateurs clés">
                @foreach ($kpis as $kpi)
                    <article class="{{ $card }} relative grid grid-cols-[43px_1fr_auto] items-center gap-x-[11px] overflow-hidden p-[15px] transition duration-150 after:absolute after:right-0 after:top-0 after:h-[54px] after:w-[54px] after:rounded-bl-[54px] after:bg-[#22d3ee]/5 after:content-[''] hover:-translate-y-0.5 hover:shadow-[0_10px_32px_rgba(11,31,58,0.08)] min-[661px]:block min-[661px]:px-5 min-[661px]:pb-[18px] min-[661px]:pt-[19px]">
                        <div class="contents min-[661px]:flex min-[661px]:items-center min-[661px]:justify-between">
                            <span class="col-start-1 row-span-2 row-start-1 grid h-[39px] w-[39px] place-items-center rounded-[10px] bg-[#22d3ee]/[0.12] text-[#22d3ee]">
                                {!! $icon($kpi['icon'], 21) !!}
                            </span>
                            <span class="relative z-[1] col-start-3 row-span-2 row-start-1 rounded-full bg-[#f4fbff] px-2 py-[5px] text-[9px] font-semibold text-[#5f6f84]">
                                {{ $kpi['helper'] }}
                            </span>
                        </div>
                        <p class="col-start-2 row-start-1 text-xl font-bold leading-none tracking-[-0.04em] min-[661px]:mt-[15px] min-[661px]:text-[25px]">{{ $kpi['value'] }}</p>
                        <p class="col-start-2 row-start-2 -mt-2.5 text-[11px] font-medium text-[#5f6f84] min-[661px]:mt-2">{{ $kpi['label'] }}</p>
                    </article>
                @endforeach
            </section>

            {{-- Délibérations + Agenda --}}
            <div class="mt-6 grid grid-cols-1 gap-6 min-[1161px]:grid-cols-[minmax(0,2fr)_minmax(290px,0.88fr)]">

                {{-- Délibérations --}}
                <section class="{{ $card }}">
                    <div class="flex items-center justify-between gap-[18px] border-b border-[#e3edf3] px-[17px] pb-[17px] pt-[17px] min-[661px]:px-[22px] min-[661px]:pt-5">
                        <div>
                            <p class="text-sm font-bold tracking-[-0.02em]">État d’avancement des délibérations</p>
                            <p class="mt-[5px] text-[10px] text-[#5f6f84]">Suivi des cours publiés et en attente de validation</p>
                        </div>
                        <button type="button" class="{{ $secondaryButton }}">
                            {!! $icon('sliders-horizontal', 16) !!}
                            Filtrer
                        </button>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full border-collapse text-left">
                            <thead class="bg-[#f4fbff]/70">
                                <tr>
                                    @foreach (['Nom du cours', 'Enseignant', 'Date publication', 'Statut'] as $th)
                                        <th class="whitespace-nowrap px-[13px] py-[11px] text-[9px] font-bold uppercase tracking-[0.04em] text-[#5f6f84] first:pl-[17px] min-[661px]:first:pl-[22px]">{{ $th }}</th>
                                    @endforeach
                                    <th aria-label="Action" class="px-[13px] py-[11px] pr-[22px]"></th>
                                </tr>
                            </thead>
                            <tbody id="coursesBody">
                                @foreach ($courses as $course)
                                    <tr data-search="{{ \Illuminate\Support\Str::lower($course->name . ' ' . $course->teacher . ' ' . $course->code . ' ' . $course->status) }}"
                                        class="transition-colors duration-150 hover:bg-[#f4fbff]/60">
                                        <td class="whitespace-nowrap border-t border-[#e3edf3] p-[13px] pl-[17px] align-middle text-[10px] min-[661px]:pl-[22px]">
                                            <div class="text-[11px] font-semibold">{{ $course->name }}</div>
                                            <div class="mt-1 text-[8px] font-semibold tracking-[0.05em] text-[#5f6f84]">{{ $course->code }}</div>
                                        </td>
                                        <td class="whitespace-nowrap border-t border-[#e3edf3] p-[13px] align-middle text-[10px]">
                                            <div class="flex items-center gap-2 text-[#5f6f84]">
                                                <span class="grid h-[26px] w-[26px] place-items-center rounded-[7px] bg-[#f4fbff] text-[8px] font-bold text-[#0b1f3a]">{{ $course->initials }}</span>
                                                <span>{{ $course->teacher }}</span>
                                            </div>
                                        </td>
                                        <td class="whitespace-nowrap border-t border-[#e3edf3] p-[13px] align-middle text-[10px] text-[#5f6f84]">{{ $course->date }}</td>
                                        <td class="whitespace-nowrap border-t border-[#e3edf3] p-[13px] align-middle text-[10px]">
                                            <span class="inline-flex items-center gap-1.5 rounded-full px-2 py-[5px] text-[9px] font-semibold {{ $statusClasses[$course->status] ?? $statusClasses['En attente'] }}">
                                                <span class="h-[5px] w-[5px] rounded-full bg-current"></span>
                                                {{ $course->status }}
                                            </span>
                                        </td>
                                        <td class="whitespace-nowrap border-t border-[#e3edf3] p-[13px] pr-[22px] align-middle text-[10px]">
                                            <button type="button"
                                                    class="inline-flex min-w-[72px] cursor-pointer items-center justify-center gap-[7px] rounded-[7px] border border-[#22d3ee]/50 bg-white px-[9px] py-[7px] text-[10px] font-semibold text-[#0b1f3a] transition hover:border-[#22d3ee] hover:bg-[#22d3ee]/[0.07]">
                                                {{ $course->status === 'En attente' ? 'Lancer' : 'Consulter' }}
                                                {!! $icon('arrow-up-right', 15, 1.8, 'text-[#22d3ee]') !!}
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>

                        <div id="coursesEmpty" class="{{ count($courses) ? 'hidden' : '' }} p-[34px] text-center text-[11px] text-[#5f6f84]">
                            Aucun cours ne correspond à votre recherche.
                        </div>
                    </div>

                    <button type="button"
                            class="inline-flex w-full cursor-pointer items-center justify-center gap-[7px] rounded-b-[14px] border-0 border-t border-[#e3edf3] bg-white p-[13px] text-[10px] font-semibold text-[#5f6f84] transition hover:bg-[#f4fbff] hover:text-[#0b1f3a]">
                        Voir toutes les délibérations
                        {!! $icon('chevron-right', 16, 2) !!}
                    </button>
                </section>

                {{-- Agenda + alertes --}}
                <aside class="{{ $card }} grid min-w-0 grid-cols-1 min-[661px]:grid-cols-2 min-[1161px]:block">
                    <div class="col-span-full flex items-center justify-between gap-[18px] border-b border-[#e3edf3] px-[17px] pb-4 pt-[17px] min-[661px]:px-[22px] min-[661px]:pt-5">
                        <div>
                            <p class="text-sm font-bold tracking-[-0.02em]">Calendrier Académique</p>
                            <p class="mt-[5px] text-[10px] text-[#5f6f84]">Prochaines échéances</p>
                        </div>
                        <span class="grid h-[37px] w-[37px] place-items-center rounded-[9px] bg-[#22d3ee]/[0.12] text-[#22d3ee]">
                            {!! $icon('calendar-days', 19) !!}
                        </span>
                    </div>

                    <div class="flex flex-col px-5 pb-1 pt-1.5">
                        @foreach ($calendar as $event)
                            <div class="flex items-center gap-[13px] border-b border-[#e3edf3] py-[13px] last:border-b-0">
                                <div class="flex h-12 w-[43px] flex-none flex-col items-center justify-center rounded-[9px] border border-[#22d3ee]/30 bg-[#22d3ee]/[0.07]">
                                    <strong class="text-base leading-none">{{ $event['day'] }}</strong>
                                    <span class="mt-[5px] text-[7px] font-extrabold tracking-[0.07em] text-[#22d3ee]">{{ $event['month'] }}</span>
                                </div>
                                <div class="flex min-w-0 flex-col">
                                    <strong class="truncate text-[10px] font-semibold">{{ $event['title'] }}</strong>
                                    <span class="mt-1.5 flex items-center gap-[5px] text-[8px] text-[#5f6f84]">
                                        {!! $icon('clock-3', 13, 1.8, 'text-[#22d3ee]') !!}
                                        {{ $event['meta'] }}
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="border-t border-[#e3edf3] px-5 pb-5 pt-4 min-[661px]:border-l min-[661px]:border-t-0 min-[1161px]:border-l-0 min-[1161px]:border-t">
                        <div class="flex items-center justify-between text-[9px] font-bold uppercase tracking-[0.05em] text-[#5f6f84]">
                            <span>Alertes systèmes</span>
                            <span class="grid h-5 w-5 place-items-center rounded-full bg-[#0b1f3a] text-[8px] text-white">{{ $alertCount }}</span>
                        </div>
                        <div class="mt-[11px] flex gap-2.5 rounded-[9px] border-l-[3px] border-[#22d3ee] bg-[#f4fbff] p-3">
                            {!! $icon('triangle-alert', 19, 1.8, 'text-[#22d3ee]') !!}
                            <div>
                                <strong class="text-[10px] font-semibold">{{ $systemAlert['title'] }}</strong>
                                <p class="mt-1 text-[8px] leading-normal text-[#5f6f84]">{{ $systemAlert['text'] }}</p>
                                <button type="button" class="mt-[7px] cursor-pointer text-[8px] font-bold text-[#0b1f3a] underline decoration-[#22d3ee] underline-offset-[3px]">
                                    Voir les détails
                                </button>
                            </div>
                        </div>
                    </div>
                </aside>
            </div>

            {{-- Performance par filière --}}
            <section class="{{ $card }} mt-6">
                <div class="flex items-center justify-between gap-[18px] border-b border-[#e3edf3] px-[17px] pb-[17px] pt-[17px] min-[661px]:px-[22px] min-[661px]:pt-5">
                    <div>
                        <p class="text-sm font-bold tracking-[-0.02em]">Performance par Filière</p>
                        <p class="mt-[5px] text-[10px] text-[#5f6f84]">Taux d’admission pour le semestre en cours</p>
                    </div>
                    <button type="button" class="{{ $secondaryButton }}">
                        {!! $icon('download', 16) !!}
                        Exporter
                    </button>
                </div>

                <div class="grid grid-cols-1 p-[18px] min-[661px]:grid-cols-3 min-[661px]:p-[22px]">
                    @foreach ($programs as $program)
                        <article class="border-b border-[#e3edf3] py-[17px] first:pt-0 last:border-b-0 last:pb-0 min-[661px]:border-b-0 min-[661px]:border-r min-[661px]:px-6 min-[661px]:py-px min-[661px]:first:pl-0 min-[661px]:last:border-r-0 min-[661px]:last:pr-0">
                            <div class="flex items-start justify-between">
                                <div class="flex flex-col">
                                    <strong class="text-[11px] font-semibold">{{ $program->name }}</strong>
                                    <span class="mt-1 text-[8px] text-[#5f6f84]">{{ $program->students }} étudiants inscrits</span>
                                </div>
                                <strong class="text-[17px] font-semibold tracking-[-0.03em] text-[#0b1f3a]">{{ $program->value }}%</strong>
                            </div>

                            <div class="mt-[17px] h-[7px] overflow-hidden rounded-full bg-[#e3edf3]"
                                 role="progressbar" aria-label="Taux d’admission en {{ $program->name }}"
                                 aria-valuenow="{{ $program->value }}" aria-valuemin="0" aria-valuemax="100">
                                <span class="block h-full rounded-full bg-[#22d3ee]" style="width: {{ $program->value }}%"></span>
                            </div>

                            <div class="mt-2 flex justify-between text-[8px] text-[#5f6f84]">
                                <span>Taux d’admission</span>
                                <span>Objectif : {{ $admissionGoal }}%</span>
                            </div>
                        </article>
                    @endforeach
                </div>
            </section>
        </main>
    </div>
</div>

<script>
    (function () {
        /* --- Sidebar mobile --- */
        var sidebar  = document.getElementById('sidebar');
        var backdrop = document.getElementById('sidebarBackdrop');

        function openSidebar() {
            sidebar.classList.remove('-translate-x-full');
            sidebar.classList.add('translate-x-0');
            backdrop.classList.remove('hidden');
        }
        function closeSidebar() {
            sidebar.classList.add('-translate-x-full');
            sidebar.classList.remove('translate-x-0');
            backdrop.classList.add('hidden');
        }

        document.getElementById('sidebarOpen').addEventListener('click', openSidebar);
        document.getElementById('sidebarClose').addEventListener('click', closeSidebar);
        backdrop.addEventListener('click', closeSidebar);
        sidebar.querySelectorAll('nav a').forEach(function (a) { a.addEventListener('click', closeSidebar); });
        document.addEventListener('keydown', function (e) { if (e.key === 'Escape') closeSidebar(); });

        /* --- Recherche de cours (filtre côté client) + raccourci ⌘K / Ctrl+K --- */
        var input = document.getElementById('courseSearch');
        var rows  = document.querySelectorAll('#coursesBody tr');
        var empty = document.getElementById('coursesEmpty');

        function filterCourses() {
            var q = input.value.trim().toLocaleLowerCase('fr');
            var visible = 0;
            rows.forEach(function (row) {
                var match = !q || row.dataset.search.indexOf(q) !== -1;
                row.classList.toggle('hidden', !match);
                if (match) visible++;
            });
            empty.classList.toggle('hidden', visible > 0);
        }

        input.addEventListener('input', filterCourses);
        document.addEventListener('keydown', function (e) {
            if ((e.metaKey || e.ctrlKey) && e.key.toLowerCase() === 'k') {
                e.preventDefault();
                input.focus();
            }
        });
    })();
</script>
</body>
</html>