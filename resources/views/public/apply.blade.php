<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Candidature — SHARKLINK</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: { brand: { nuit: '#0b1f3a', cyan: '#22d3ee', fond: '#f4fbff' } },
                    fontFamily: { sans: ['Inter', 'sans-serif'] }
                }
            }
        }
    </script>
    <style>
        .step-content { display: none; }
        .step-content.active { display: block; animation: fadeIn 0.3s ease-in-out; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
    </style>
</head>
<body class="bg-brand-fond font-sans text-brand-nuit min-h-screen flex flex-col">

    {{-- Header simple --}}
    <header class="bg-white border-b border-gray-200 py-4">
        <div class="max-w-4xl mx-auto px-6 flex items-center justify-between">
            <a href="/" class="text-2xl font-extrabold tracking-wide text-brand-nuit">SHARK<span class="text-brand-cyan">LINK</span></a>
            <a href="{{ route('login') }}" class="text-sm font-semibold text-brand-nuit hover:text-brand-cyan transition">Se connecter</a>
        </div>
    </header>

    <main class="flex-1 py-10 px-4">
        <div class="max-w-3xl mx-auto">
            
            {{-- Titre --}}
            <div class="text-center mb-10">
                <h1 class="text-3xl font-bold text-brand-nuit mb-2">Déposez votre candidature</h1>
                <p class="text-gray-500">Rejoignez l'excellence académique en quelques étapes.</p>
            </div>

            {{-- Barre de progression --}}
            <div class="flex items-center justify-between mb-8 px-4">
                @php $steps = ['Infos', 'Parcours', 'Documents', 'Validation']; @endphp
                @foreach($steps as $index => $step)
                    <div class="flex flex-col items-center relative z-10">
                        <div id="step-indicator-{{ $index + 1 }}" class="w-10 h-10 rounded-full flex items-center justify-center font-bold text-sm transition-all duration-300 bg-brand-nuit text-white border-2 border-brand-nuit">
                            {{ $index + 1 }}
                        </div>
                        <span class="text-xs mt-2 font-medium text-gray-500">{{ $step }}</span>
                    </div>
                    @if(!$loop->last)
                        <div class="flex-1 h-1 bg-gray-200 mx-2 -mt-6">
                            <div id="progress-line-{{ $index + 1 }}" class="h-full bg-brand-cyan transition-all duration-500" style="width: 0%"></div>
                        </div>
                    @endif
                @endforeach
            </div>

            {{-- Formulaire --}}
            <form action="{{ route('apply.store') }}" method="POST" enctype="multipart/form-data" class="bg-white rounded-2xl shadow-lg p-8 border border-gray-100">
                @csrf

                @if ($errors->any())
                    <div class="mb-6 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg text-sm">
                        <ul class="list-disc pl-5">
                            @foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach
                        </ul>
                    </div>
                @endif

                {{-- ÉTAPE 1 : Infos Personnelles --}}
                <div id="step-1" class="step-content active">
                    <h2 class="text-xl font-bold mb-6">Informations personnelles</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Prénom</label>
                            <input type="text" name="first_name" value="{{ old('first_name') }}" class="w-full px-4 py-2.5 rounded-lg border border-gray-200 focus:border-brand-cyan focus:ring-1 focus:ring-brand-cyan outline-none transition" required>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Nom</label>
                            <input type="text" name="last_name" value="{{ old('last_name') }}" class="w-full px-4 py-2.5 rounded-lg border border-gray-200 focus:border-brand-cyan focus:ring-1 focus:ring-brand-cyan outline-none transition" required>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Email</label>
                            <input type="email" name="email" value="{{ old('email') }}" class="w-full px-4 py-2.5 rounded-lg border border-gray-200 focus:border-brand-cyan focus:ring-1 focus:ring-brand-cyan outline-none transition" required>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Téléphone</label>
                            <input type="tel" name="phone" value="{{ old('phone') }}" class="w-full px-4 py-2.5 rounded-lg border border-gray-200 focus:border-brand-cyan focus:ring-1 focus:ring-brand-cyan outline-none transition" required>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Date de naissance</label>
                            <input type="date" name="birth_date" value="{{ old('birth_date') }}" class="w-full px-4 py-2.5 rounded-lg border border-gray-200 focus:border-brand-cyan focus:ring-1 focus:ring-brand-cyan outline-none transition" required>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Lieu de naissance</label>
                            <input type="text" name="birth_place" value="{{ old('birth_place') }}" class="w-full px-4 py-2.5 rounded-lg border border-gray-200 focus:border-brand-cyan focus:ring-1 focus:ring-brand-cyan outline-none transition" required>
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Genre</label>
                            <div class="flex gap-4">
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="radio" name="gender" value="M" {{ old('gender') == 'M' ? 'checked' : '' }} class="accent-brand-nuit"> <span class="text-sm">Masculin</span>
                                </label>
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="radio" name="gender" value="F" {{ old('gender') == 'F' ? 'checked' : '' }} class="accent-brand-nuit"> <span class="text-sm">Féminin</span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ÉTAPE 2 : Parcours & Choix --}}
                <div id="step-2" class="step-content">
                    <h2 class="text-xl font-bold mb-6">Parcours académique & Choix</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div class="md:col-span-2">
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Dernier établissement fréquenté</label>
                            <input type="text" name="previous_school" value="{{ old('previous_school') }}" placeholder="ex: Lycée de Bafoussam" class="w-full px-4 py-2.5 rounded-lg border border-gray-200 focus:border-brand-cyan focus:ring-1 focus:ring-brand-cyan outline-none transition" required>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Dernier diplôme obtenu</label>
                            <input type="text" name="previous_diploma" value="{{ old('previous_diploma') }}" placeholder="ex: BAC A4, Licence" class="w-full px-4 py-2.5 rounded-lg border border-gray-200 focus:border-brand-cyan focus:ring-1 focus:ring-brand-cyan outline-none transition" required>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Année d'obtention</label>
                            <input type="number" name="graduation_year" value="{{ old('graduation_year') }}" min="1990" max="2030" class="w-full px-4 py-2.5 rounded-lg border border-gray-200 focus:border-brand-cyan focus:ring-1 focus:ring-brand-cyan outline-none transition" required>
                        </div>
                        <div class="md:col-span-2 border-t border-gray-100 pt-5 mt-2">
                            <p class="text-sm font-bold text-brand-nuit mb-4">Formation souhaitée à SHARKLINK</p>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Programme / Filière</label>
                            <select name="program_id" class="w-full px-4 py-2.5 rounded-lg border border-gray-200 focus:border-brand-cyan focus:ring-1 focus:ring-brand-cyan outline-none transition bg-white" required>
                                <option value="">-- Choisir --</option>
                                @foreach($programs as $prog)
                                    <option value="{{ $prog->id }}" {{ old('program_id') == $prog->id ? 'selected' : '' }}>{{ $prog->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Niveau souhaité</label>
                            <select name="level_id" class="w-full px-4 py-2.5 rounded-lg border border-gray-200 focus:border-brand-cyan focus:ring-1 focus:ring-brand-cyan outline-none transition bg-white" required>
                                <option value="">-- Choisir --</option>
                                @foreach($levels as $lvl)
                                    <option value="{{ $lvl->id }}" {{ old('level_id') == $lvl->id ? 'selected' : '' }}>{{ $lvl->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                {{-- ÉTAPE 3 : Documents --}}
                <div id="step-3" class="step-content">
                    <h2 class="text-xl font-bold mb-6">Pièces justificatives</h2>
                    <p class="text-sm text-gray-500 mb-6 bg-blue-50 p-3 rounded-lg border border-blue-100">Veuillez uploader vos documents au format PDF ou Image (Max 2 Mo).</p>
                    
                    <div class="space-y-5">
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Copie du dernier diplôme / BAC</label>
                            <input type="file" name="documents[diploma]" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-brand-fond file:text-brand-nuit hover:file:bg-gray-100" required>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Relevé de notes</label>
                            <input type="file" name="documents[transcript]" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-brand-fond file:text-brand-nuit hover:file:bg-gray-100" required>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Pièce d'identité (CNI / Passeport)</label>
                            <input type="file" name="documents[id_card]" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-brand-fond file:text-brand-nuit hover:file:bg-gray-100" required>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Photo d'identité</label>
                            <input type="file" name="documents[photo]" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-brand-fond file:text-brand-nuit hover:file:bg-gray-100" required>
                        </div>
                    </div>
                </div>

                {{-- ÉTAPE 4 : Validation --}}
                <div id="step-4" class="step-content">
                    <h2 class="text-xl font-bold mb-6">Vérification et soumission</h2>
                    <div class="bg-brand-fond p-6 rounded-xl border border-gray-200 mb-6">
                        <p class="text-sm text-gray-600 mb-4">En soumettant ce formulaire, vous certifiez que les informations fournies sont exactes. Votre dossier sera examiné par notre service des admissions.</p>
                        <label class="flex items-start gap-3 cursor-pointer">
                            <input type="checkbox" required class="mt-1 accent-brand-nuit w-4 h-4">
                            <span class="text-xs text-gray-600">J'accepte les conditions d'utilisation et la politique de confidentialité de SHARKLINK.</span>
                        </label>
                    </div>
                </div>

                {{-- Boutons de navigation --}}
                <div class="flex justify-between mt-10 pt-6 border-t border-gray-100">
                    <button type="button" id="prevBtn" class="hidden px-6 py-2.5 rounded-lg border border-gray-300 text-sm font-semibold text-gray-600 hover:bg-gray-50 transition">
                        Précédent
                    </button>
                    <button type="button" id="nextBtn" class="ml-auto px-8 py-2.5 rounded-lg bg-brand-nuit text-white text-sm font-semibold hover:bg-opacity-90 transition flex items-center gap-2">
                        Suivant <span>→</span>
                    </button>
                    <button type="submit" id="submitBtn" class="hidden ml-auto px-8 py-2.5 rounded-lg bg-brand-cyan text-brand-nuit text-sm font-bold hover:bg-cyan-400 transition flex items-center gap-2">
                        Soumettre ma candidature <span>✓</span>
                    </button>
                </div>
            </form>
        </div>
    </main>

    <footer class="bg-white border-t border-gray-200 py-6 mt-auto">
        <div class="max-w-4xl mx-auto px-6 text-center text-xs text-gray-500">
            © 2026 SHARKLINK. Tous droits réservés.
        </div>
    </footer>

    <script>
        let currentStep = 1;
        const totalSteps = 4;

        function updateUI() {
            // Cacher/Montrer les étapes
            document.querySelectorAll('.step-content').forEach(el => el.classList.remove('active'));
            document.getElementById(`step-${currentStep}`).classList.add('active');

            // Mettre à jour les indicateurs
            for (let i = 1; i <= totalSteps; i++) {
                const indicator = document.getElementById(`step-indicator-${i}`);
                if (i < currentStep) {
                    indicator.className = 'w-10 h-10 rounded-full flex items-center justify-center font-bold text-sm transition-all duration-300 bg-brand-cyan text-brand-nuit border-2 border-brand-cyan';
                    indicator.innerHTML = '✓';
                } else if (i === currentStep) {
                    indicator.className = 'w-10 h-10 rounded-full flex items-center justify-center font-bold text-sm transition-all duration-300 bg-brand-nuit text-white border-2 border-brand-nuit';
                    indicator.innerHTML = i;
                } else {
                    indicator.className = 'w-10 h-10 rounded-full flex items-center justify-center font-bold text-sm transition-all duration-300 bg-white text-gray-400 border-2 border-gray-200';
                    indicator.innerHTML = i;
                }

                if (i < totalSteps) {
                    const line = document.getElementById(`progress-line-${i}`);
                    line.style.width = i < currentStep ? '100%' : '0%';
                }
            }

            // Gérer les boutons
            document.getElementById('prevBtn').classList.toggle('hidden', currentStep === 1);
            document.getElementById('nextBtn').classList.toggle('hidden', currentStep === totalSteps);
            document.getElementById('submitBtn').classList.toggle('hidden', currentStep !== totalSteps);
        }

        document.getElementById('nextBtn').addEventListener('click', () => {
            if (currentStep < totalSteps) {
                // Validation HTML5 basique avant de passer à l'étape suivante
                const currentInputs = document.getElementById(`step-${currentStep}`).querySelectorAll('input[required], select[required]');
                let isValid = true;
                currentInputs.forEach(input => {
                    if (!input.checkValidity()) {
                        isValid = false;
                        input.reportValidity();
                    }
                });
                
                if (isValid) {
                    currentStep++;
                    updateUI();
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                }
            }
        });

        document.getElementById('prevBtn').addEventListener('click', () => {
            if (currentStep > 1) {
                currentStep--;
                updateUI();
                window.scrollTo({ top: 0, behavior: 'smooth' });
            }
        });
    </script>
</body>
</html>