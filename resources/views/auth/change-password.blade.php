<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sécurisation du compte — SHARKLINK</title>
    
    {{-- Police Inter --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    {{-- Tailwind CDN avec la charte SHARKLINK --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: { 
                            nuit: '#0b1f3a', 
                            cyan: '#22d3ee', 
                            fond: '#f4fbff', 
                            trait: '#e3edf3', 
                            gris: '#5f6f84',
                            danger: '#ef4444',
                            success: '#16845b'
                        }
                    },
                    fontFamily: { sans: ['Inter', 'sans-serif'] }
                }
            }
        }
    </script>
</head>
<body class="bg-brand-fond font-sans min-h-screen flex items-center justify-center p-4">

    <div class="w-full max-w-md">
        
        {{-- Logo --}}
        <div class="text-center mb-8">
            <a href="/" class="inline-flex items-center gap-2 text-2xl font-extrabold tracking-wide text-brand-nuit">
                <svg class="w-8 h-8 text-brand-cyan" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                SHARK<span class="text-brand-cyan">LINK</span>
            </a>
        </div>

        {{-- Carte principale --}}
        <div class="bg-white rounded-2xl shadow-xl border border-brand-trait p-8">
            
            <div class="text-center mb-6">
                <div class="w-14 h-14 bg-brand-cyan/10 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-7 h-7 text-brand-cyan" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                </div>
                <h1 class="text-xl font-bold text-brand-nuit">Sécurisez votre compte</h1>
                <p class="text-sm text-brand-gris mt-2">
                    Vous utilisez un mot de passe temporaire. Veuillez définir un nouveau mot de passe sécurisé pour accéder à votre espace.
                </p>
            </div>

            {{-- Affichage des erreurs de validation --}}
            @if ($errors->any())
                <div class="mb-5 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg text-sm">
                    <ul class="list-disc pl-5 space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Formulaire de changement de mot de passe --}}
            <form action="{{ route('password.update') }}" method="POST" class="space-y-4">
                @csrf
                
                {{-- Nouveau mot de passe --}}
                <div>
                    <label for="password" class="block text-xs font-semibold text-brand-gris mb-1.5">Nouveau mot de passe</label>
                    <div class="relative">
                        <input type="password" id="password" name="password" required
                               class="w-full px-4 py-2.5 rounded-lg border border-brand-trait bg-brand-fond text-brand-nuit font-medium focus:outline-none focus:border-brand-cyan focus:ring-1 focus:ring-brand-cyan transition pr-10"
                               placeholder="Minimum 8 caractères">
                        <button type="button" onclick="togglePassword('password', this)" class="absolute right-3 top-1/2 -translate-y-1/2 text-brand-gris hover:text-brand-nuit transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                        </button>
                    </div>
                </div>

                {{-- Confirmation du mot de passe --}}
                <div>
                    <label for="password_confirmation" class="block text-xs font-semibold text-brand-gris mb-1.5">Confirmer le mot de passe</label>
                    <div class="relative">
                        <input type="password" id="password_confirmation" name="password_confirmation" required
                               class="w-full px-4 py-2.5 rounded-lg border border-brand-trait bg-brand-fond text-brand-nuit font-medium focus:outline-none focus:border-brand-cyan focus:ring-1 focus:ring-brand-cyan transition pr-10"
                               placeholder="Retapez votre mot de passe">
                        <button type="button" onclick="togglePassword('password_confirmation', this)" class="absolute right-3 top-1/2 -translate-y-1/2 text-brand-gris hover:text-brand-nuit transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                        </button>
                    </div>
                </div>

                {{-- Indicateur de force (Optionnel mais pro) --}}
                <div class="bg-blue-50 border border-blue-100 rounded-lg p-3 flex gap-3">
                    <svg class="w-5 h-5 text-blue-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <p class="text-xs text-blue-800 leading-relaxed">
                        Pour la sécurité de votre compte, utilisez au moins <strong>8 caractères</strong>, incluant des majuscules, des chiffres et des symboles.
                    </p>
                </div>

                {{-- Bouton de soumission --}}
                <button type="submit" class="w-full py-3 rounded-lg bg-brand-nuit text-white font-bold text-sm hover:bg-opacity-90 transition flex items-center justify-center gap-2 shadow-sm mt-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                    Valider et accéder à mon espace
                </button>
            </form>

            {{-- Lien de déconnexion (au cas où) --}}
            <div class="mt-6 text-center border-t border-brand-trait pt-4">
                <p class="text-xs text-brand-gris mb-2">Ce n'est pas votre compte ?</p>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="text-xs font-semibold text-brand-nuit hover:text-brand-cyan transition flex items-center justify-center gap-1 mx-auto">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                        Se déconnecter
                    </button>
                </form>
            </div>
        </div>

        {{-- Footer --}}
        <p class="text-center text-xs text-brand-gris mt-6">
            © {{ date('Y') }} SHARKLINK. Tous droits réservés.
        </p>
    </div>

    {{-- Script pour afficher/masquer le mot de passe --}}
    <script>
        function togglePassword(inputId, btn) {
            const input = document.getElementById(inputId);
            if (input.type === 'password') {
                input.type = 'text';
                btn.innerHTML = '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"></path></svg>';
            } else {
                input.type = 'password';
                btn.innerHTML = '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>';
            }
        }
    </script>
</body>
</html>