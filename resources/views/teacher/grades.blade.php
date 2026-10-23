<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Saisie des Notes — SHARKLINK</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: { nuit: '#0b1f3a', cyan: '#22d3ee', pale: '#d9f6fc', turquoise: '#087e96', fond: '#f4fbff', trait: '#e3edf3', gris: '#5f6f84', success: '#16845b', danger: '#ef4444' }
                    },
                    fontFamily: { sans: ['Inter', 'sans-serif'] }
                }
            }
        }
    </script>
</head>
<body class="bg-brand-fond font-sans text-sm text-brand-nuit min-h-screen flex flex-col md:flex-row">

    {{-- Sidebar (Cachée sur mobile, visible uniquement sur desktop) --}}
    <aside class="hidden md:flex w-[260px] min-h-screen bg-brand-nuit text-[#d3dcea] p-4 flex-col gap-4 shrink-0">
        <div class="text-[19px] font-extrabold tracking-[0.08em] text-brand-cyan px-2 mb-4">SHARKLINK</div>
        <nav class="flex flex-col gap-1">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-lg hover:bg-white/5 text-white transition">
                <i class="fa-solid fa-gauge-high w-5 text-center"></i> Tableau de bord
            </a>
            <a href="{{ route('teacher.courses') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-lg hover:bg-white/5 text-white transition">
                <i class="fa-solid fa-book-open w-5 text-center"></i> Mes Cours
            </a>
            <a href="{{ route('teacher.grades') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-lg bg-brand-cyan/10 text-brand-cyan border border-brand-cyan/30 font-semibold">
                <i class="fa-solid fa-pen-to-square w-5 text-center"></i> Saisie Notes
            </a>
            <a href="{{ route('teacher.timetable') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-lg hover:bg-white/5 text-white transition">
                <i class="fa-solid fa-calendar-days w-5 text-center"></i> Emploi du Temps
            </a>
        </nav>
    </aside>

    {{-- Contenu Principal --}}
    <div class="flex-1 flex flex-col min-w-0">
        
        {{-- Header --}}
    <header class="h-[72px] bg-white border-b border-brand-trait flex items-center justify-between px-4 md:px-9 shrink-0">
        <div>
            <h1 class="text-lg font-bold text-brand-nuit">Saisie des Notes</h1>
            <p class="text-xs text-brand-gris mt-0.5">Enseignant · {{ $context['establishment']->name }}</p>
        </div>
        <a href="{{ route('dashboard') }}" class="text-xs text-brand-gris hover:text-brand-nuit flex items-center gap-2 bg-brand-fond px-3 py-2 rounded-lg border border-brand-trait transition">
            <i class="fa-solid fa-arrow-left"></i> <span class="hidden sm:inline">Retour</span>
        </a>
    </header>

        <main class="p-6 md:p-9 flex flex-col gap-6 flex-1">
            
            {{-- Message de succès --}}
            @if(session('success'))
                <div class="bg-brand-success/10 text-brand-success border border-brand-success/20 rounded-lg p-4 flex items-center gap-3">
                    <i class="fa-solid fa-circle-check"></i> {{ session('success') }}
                </div>
            @endif

            {{-- Sélecteurs de contexte --}}
            <div class="bg-white rounded-xl border border-brand-trait p-5 flex flex-col md:flex-row gap-4 items-end">
                <div class="flex-1 w-full">
                    <label class="block text-xs font-semibold text-brand-gris mb-1.5">1. Sélectionner le cours</label>
                    <form method="GET" action="{{ route('teacher.grades') }}" class="flex gap-3">
                        <select name="offering_id" onchange="this.form.submit()" class="w-full md:w-auto px-4 py-2.5 rounded-lg border border-brand-trait bg-brand-fond text-brand-nuit font-medium focus:outline-none focus:border-brand-cyan transition">
                            @foreach($offerings as $offering)
                                <option value="{{ $offering->id }}" {{ $selectedOffering && $selectedOffering->id == $offering->id ? 'selected' : '' }}>
                                    {{ $offering->title }} ({{ $offering->term }})
                                </option>
                            @endforeach
                        </select>
                    </form>
                </div>
                
                <div class="flex-1 w-full">
                    <label class="block text-xs font-semibold text-brand-gris mb-1.5">2. Sélectionner l'évaluation</label>
                    <select id="assessment-select" class="w-full px-4 py-2.5 rounded-lg border border-brand-trait bg-brand-fond text-brand-nuit font-medium focus:outline-none focus:border-brand-cyan transition">
                        <option value="">-- Choisir une évaluation --</option>
                        @foreach($assessments as $assessment)
                            <option value="{{ $assessment->id }}" data-max="{{ $assessment->max_score }}">
                                {{ $assessment->title }} (Coeff. {{ $assessment->coefficient }})
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{-- Tableau de saisie --}}
            @if($selectedOffering && $students->count() > 0)
            <form method="POST" action="{{ route('teacher.grades.store') }}" id="grades-form">
                @csrf
                <input type="hidden" name="offering_id" value="{{ $selectedOffering->id }}">
                <input type="hidden" name="assessment_id" id="form-assessment-id" value="">
                
                <div class="bg-white rounded-xl border border-brand-trait overflow-hidden shadow-sm">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-brand-fond text-brand-gris text-xs uppercase tracking-wider border-b border-brand-trait">
                                    <th class="px-6 py-4 font-semibold">Étudiant</th>
                                    <th class="px-6 py-4 font-semibold">Matricule</th>
                                    <th class="px-6 py-4 font-semibold text-center">Note / <span id="max-score-display">20</span></th>
                                    <th class="px-6 py-4 font-semibold text-center">Mention</th>
                                    <th class="px-6 py-4 font-semibold text-right">Action</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-brand-trait">
                                @foreach($students as $index => $student)
                                    <tr class="hover:bg-brand-fond/50 transition-colors group">
                                        <td class="px-6 py-4">
                                            <div class="flex items-center gap-3">
                                                <div class="w-8 h-8 rounded-full bg-brand-pale text-brand-turquoise flex items-center justify-center font-bold text-xs">
                                                    {{ substr($student->first_name, 0, 1) }}{{ substr($student->last_name, 0, 1) }}
                                                </div>
                                                <span class="font-medium text-brand-nuit">{{ $student->first_name }} {{ $student->last_name }}</span>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 text-brand-gris font-mono text-xs">{{ $student->member_number }}</td>
                                        <td class="px-6 py-4 text-center">
                                            <input type="number" 
                                                   name="grades[{{ $student->membership_id }}]" 
                                                   min="0" 
                                                   max="20" 
                                                   step="0.5"
                                                   placeholder="-"
                                                   class="grade-input w-20 px-3 py-1.5 text-center rounded-md border border-brand-trait focus:border-brand-cyan focus:ring-1 focus:ring-brand-cyan outline-none transition font-bold text-brand-nuit"
                                                   oninput="updateMention(this)">
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            <span class="mention-badge text-xs font-semibold px-2.5 py-1 rounded-full bg-gray-100 text-gray-500">-</span>
                                        </td>
                                        <td class="px-6 py-4 text-right">
                                            <span class="save-status text-xs text-brand-gris opacity-0 group-hover:opacity-100 transition">En attente...</span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    {{-- Footer du tableau --}}
                    <div class="bg-brand-fond/50 px-6 py-4 border-t border-brand-trait flex items-center justify-between">
                        <p class="text-xs text-brand-gris">
                            <i class="fa-solid fa-circle-info mr-1"></i> Les notes sont sauvegardées automatiquement ou via le bouton ci-dessous.
                        </p>
                        <button type="submit" class="px-5 py-2.5 bg-brand-nuit text-white rounded-lg text-xs font-semibold hover:bg-opacity-90 transition flex items-center gap-2">
                            <i class="fa-solid fa-floppy-disk"></i> Enregistrer les notes
                        </button>
                    </div>
                </div>
            </form>
            @elseif($selectedOffering && $students->count() === 0)
                <div class="bg-white rounded-xl border border-brand-trait p-10 text-center text-brand-gris">
                    <i class="fa-solid fa-user-slash text-4xl mb-3 text-brand-trait"></i>
                    <p>Aucun étudiant inscrit dans ce cours pour le moment.</p>
                </div>
            @else
                <div class="bg-white rounded-xl border border-brand-trait p-10 text-center text-brand-gris">
                    <i class="fa-solid fa-book-open text-4xl mb-3 text-brand-trait"></i>
                    <p>Sélectionnez un cours ci-dessus pour commencer la saisie.</p>
                </div>
            @endif

        </main>
    </div>

    {{-- Script pour l'interactivité (Calcul de mention en temps réel) --}}
    <script>
        // Mise à jour du max score quand on change d'évaluation
        document.getElementById('assessment-select').addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            const maxScore = selectedOption.dataset.max || 20;
            document.getElementById('max-score-display').textContent = maxScore;
            document.getElementById('form-assessment-id').value = this.value;
            
            // Mettre à jour les attributs max des inputs
            document.querySelectorAll('.grade-input').forEach(input => {
                input.max = maxScore;
            });
        });

        // Calcul de la mention en temps réel
        function updateMention(input) {
            const maxScore = parseFloat(document.getElementById('max-score-display').textContent);
            const note = parseFloat(input.value);
            const badge = input.closest('tr').querySelector('.mention-badge');
            const status = input.closest('tr').querySelector('.save-status');

            if (isNaN(note) || input.value === '') {
                badge.className = 'mention-badge text-xs font-semibold px-2.5 py-1 rounded-full bg-gray-100 text-gray-500';
                badge.textContent = '-';
                status.textContent = 'En attente...';
                status.className = 'save-status text-xs text-brand-gris opacity-0 group-hover:opacity-100 transition';
                return;
            }

            const percentage = (note / maxScore) * 100;
            let text = '';
            let classes = '';

            if (percentage >= 80) { text = 'Excellent'; classes = 'bg-brand-success/10 text-brand-success'; }
            else if (percentage >= 60) { text = 'Bien'; classes = 'bg-blue-50 text-blue-600'; }
            else if (percentage >= 50) { text = 'Passable'; classes = 'bg-yellow-50 text-yellow-600'; }
            else if (percentage >= 40) { text = 'Insuffisant'; classes = 'bg-orange-50 text-orange-600'; }
            else { text = 'Échec'; classes = 'bg-red-50 text-brand-danger'; }

            badge.className = `mention-badge text-xs font-semibold px-2.5 py-1 rounded-full ${classes}`;
            badge.textContent = text;
            
            status.textContent = 'Modifié (non sauvegardé)';
            status.className = 'save-status text-xs text-brand-cyan font-medium';
        }
    </script>
</body>
</html>