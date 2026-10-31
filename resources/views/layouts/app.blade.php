<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
      <title>SHARKLINK</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}?v=3">
    <link rel="shortcut icon" href="{{ asset('favicon.png') }}?v=3">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800" rel="stylesheet" />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" />


    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-[#f4fbff] text-[#0b1f3a] font-sans text-base antialiased">
    <div class="flex min-h-screen">
        @include('partials.sidebar')

        <main class="flex min-w-0 flex-1 flex-col gap-6 px-4 pb-10 sm:px-10">
            @include('partials.topbar')
            @yield('content')
        </main>
    </div>

      {{-- 2. Tailwind CDN avec configuration personnalisée --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        // Définit 'Inter' comme police par défaut partout
                        sans: ['Inter', 'ui-sans-serif', 'system-ui', 'sans-serif'],
                    },
                    fontSize: {
                        // Augmente la taille de base pour une meilleure lisibilité SaaS
                        base: ['30px', { lineHeight: '2' }], 
                        sm: ['30px', { lineHeight: '2' }],
                        xs: ['30px', { lineHeight: '2' }],
                    }
                }
            }
        }
    </script>
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

            // Repasse en mode normal si on agrandit la fenêtre au-delà de lg (1024px)
            window.matchMedia('(min-width: 1024px)').addEventListener('change', e => {
                if (e.matches) setOpen(false);
            });
        })();
    </script>
</body>
</html>