<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Connexion — SHARKLINK</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=arimo:400,700|inter:800" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-[#f4fbff] text-[#0b1f3a] antialiased" style="font-family: 'Arimo', ui-sans-serif, system-ui, sans-serif;">

<div class="min-h-screen bg-[#f4fbff] lg:flex">

    {{-- ===== Marque (mobile uniquement) ===== --}}
    <div class="flex h-24 items-center justify-center gap-[5px] bg-[#f4fbff] lg:hidden">
        <img src="{{ asset('images/shark-mark-dark.svg') }}" alt="" class="h-[41px] w-[38px]">
        <span class="text-[28px] font-extrabold text-[#0b1f3a]" style="font-family: 'Inter', sans-serif;">SHARKLINK</span>
    </div>

    {{-- ===== Photographie de la remise des diplômes ===== --}}
    <div class="relative h-[142px] overflow-hidden bg-[#0b1f3a] lg:h-auto lg:min-h-screen lg:flex-1">
        <img src="{{ asset('images/login-graduates.png') }}" alt=""
             class="absolute inset-0 size-full object-cover object-[center_30%] lg:object-center">
        {{-- Accent de marque (mobile) --}}
        <div class="absolute bottom-0 left-0 h-[3px] w-full bg-[#22d3ee] lg:hidden"></div>
    </div>

    {{-- ===== Espace de connexion ===== --}}
    <section class="relative flex flex-col overflow-hidden bg-[#f4fbff] px-7 pb-6 pt-7 lg:min-h-screen lg:w-[370px] lg:shrink-0 lg:px-0 lg:pb-0 lg:pt-[52px]">

        {{-- Fond photographique discret (desktop) --}}
        <div class="pointer-events-none absolute inset-0 hidden overflow-hidden opacity-[0.08] lg:block" aria-hidden="true">
            <img src="{{ asset('images/login-graduates.png') }}" alt=""
                 class="absolute -left-[700px] -top-[78px] h-[978px] w-[1304px] max-w-none object-cover">
        </div>

        <div class="relative z-10 flex flex-1 flex-col">

            {{-- Identité (desktop) --}}
            <div class="hidden h-[75px] items-center justify-center gap-[5px] lg:flex">
                <img src="{{ asset('images/shark-mark-dark.svg') }}" alt="" class="h-[46px] w-[43px]">
                <span class="text-[29px] font-extrabold text-[#0b1f3a]" style="font-family: 'Inter', sans-serif;">SHARKLINK</span>
            </div>

            {{-- Type d'utilisateur --}}
            <div class="flex h-12 overflow-hidden rounded-3xl border-[1.5px] border-[#0b1f3a] lg:mx-11 lg:mt-[124px] lg:h-[46px]"
                 role="tablist" aria-label="Type d'utilisateur">
                <button type="button" data-role="etudiant" role="tab" aria-selected="true"
                        class="role-tab flex flex-1 items-center justify-center bg-white text-base text-[#0b1f3a]">
                    Étudiants
                </button>
                <button type="button" data-role="employe" role="tab" aria-selected="false"
                        class="role-tab flex flex-1 items-center justify-center bg-[#0b1f3a] text-base text-white">
                    Employés
                </button>
            </div>

            {{-- Formulaire --}}
            <form method="POST" action="{{ route('login.store') }}"
                  class="mt-7 flex flex-col gap-6 lg:mx-11 lg:mt-[76px]">
                @csrf
                <input type="hidden" name="user_type" id="user_type" value="{{ old('user_type', 'etudiant') }}">

                {{-- Adresse email --}}
                <div class="flex flex-col gap-3 border-b-2 border-[#22d3ee] pb-2.5 pt-0.5">
                    <label for="email" class="text-sm text-[#22d3ee]">Adresse email</label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}"
                           autocomplete="username" required
                           class="min-h-[21px] w-full bg-transparent text-[17px] text-[#0b1f3a] focus:outline-none">
                </div>
                @error('email')
                    <p class="-mt-4 text-xs text-red-600">{{ $message }}</p>
                @enderror

                {{-- Mot de passe --}}
                <div class="border-b border-[#d1d5db] transition-colors focus-within:border-[#22d3ee]">
                    <label for="password" class="block pt-0.5 text-sm text-[#6b7280] lg:pb-3">Mot de passe</label>
                    <div class="flex h-11 items-center justify-between lg:h-auto lg:pb-2.5">
                        <input id="password" type="password" name="password" placeholder="••••••••"
                               autocomplete="current-password" required
                               class="min-w-0 flex-1 bg-transparent text-lg text-[#0b1f3a] placeholder:text-[#6b7280] focus:outline-none lg:text-[17px]">
                        <button type="button" id="toggle-password" aria-label="Afficher le mot de passe"
                                class="flex size-11 shrink-0 items-center justify-center text-[#6b7280] lg:size-[18px]">
                            <svg id="eye-on" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                 stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" class="size-5 lg:size-[18px]">
                                <path d="M2.062 12.348a1 1 0 0 1 0-.696 10.75 10.75 0 0 1 19.876 0 1 1 0 0 1 0 .696 10.75 10.75 0 0 1-19.876 0"/>
                                <circle cx="12" cy="12" r="3"/>
                            </svg>
                            <svg id="eye-off" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                 stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" class="hidden size-5 lg:size-[18px]">
                                <path d="M10.733 5.076a10.744 10.744 0 0 1 11.205 6.575 1 1 0 0 1 0 .696 10.747 10.747 0 0 1-1.444 2.49"/>
                                <path d="M14.084 14.158a3 3 0 0 1-4.242-4.242"/>
                                <path d="M17.479 17.499a10.75 10.75 0 0 1-15.417-5.151 1 1 0 0 1 0-.696 10.75 10.75 0 0 1 4.446-5.143"/>
                                <path d="m2 2 20 20"/>
                            </svg>
                        </button>
                    </div>
                </div>
                @error('password')
                    <p class="-mt-4 text-xs text-red-600">{{ $message }}</p>
                @enderror

                {{-- Options --}}
                <div class="flex flex-col gap-3">
                    <label class="flex min-h-6 cursor-pointer items-center gap-2.5 text-sm text-[#0b1f3a] lg:min-h-0">
                        <input type="checkbox" name="remember" value="1" @checked(old('remember'))
                               class="size-[18px] shrink-0 cursor-pointer appearance-none rounded-[4px] border-[1.5px] border-[#0b1f3a] bg-white checked:bg-[#0b1f3a] lg:size-4">
                        Se souvenir de moi
                    </label>
                    <a href="#" class="text-sm font-bold leading-6 text-[#22d3ee] lg:leading-normal">Mot de passe oublié ?</a>
                </div>

                {{-- Bouton --}}
                <button type="submit"
                        class="flex h-[50px] w-full items-center justify-center rounded-[2px] bg-[#0b1f3a] text-base text-white lg:h-[43px]">
                    Connexion
                </button>
            </form>

            {{-- Aide à la connexion --}}
            <div class="mt-7 flex flex-col gap-3 text-sm leading-5 text-[#6b7280] lg:mx-11 lg:mt-0 lg:gap-2 lg:pt-7 lg:leading-normal">
                <a href="#">Première utilisation?</a>
                <a href="#">Problème de connexion?</a>
            </div>
        </div>

        {{-- Informations légales --}}
        <div class="relative z-10 mt-4 flex flex-col items-center gap-3 text-center text-xs text-[#6b7280] lg:absolute lg:inset-x-7 lg:bottom-5 lg:mt-0">
            <a href="#">Sécurité et confidentialité</a>
            <a href="#">Conditions d’utilisation</a>
            <p class="pt-3 text-[11px] lg:pt-[25px]">
                <span class="lg:hidden">© 2025 Skytech Communications</span>
                <span class="hidden lg:inline">© 2026 Sharklink Solutions. Tous droits réservés.</span>
            </p>
        </div>
    </section>
</div>

<script>
    // Afficher / masquer le mot de passe
    const pwd = document.getElementById('password');
    const eyeOn = document.getElementById('eye-on');
    const eyeOff = document.getElementById('eye-off');
    document.getElementById('toggle-password').addEventListener('click', () => {
        const show = pwd.type === 'password';
        pwd.type = show ? 'text' : 'password';
        eyeOn.classList.toggle('hidden', show);
        eyeOff.classList.toggle('hidden', !show);
    });

    // Bascule Étudiants / Employés (sélectionné = fond blanc, comme dans la maquette)
    const tabs = document.querySelectorAll('.role-tab');
    const roleInput = document.getElementById('user_type');
    const setRole = (role) => {
        roleInput.value = role;
        tabs.forEach((tab) => {
            const active = tab.dataset.role === role;
            tab.setAttribute('aria-selected', active);
            tab.classList.toggle('bg-white', active);
            tab.classList.toggle('text-[#0b1f3a]', active);
            tab.classList.toggle('bg-[#0b1f3a]', !active);
            tab.classList.toggle('text-white', !active);
        });
    };
    tabs.forEach((tab) => tab.addEventListener('click', () => setRole(tab.dataset.role)));
    setRole(roleInput.value || 'etudiant');
</script>
</body>
</html>