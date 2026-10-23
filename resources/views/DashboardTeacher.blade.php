{{--
    Tableau de bord enseignant — SHARKLINK (Tailwind CSS)
    Charte : bleu profond, cyan et blanc (technologie, confiance, connectivité).

    Variables attendues (envoyées par le contrôleur) :
      $user              : utilisateur connecté (name)
      $etablissementNom  : nom de l'établissement actif
      $stats             : [cours, etudiants, notes_en_attente, presence_moyenne]
      $emploi            : liste [debut, fin, titre, lieu, statut]   statut = 'en_cours' | 'termine' | null
      $devoirs           : liste [titre, details, badge]            badge = 'À corriger' | null
      $activites         : liste [titre, details]

    Icônes à placer dans public/images/ (SVG Lucide, une seule couleur) : layout-dashboard,
    book-open, clipboard-pen, calendar-days, user-round-check, file-text, megaphone,
    chart-no-axes-column, file-plus, search, bell.

    Tailwind : cette vue charge le CDN « Play » pour fonctionner tout de suite.
    En production, remplace le bloc <script> Tailwind par @vite(['resources/css/app.css'])
    et déclare les couleurs « brand » dans ta config Tailwind.
--}}
@php
    $initiales = mb_strtoupper(
        collect(preg_split('/\s+/', trim($user->name ?? 'Utilisateur')))
            ->filter()
            ->map(fn ($mot) => mb_substr($mot, 0, 1))
            ->take(2)
            ->join('')
    );

    // ✅ 1. La variable $fa était manquante, on la remet
    $fa = 'fa-solid';

    // ✅ 2. Noms d'icônes corrigés pour Font Awesome (au lieu des noms Lucide)
    $menu = [
        ['label' => 'Tableau de bord', 'icone' => 'gauge-high',         'route' => 'dashboard'],
        ['label' => 'Mes Cours',       'icone' => 'book-open',          'route' => 'teacher.courses'],
        ['label' => 'Saisie Notes',    'icone' => 'pen-to-square',      'route' => 'teacher.grades'],
        ['label' => 'Emploi du Temps', 'icone' => 'calendar-days',      'route' => 'teacher.timetable'],
        ['label' => 'Présence',        'icone' => 'user-check',         'route' => 'teacher.attendance'],
        ['label' => 'Devoirs',         'icone' => 'file-lines',         'route' => 'teacher.assignments'],
        ['label' => 'Annonces',        'icone' => 'bullhorn',           'route' => 'teacher.announcements'],
        ['label' => 'Rapports',        'icone' => 'chart-column',       'route' => 'teacher.reports'],
    ];

   $actions = [
        ['label' => 'Prendre Présence', 'icone' => 'user-check',         'href' => route('teacher.attendance')],
        ['label' => 'Saisir Notes',     'icone' => 'pen-to-square',      'href' => route('teacher.grades')],
        ['label' => 'Créer Devoir',     'icone' => 'file-circle-plus',   'href' => route('teacher.assignments')],
        ['label' => 'Voir Rapports',    'icone' => 'chart-column',       'href' => route('teacher.reports')],
    ];

    // Classes réutilisées
    $carte       = 'min-w-0 rounded-xl border border-brand-trait bg-white';
    $badge       = 'whitespace-nowrap rounded-full px-[11px] py-[5px] text-xs font-medium';
    $badgeRouge  = 'bg-[#fde6e6] text-[#c0392b]';
    $badgeVert   = 'bg-[#e3f7ec] text-[#16845b]';
    $badgeGris   = 'bg-[#eef1f5] text-brand-gris';
    $badgeOrange = 'bg-[#fdf0d5] text-[#b45309]';

    $lienBase    = 'flex h-[42px] shrink-0 items-center gap-3.5 rounded-lg border px-4 text-sm transition-colors md:h-[46px] md:text-[15px]';
    $lienActif   = 'border-brand-cyan/30 bg-brand-cyan/10 text-brand-cyan';
    $lienInactif = 'border-transparent hover:bg-white/5 hover:text-white';

    $statuts = [
        'en_cours' => ['En cours', $badgeVert],
        'termine'  => ['Terminé',  $badgeGris],
    ];
@endphp
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Tableau de bord Enseignant — SHARKLINK</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    {{-- ✅ LIEN FONT AWESOME FIABLE ET SÉCURISÉ --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <style>
        /* Menu mobile responsive */
        .sidebar-mobile {
            transform: translateX(-100%);
            transition: transform 0.3s ease-in-out;
            height: 100vh;
            overflow-y: auto;
        }
        .sidebar-mobile::-webkit-scrollbar {
            width: 0px;
            background: transparent;
        }
        .sidebar-mobile.open {
            transform: translateX(0);
        }
        .overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.5);
            z-index: 40;
        }
        .overlay.active {
            display: block;
        }
        @media (min-width: 768px) {
            .sidebar-mobile { transform: none !important; }
            .overlay { display: none !important; }
        }
    </style>

    {{-- Tailwind (CDN Play, à remplacer par @vite en production) --}}
    <script src="https://cdn.tailwindcss.com/3.4.17"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: {
                            nuit: '#0b1f3a',
                            cyan: '#22d3ee',
                            pale: '#d9f6fc',
                            turquoise: '#087e96',
                            fond: '#f4fbff',
                            gris: '#5f6f84',
                            trait: '#e3edf3',
                        },
                    },
                    fontFamily: {
                        sans: ['Inter', 'Arial', 'sans-serif'],
                    },
                },
            },
        }
    </script>
</head>
<body class="flex min-h-screen flex-col bg-brand-fond font-sans text-sm text-brand-nuit md:flex-row">

    {{-- Overlay pour fermer le menu en cliquant à l'extérieur --}}
    <div id="sidebar-overlay" class="overlay" onclick="toggleSidebar()"></div>
    {{-- ===================== Barre latérale ===================== --}}
    <aside id="sidebar" class="sidebar-mobile fixed top-0 left-0 z-50 h-screen w-[260px] bg-brand-nuit text-[#d3dcea] overflow-y-auto md:static md:h-auto md:overflow-visible">
        <div class="flex flex-col gap-3 p-4 md:gap-8 md:p-[17px] md:py-8">
            
            {{-- Logo --}}
        <div class="flex items-center justify-between px-4">
            <div class="flex items-center gap-1">
                <img src="{{ asset('images/shark-mark-dark.svg') }}"
                    alt=""
                    style="height: 30px; width: auto; filter: brightness(0) invert(1);" />

                <span style="font-size: 18px; font-weight: 800; color: var(--cyan); letter-spacing: 0.05em;">
                    SHARKLINK
                </span>
            </div>

            <button type="button" data-sidebar-close
                    class="text-[#d3dcea] hover:text-white md:hidden"
                    aria-label="Fermer le menu">
                <i class="fa-solid fa-xmark text-xl"></i>
            </button>
        </div>
        {{-- Navigation --}}
        <nav class="flex flex-col gap-1.5">
            @foreach ($menu as $item)
                @php
                    $actif = $item['route'] && request()->routeIs($item['route']);
                    $lien  = $item['route'] && \Illuminate\Support\Facades\Route::has($item['route'])
                                ? route($item['route'])
                                : '#';
                @endphp
                <a href="{{ $lien }}"
                class="{{ $lienBase }} {{ $actif ? $lienActif : $lienInactif }}"
                @if ($actif) aria-current="page" @endif>
                    
                    {{-- ✅ C'est ici que l'icône est générée correctement --}}
                    <i class="{{ $fa }} fa-{{ $item['icone'] }} w-5 shrink-0 text-center text-[17px]"></i>
                    
                    <span>{{ $item['label'] }}</span>
                </a>
            @endforeach
        </nav>
    </aside>

    <div class="min-w-0 flex-1">

        {{-- ===================== En-tête ===================== --}}
        <header class="flex h-[72px] items-center justify-between gap-4 border-b border-brand-trait bg-white px-4 md:px-9">
            <div class="flex items-center gap-3">
                <button onclick="toggleSidebar()" class="md:hidden text-brand-nuit">
                    <i class="fa-solid fa-bars text-xl"></i>
                </button>
                <div class="min-w-0">
                    <h1 class="truncate text-lg font-bold">Bonjour {{ $user->name }}</h1>
                    <p class="mt-[3px] truncate text-xs text-brand-gris">Enseignant · {{ $etablissementNom }}</p>
                </div>
            </div>
            <div class="flex items-center gap-5">
                <div class="hidden h-11 w-[290px] items-center gap-2.5 rounded-lg border border-brand-trait bg-brand-fond px-3.5 text-brand-gris focus-within:border-brand-cyan md:flex" role="search">
                    <i class="ico size-[18px]" style="--ico: url('{{ asset('images/search.svg') }}')"></i>
                    <input type="search" placeholder="Rechercher..." aria-label="Rechercher"
                           class="min-w-0 flex-1 bg-transparent text-sm text-brand-nuit outline-none placeholder:text-brand-gris">
                </div>

                <a href="#" class="relative text-brand-nuit" aria-label="Notifications">
                    <i class="ico size-6" style="--ico: url('{{ asset('images/bell.svg') }}')"></i>
                    <span class="absolute right-px top-0 size-[7px] rounded-full bg-red-500"></span>
                </a>

                <details class="relative">
                    <summary class="cursor-pointer list-none [&::-webkit-details-marker]:hidden" aria-label="Menu du compte">
                        <span class="flex size-[38px] items-center justify-center rounded-full bg-brand-pale text-xs font-bold">{{ $initiales }}</span>
                    </summary>
                    <div class="absolute right-0 top-[46px] z-10 min-w-[170px] rounded-lg border border-brand-trait bg-white p-1.5 shadow-lg">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="w-full rounded-md px-3 py-2.5 text-left text-sm hover:bg-brand-fond">Se déconnecter</button>
                        </form>
                    </div>
                </details>
            </div>
        </header>

        <main class="flex flex-col gap-[26px] px-4 pb-12 pt-8 md:px-9">

            {{-- ===================== Statistiques ===================== --}}
            <section class="grid grid-cols-1 gap-[18px] sm:grid-cols-2 lg:grid-cols-4">
                <article class="{{ $carte }} flex h-[126px] flex-col justify-between p-[22px]">
                    <span class="text-[15px] text-brand-gris">Total Cours</span>
                    <span class="text-4xl font-bold leading-[1.1]">{{ $stats['cours'] }}</span>
                </article>

                <article class="{{ $carte }} flex h-[126px] flex-col justify-between p-[22px]">
                    <span class="text-[15px] text-brand-gris">Total Étudiants</span>
                    <span class="text-4xl font-bold leading-[1.1]">{{ $stats['etudiants'] }}</span>
                </article>

                <article class="{{ $carte }} flex h-[126px] flex-col justify-between p-[22px]">
                    <span class="text-[15px] text-brand-gris">Notes en attente</span>
                    <div class="flex items-center justify-between gap-2">
                        <span class="text-4xl font-bold leading-[1.1]">{{ $stats['notes_en_attente'] }}</span>
                        @if ($stats['notes_en_attente'] > 0)
                            <span class="{{ $badge }} {{ $badgeRouge }}">À traiter</span>
                        @endif
                    </div>
                </article>

                <article class="{{ $carte }} flex h-[126px] flex-col justify-between p-[22px]">
                    <span class="text-[15px] text-brand-gris">Présence moyenne</span>
                    <span class="text-4xl font-bold leading-[1.1]">{{ $stats['presence_moyenne'] }}&nbsp;%</span>
                </article>
            </section>

            {{-- ===================== Aujourd'hui ===================== --}}
            <h2 class="-mb-2 text-2xl font-bold">Aujourd’hui</h2>

            <section class="grid grid-cols-1 gap-[18px] lg:grid-cols-[608fr_406fr]">
                <article class="{{ $carte }} min-h-[304px] p-6">
                    <h3 class="text-lg font-bold">Emploi du temps</h3>

                    <div class="mt-4">
                        @forelse ($emploi as $c)
                            <div class="flex flex-wrap items-start gap-x-3.5 gap-y-1 border-b border-brand-trait py-3.5 first:pt-0 last:border-b-0">
                                <span class="w-full pt-0.5 text-xs font-bold text-brand-turquoise sm:w-[86px] sm:shrink-0">{{ $c['debut'] }}-{{ $c['fin'] }}</span>
                                <div class="min-w-0 flex-1">
                                    <p class="text-[15px] font-semibold">{{ $c['titre'] }}</p>
                                    <p class="mt-1.5 text-[13px] text-brand-gris">{{ $c['lieu'] }}</p>
                                </div>
                                @if (!empty($c['statut']) && isset($statuts[$c['statut']]))
                                    <span class="{{ $badge }} {{ $statuts[$c['statut']][1] }}">{{ $statuts[$c['statut']][0] }}</span>
                                @endif
                            </div>
                        @empty
                            <p class="text-[13px] text-brand-gris">Aucun cours prévu aujourd’hui.</p>
                        @endforelse
                    </div>
                </article>

                <article class="{{ $carte }} bg-brand-fond p-6">
                    <h3 class="text-lg font-bold">Actions Rapides</h3>

                    <div class="mt-[18px] grid grid-cols-2 gap-3">
                        @foreach ($actions as $action)
                            <a href="{{ $action['href'] }}"
                               class="flex h-[103px] flex-col justify-between rounded-[10px] border border-brand-trait bg-white px-[17px] py-4 text-[15px] font-semibold transition hover:border-brand-cyan hover:shadow-[0_4px_16px_rgba(34,211,238,0.18)]">
                                <i class="ico size-[22px] text-brand-turquoise" style="--ico: url('{{ asset('images/' . $action['icone'] . '.svg') }}')"></i>
                                <span>{{ $action['label'] }}</span>
                            </a>
                        @endforeach
                    </div>
                </article>
            </section>

            {{-- ===================== Suivi ===================== --}}
            <h2 class="-mb-2 text-2xl font-bold">Suivi</h2>

            <section class="grid grid-cols-1 gap-[18px] lg:grid-cols-2">
                <article class="{{ $carte }} min-h-[200px] p-6">
                    <h3 class="text-lg font-bold">Devoirs à corriger</h3>

                    <div class="mt-4">
                        @forelse ($devoirs as $d)
                            <div class="border-b border-brand-trait py-3.5 first:pt-0 last:border-b-0 last:pb-0">
                                <div class="flex items-center justify-between gap-3">
                                    <p class="text-[15px] font-semibold">{{ $d['titre'] }}</p>
                                    @if (!empty($d['badge']))
                                        <span class="{{ $badge }} {{ $badgeOrange }}">{{ $d['badge'] }}</span>
                                    @endif
                                </div>
                                <p class="mt-1.5 text-[13px] text-brand-gris">{{ $d['details'] }}</p>
                            </div>
                        @empty
                            <p class="text-[13px] text-brand-gris">Aucun devoir à corriger.</p>
                        @endforelse
                    </div>
                </article>

                <article class="{{ $carte }} min-h-[200px] p-6">
                    <h3 class="text-lg font-bold">Dernières Activités</h3>

                    <div class="mt-4">
                        @forelse ($activites as $a)
                            <div class="border-b border-brand-trait py-3.5 first:pt-0 last:border-b-0 last:pb-0">
                                <p class="text-[15px] font-semibold">{{ $a['titre'] }}</p>
                                <p class="mt-1.5 text-[13px] text-brand-gris">{{ $a['details'] }}</p>
                            </div>
                        @empty
                            <p class="text-[13px] text-brand-gris">Aucune activité récente.</p>
                        @endforelse
                    </div>
                </article>
            </section>
        </main>
    </div>

    <script>
        function toggleSidebar() {
            document.getElementById('sidebar').classList.toggle('open');
            document.getElementById('sidebar-overlay').classList.toggle('active');
        }
    </script>
</body>
</body>
</html>