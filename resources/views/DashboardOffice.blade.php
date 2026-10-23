<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Espace Scolarité — SHARKLINK</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: { nuit: '#0b1f3a', cyan: '#22d3ee', pale: '#d9f6fc', turquoise: '#087e96', fond: '#f4fbff', trait: '#e3edf3', gris: '#5f6f84' }
                    },
                    fontFamily: { sans: ['Inter', 'sans-serif'] }
                }
            }
        }
    </script>
</head>
<body class="flex min-h-screen flex-col bg-brand-fond font-sans text-sm text-brand-nuit md:flex-row">

    <div id="sidebar-overlay" class="fixed inset-0 z-30 hidden bg-black/50 md:hidden"></div>
    {{-- Sidebar --}}
    <aside id="sidebar" class="fixed left-0 top-0 z-40 flex h-screen w-[260px] -translate-x-full flex-col gap-3 overflow-y-auto bg-brand-nuit px-[17px] py-8 text-[#d3dcea] transition-transform duration-300 ease-in-out md:sticky md:shrink-0 md:translate-x-0">        <div id="sidebar-overlay" class="fixed inset-0 z-30 hidden bg-black/50 md:hidden"></div>
        <div class="flex items-center justify-between px-4">
            <span class="text-[19px] font-extrabold tracking-[0.08em] text-brand-cyan">SHARKLINK</span>
            <button type="button" data-sidebar-close class="text-[#d3dcea] hover:text-white md:hidden" aria-label="Fermer le menu">
                <i class="fa-solid fa-xmark text-xl"></i>
            </button>
        </div>
        <nav class="flex flex-col gap-1.5">
            <a href="{{ route('dashboard') }}" class="flex h-[42px] items-center gap-3.5 rounded-lg border border-transparent px-4 text-sm hover:bg-white/5 hover:text-white md:h-[46px] md:text-[15px]">
                <i class="fa-solid fa-gauge-high w-5 text-center text-[17px]"></i><span>Tableau de bord</span>
            </a>
            <a href="{{ route('office.enrollments') }}" class="flex h-[42px] items-center gap-3.5 rounded-lg border border-transparent px-4 text-sm hover:bg-white/5 hover:text-white md:h-[46px] md:text-[15px]">
                <i class="fa-solid fa-file-signature w-5 text-center text-[17px]"></i><span>Inscriptions</span>
            </a>
            <a href="{{ route('office.students') }}" class="flex h-[42px] items-center gap-3.5 rounded-lg border border-transparent px-4 text-sm hover:bg-white/5 hover:text-white md:h-[46px] md:text-[15px]">
                <i class="fa-solid fa-user-graduate w-5 text-center text-[17px]"></i><span>Étudiants</span>
            </a>
            <a href="{{ route('office.documents') }}" class="flex h-[42px] items-center gap-3.5 rounded-lg border border-transparent px-4 text-sm hover:bg-white/5 hover:text-white md:h-[46px] md:text-[15px]">
                <i class="fa-solid fa-folder-open w-5 text-center text-[17px]"></i><span>Documents</span>
            </a>
            <a href="{{ route('office.timetable') }}" class="flex h-[42px] items-center gap-3.5 rounded-lg border border-transparent px-4 text-sm hover:bg-white/5 hover:text-white md:h-[46px] md:text-[15px]">
                <i class="fa-solid fa-calendar-days w-5 text-center text-[17px]"></i><span>Emploi du temps</span>
            </a>
            <a href="{{ route('office.announcements') }}" class="flex h-[42px] items-center gap-3.5 rounded-lg border border-transparent px-4 text-sm hover:bg-white/5 hover:text-white md:h-[46px] md:text-[15px]">
                <i class="fa-solid fa-bullhorn w-5 text-center text-[17px]"></i><span>Annonces</span>
            </a>
        </nav>
    </aside>

    <div class="min-w-0 flex-1">
        {{-- Header --}}
        <header class="flex h-[72px] items-center justify-between gap-4 border-b border-brand-trait bg-white px-4 md:px-9">
           <div class="flex min-w-0 items-center gap-3">
                <button type="button" id="sidebar-toggle"
                        class="flex size-9 shrink-0 items-center justify-center rounded-lg text-brand-nuit hover:bg-brand-pale md:hidden"
                        aria-label="Ouvrir le menu" aria-controls="sidebar" aria-expanded="false">
                    <i id="sidebar-toggle-icon" class="fa-solid fa-bars text-xl"></i>
                </button>
                <div class="min-w-0">
                    <h1 class="truncate text-lg font-bold">Bonjour {{ $user->first_name }} {{ $user->last_name }}</h1>
                    <p class="mt-[3px] truncate text-xs text-brand-gris">Scolarité · {{ $establishment->name }}</p>
                </div>
            </div>
            <div class="flex items-center gap-4">
                <a href="#" class="relative text-brand-nuit"><i class="fa-solid fa-bell text-[22px]"></i><span class="absolute right-0 top-0 size-[7px] rounded-full bg-red-500"></span></a>
                <form method="POST" action="{{ route('logout') }}">@csrf<button type="submit" class="text-xs text-brand-gris hover:text-red-500">Déconnexion</button></form>
            </div>
        </header>

        <main class="flex flex-col gap-[26px] px-4 pb-12 pt-8 md:px-9">
            {{-- Stats --}}
            <section class="grid grid-cols-1 gap-[18px] sm:grid-cols-2 lg:grid-cols-4">
                <div class="min-w-0 rounded-xl border border-brand-trait bg-white flex h-[126px] flex-col justify-between p-[22px]">
                    <span class="text-[15px] text-brand-gris">Dossiers en attente</span>
                    <span class="text-4xl font-bold leading-[1.1] text-brand-nuit">12</span>
                </div>
                <div class="min-w-0 rounded-xl border border-brand-trait bg-white flex h-[126px] flex-col justify-between p-[22px]">
                    <span class="text-[15px] text-brand-gris">Inscriptions validées</span>
                    <span class="text-4xl font-bold leading-[1.1] text-brand-nuit">348</span>
                </div>
                <div class="min-w-0 rounded-xl border border-brand-trait bg-white flex h-[126px] flex-col justify-between p-[22px]">
                    <span class="text-[15px] text-brand-gris">Documents générés</span>
                    <span class="text-4xl font-bold leading-[1.1] text-brand-nuit">85</span>
                </div>
                <div class="min-w-0 rounded-xl border border-brand-trait bg-white flex h-[126px] flex-col justify-between p-[22px]">
                    <span class="text-[15px] text-brand-gris">Annonces actives</span>
                    <span class="text-4xl font-bold leading-[1.1] text-brand-nuit">4</span>
                </div>
            </section>

            {{-- Actions Rapides --}}
            <h2 class="text-2xl font-bold">Actions Rapides</h2>
            <section class="grid grid-cols-2 gap-3 md:grid-cols-4">
                <a href="{{ route('office.enrollments') }}" class="flex h-[103px] flex-col justify-between rounded-[10px] border border-brand-trait bg-white px-[17px] py-4 text-[15px] font-semibold transition hover:border-brand-cyan hover:shadow-lg">
                    <i class="fa-solid fa-user-plus text-[22px] text-brand-turquoise"></i><span>Nouvelle Inscription</span>
                </a>
                <a href="{{ route('office.students') }}" class="flex h-[103px] flex-col justify-between rounded-[10px] border border-brand-trait bg-white px-[17px] py-4 text-[15px] font-semibold transition hover:border-brand-cyan hover:shadow-lg">
                    <i class="fa-solid fa-magnifying-glass text-[22px] text-brand-turquoise"></i><span>Rechercher Étudiant</span>
                </a>
                <a href="{{ route('office.documents') }}" class="flex h-[103px] flex-col justify-between rounded-[10px] border border-brand-trait bg-white px-[17px] py-4 text-[15px] font-semibold transition hover:border-brand-cyan hover:shadow-lg">
                    <i class="fa-solid fa-file-pdf text-[22px] text-brand-turquoise"></i><span>Générer Attestation</span>
                </a>
                <a href="{{ route('office.announcements') }}" class="flex h-[103px] flex-col justify-between rounded-[10px] border border-brand-trait bg-white px-[17px] py-4 text-[15px] font-semibold transition hover:border-brand-cyan hover:shadow-lg">
                    <i class="fa-solid fa-bullhorn text-[22px] text-brand-turquoise"></i><span>Publier Annonce</span>
                </a>
            </section>
        </main>
    </div>
    <script>
        (function () {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebar-overlay');
            const toggle  = document.getElementById('sidebar-toggle');
            const icon    = document.getElementById('sidebar-toggle-icon');
            if (!sidebar || !toggle) return;

            const isOpen = () => !sidebar.classList.contains('-translate-x-full');

            function setOpen(open) {
                sidebar.classList.toggle('-translate-x-full', !open);
                overlay.classList.toggle('hidden', !open);
                document.body.classList.toggle('overflow-hidden', open);
                toggle.setAttribute('aria-expanded', open);
                toggle.setAttribute('aria-label', open ? 'Fermer le menu' : 'Ouvrir le menu');
                icon.classList.toggle('fa-bars', !open);
                icon.classList.toggle('fa-xmark', open);
            }

            toggle.addEventListener('click', () => setOpen(!isOpen()));
            overlay.addEventListener('click', () => setOpen(false));
            document.querySelectorAll('[data-sidebar-close]').forEach(b =>
                b.addEventListener('click', () => setOpen(false)));
            document.addEventListener('keydown', e => { if (e.key === 'Escape') setOpen(false); });
            window.matchMedia('(min-width: 768px)').addEventListener('change', e => {
                if (e.matches) setOpen(false);
            });
        })();
    </script>
</body>
</html>