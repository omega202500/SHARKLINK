<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Saisie des Notes — SHARKLINK</title>

    <script src="https://cdn.tailwindcss.com"></script>

    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap"
        rel="stylesheet"
    >

    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"
    >

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
                            trait: '#e3edf3',
                            gris: '#5f6f84',
                            success: '#16845b',
                            danger: '#ef4444'
                        }
                    },
                    fontFamily: {
                        sans: ['Inter', 'sans-serif']
                    }
                }
            }
        };
    </script>
</head>

<body class="min-h-screen flex flex-col md:flex-row bg-brand-fond font-sans text-sm text-brand-nuit">

    {{-- ====================================================== --}}
    {{-- BARRE LATÉRALE --}}
    {{-- ====================================================== --}}

    <aside class="hidden md:flex w-[260px] min-h-screen bg-brand-nuit text-[#d3dcea] p-4 flex-col gap-4 shrink-0">

        <div class="text-[19px] font-extrabold tracking-[0.08em] text-brand-cyan px-2 mb-4">
            SHARKLINK
        </div>

        <nav class="flex flex-col gap-1">

            <a
                href="{{ route('dashboard') }}"
                class="flex items-center gap-3 px-4 py-2.5 rounded-lg hover:bg-white/5 text-white transition"
            >
                <i class="fa-solid fa-gauge-high w-5 text-center"></i>
                Tableau de bord
            </a>

            <a
                href="{{ route('teacher.courses') }}"
                class="flex items-center gap-3 px-4 py-2.5 rounded-lg hover:bg-white/5 text-white transition"
            >
                <i class="fa-solid fa-book-open w-5 text-center"></i>
                Mes cours
            </a>

            <a
                href="{{ route('teacher.grades') }}"
                class="flex items-center gap-3 px-4 py-2.5 rounded-lg bg-brand-cyan/10 text-brand-cyan border border-brand-cyan/30 font-semibold"
            >
                <i class="fa-solid fa-pen-to-square w-5 text-center"></i>
                Saisie des notes
            </a>

            <a
                href="{{ route('teacher.timetable') }}"
                class="flex items-center gap-3 px-4 py-2.5 rounded-lg hover:bg-white/5 text-white transition"
            >
                <i class="fa-solid fa-calendar-days w-5 text-center"></i>
                Emploi du temps
            </a>

        </nav>
    </aside>


    {{-- ====================================================== --}}
    {{-- CONTENU PRINCIPAL --}}
    {{-- ====================================================== --}}

    <div class="flex-1 flex flex-col min-w-0">

        {{-- EN-TÊTE --}}

        <header class="h-[72px] bg-white border-b border-brand-trait flex items-center justify-between px-4 md:px-9 shrink-0">

            <div>
                <h1 class="text-lg font-bold text-brand-nuit">
                    Saisie des notes
                </h1>

                <p class="text-xs text-brand-gris mt-0.5">
                    Enseignant · {{ $context['establishment']->name }}
                </p>
            </div>

            <a
                href="{{ route('dashboard') }}"
                class="text-xs text-brand-gris hover:text-brand-nuit flex items-center gap-2 bg-brand-fond px-3 py-2 rounded-lg border border-brand-trait transition"
            >
                <i class="fa-solid fa-arrow-left"></i>
                <span class="hidden sm:inline">Retour</span>
            </a>

        </header>


        {{-- CONTENU DE LA PAGE --}}

        <main class="p-4 md:p-9 flex flex-col gap-6 flex-1">

            {{-- MESSAGE DE SUCCÈS --}}

            @if(session('success'))
                <div class="bg-brand-success/10 text-brand-success border border-brand-success/20 rounded-lg p-4 flex items-center gap-3">
                    <i class="fa-solid fa-circle-check"></i>
                    {{ session('success') }}
                </div>
            @endif


            {{-- ERREURS DE VALIDATION --}}

            @if($errors->any())
                <div class="bg-red-50 text-brand-danger border border-red-200 rounded-lg p-4">

                    <ul class="list-disc pl-5 space-y-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>

                </div>
            @endif


            {{-- ====================================================== --}}
            {{-- SÉLECTION DU COURS ET DE L'ÉVALUATION --}}
            {{-- ====================================================== --}}

            <div class="bg-white rounded-xl border border-brand-trait p-5 flex flex-col md:flex-row gap-4 items-end">

                {{-- SÉLECTION DU COURS --}}

                <div class="flex-1 w-full">

                    <label
                        for="offering-select"
                        class="block text-xs font-semibold text-brand-gris mb-1.5"
                    >
                        1. Sélectionner le cours
                    </label>

                    <form
                        method="GET"
                        action="{{ route('teacher.grades') }}"
                    >

                        <select
                            id="offering-select"
                            name="offering_id"
                            onchange="this.form.submit()"
                            class="w-full px-4 py-2.5 rounded-lg border border-brand-trait bg-brand-fond text-brand-nuit font-medium focus:outline-none focus:border-brand-cyan transition"
                        >

                            @forelse($offerings as $offering)

                                <option
                                    value="{{ $offering->id }}"
                                    {{ $selectedOffering && $selectedOffering->id == $offering->id ? 'selected' : '' }}
                                >
                                    {{ $offering->title }} ({{ $offering->term }})
                                </option>

                            @empty

                                <option value="">
                                    Aucun cours disponible
                                </option>

                            @endforelse

                        </select>

                    </form>

                </div>


                {{-- SÉLECTION DE L'ÉVALUATION --}}

                <div class="flex-1 w-full">

                    <label
                        for="assessment-select"
                        class="block text-xs font-semibold text-brand-gris mb-1.5"
                    >
                        2. Sélectionner l'évaluation
                    </label>

                    <select
                        id="assessment-select"
                        class="w-full px-4 py-2.5 rounded-lg border border-brand-trait bg-brand-fond text-brand-nuit font-medium focus:outline-none focus:border-brand-cyan transition"
                    >

                        <option value="">
                            -- Choisir une évaluation --
                        </option>

                        @foreach($assessments as $assessment)

                            <option
                                value="{{ $assessment->id }}"
                                data-max="{{ $assessment->max_score }}"
                                {{ $selectedAssessmentId == $assessment->id ? 'selected' : '' }}
                            >
                                {{ $assessment->title }}
                                (Coeff. {{ $assessment->coefficient }})
                            </option>

                        @endforeach

                    </select>

                </div>

            </div>


            {{-- ====================================================== --}}
            {{-- TABLEAU DES NOTES --}}
            {{-- ====================================================== --}}

            @if($selectedOffering && $students->count() > 0)

                {{-- CALCUL DE L'ÉTAT DE PUBLICATION --}}

                @php
                    $isPublished = DB::table('grade_publications')
                        ->where('course_offering_id', $selectedOffering->id)
                        ->exists();
                @endphp


                {{-- FORMULAIRE D'ENREGISTREMENT DES NOTES --}}

                <form
                    method="POST"
                    action="{{ route('teacher.grades.store') }}"
                    id="grades-form"
                >

                    @csrf

                    <input
                        type="hidden"
                        name="offering_id"
                        value="{{ $selectedOffering->id }}"
                    >

                    <input
                        type="hidden"
                        name="assessment_id"
                        id="form-assessment-id"
                        value="{{ $selectedAssessmentId }}"
                    >


                    <div class="bg-white rounded-xl border border-brand-trait overflow-hidden shadow-sm">

                        {{-- TABLEAU --}}

                        <div class="overflow-x-auto">

                            <table class="w-full text-left border-collapse">

                                <thead>

                                    <tr class="bg-brand-fond text-brand-gris text-xs uppercase tracking-wider border-b border-brand-trait">

                                        <th class="px-6 py-4 font-semibold">
                                            Étudiant
                                        </th>

                                        <th class="px-6 py-4 font-semibold">
                                            Matricule
                                        </th>

                                        <th class="px-6 py-4 font-semibold text-center">
                                            Note /
                                            <span id="max-score-display">20</span>
                                        </th>

                                        <th class="px-6 py-4 font-semibold text-center">
                                            Mention
                                        </th>

                                    </tr>

                                </thead>


                                <tbody class="divide-y divide-brand-trait">

                                    @foreach($students as $student)

                                        <tr class="hover:bg-brand-fond/50 transition-colors">

                                            {{-- IDENTITÉ --}}

                                            <td class="px-6 py-4">

                                                <div class="flex items-center gap-3">

                                                    <div class="w-8 h-8 rounded-full bg-brand-pale text-brand-turquoise flex items-center justify-center font-bold text-xs shrink-0">

                                                        {{ substr($student->first_name, 0, 1) }}{{ substr($student->last_name, 0, 1) }}

                                                    </div>

                                                    <span class="font-medium text-brand-nuit whitespace-nowrap">

                                                        {{ $student->first_name }}
                                                        {{ $student->last_name }}

                                                    </span>

                                                </div>

                                            </td>


                                            {{-- MATRICULE --}}

                                            <td class="px-6 py-4 text-brand-gris font-mono text-xs">

                                                {{ $student->member_number }}

                                            </td>


                                            {{-- NOTE --}}

                                            <td class="px-6 py-4 text-center">

                                                <input
                                                    type="number"
                                                    name="grades[{{ $student->membership_id }}]"
                                                    min="0"
                                                    max="20"
                                                    step="0.5"
                                                    value="{{ $existingGrades->get($student->membership_id, '') }}"
                                                    placeholder="-"
                                                    class="grade-input w-20 px-3 py-1.5 text-center rounded-md border border-brand-trait focus:border-brand-cyan outline-none transition font-bold text-brand-nuit"
                                                >

                                            </td>


                                            {{-- MENTION --}}

                                            <td class="px-6 py-4 text-center">

                                                <span class="mention-badge text-xs font-semibold px-2.5 py-1 rounded-full bg-gray-100 text-gray-500">
                                                    -
                                                </span>

                                            </td>

                                        </tr>

                                    @endforeach

                                </tbody>

                            </table>

                        </div>


                        {{-- PIED DU TABLEAU : ÉTAT ET ACTIONS --}}

                        <div class="bg-brand-fond/50 px-4 md:px-6 py-4 border-t border-brand-trait flex flex-col md:flex-row md:items-center justify-between gap-4">

                            {{-- ÉTAT DE PUBLICATION --}}

                            <p class="text-xs text-brand-gris">

                                @if($isPublished)

                                    <i class="fa-solid fa-check-circle text-brand-success mr-1"></i>

                                    Notes déjà publiées aux étudiants.

                                @else

                                    <i class="fa-solid fa-lock mr-1"></i>

                                    Les notes sont visibles uniquement par vous.

                                @endif

                            </p>


                            {{-- BOUTONS D'ACTION --}}

                            <div class="flex flex-col sm:flex-row gap-3">

                                {{-- ENREGISTRER --}}

                                <button
                                    type="submit"
                                    id="save-btn"
                                    class="px-5 py-2.5 bg-brand-nuit text-white rounded-lg text-xs font-semibold hover:bg-opacity-90 transition flex items-center justify-center gap-2"
                                >

                                    <i class="fa-solid fa-floppy-disk"></i>

                                    <span>Enregistrer les notes</span>

                                </button>


                                {{-- PUBLIER --}}

                                @if(!$isPublished)

                                    <button
                                        type="submit"
                                        form="publish-form"
                                        id="publish-btn"
                                        class="px-5 py-2.5 bg-brand-cyan text-brand-nuit rounded-lg text-xs font-bold hover:bg-cyan-300 transition flex items-center justify-center gap-2"
                                    >

                                        <i class="fa-solid fa-paper-plane"></i>

                                        Publier aux étudiants

                                    </button>

                                @else

                                    <span class="px-5 py-2.5 bg-green-100 text-green-700 rounded-lg text-xs font-bold flex items-center justify-center gap-2">

                                        <i class="fa-solid fa-check-circle"></i>

                                        Publié

                                    </span>

                                @endif

                            </div>

                        </div>

                    </div>

                </form>


                {{-- ====================================================== --}}
                {{-- FORMULAIRE DE PUBLICATION : SÉPARÉ DU FORMULAIRE NOTES --}}
                {{-- ====================================================== --}}

                @if(!$isPublished)

                    <form
                        id="publish-form"
                        method="POST"
                        action="{{ route('teacher.grades.publish') }}"
                        onsubmit="return confirm('Publier toutes les notes de ce cours ?');"
                    >

                        @csrf

                        <input
                            type="hidden"
                            name="offering_id"
                            value="{{ $selectedOffering->id }}"
                        >

                    </form>

                @endif


            {{-- ====================================================== --}}
            {{-- AUCUN ÉTUDIANT --}}
            {{-- ====================================================== --}}

            @elseif($selectedOffering && $students->count() === 0)

                <div class="bg-white rounded-xl border border-brand-trait p-10 text-center text-brand-gris">

                    <i class="fa-solid fa-user-slash text-4xl mb-3 text-brand-trait"></i>

                    <p>
                        Aucun étudiant inscrit dans ce cours pour le moment.
                    </p>

                </div>


            {{-- ====================================================== --}}
            {{-- AUCUN COURS SÉLECTIONNÉ --}}
            {{-- ====================================================== --}}

            @else

                <div class="bg-white rounded-xl border border-brand-trait p-10 text-center text-brand-gris">

                    <i class="fa-solid fa-book-open text-4xl mb-3 text-brand-trait"></i>

                    <p>
                        Sélectionnez un cours ci-dessus pour commencer la saisie.
                    </p>

                </div>

            @endif

        </main>

    </div>


    {{-- ====================================================== --}}
    {{-- JAVASCRIPT : NOTES, MENTIONS ET SOUMISSION --}}
    {{-- ====================================================== --}}

    <script>
        const assessmentSelect = document.getElementById('assessment-select');
        const maxDisplay = document.getElementById('max-score-display');
        const assessmentInput = document.getElementById('form-assessment-id');

        /**
         * Récupère le barème de l'évaluation sélectionnée.
         */
        function getMax() {
            if (!assessmentSelect || assessmentSelect.selectedIndex < 0) {
                return 20;
            }

            const option = assessmentSelect.options[assessmentSelect.selectedIndex];

            const max = parseFloat(option?.dataset.max);

            return Number.isFinite(max) && max > 0 ? max : 20;
        }


        /**
         * Calcule la mention correspondant à une note.
         */
        function updateMention(input) {
            const maxScore = getMax();
            const note = parseFloat(input.value);

            const badge = input.closest('tr')?.querySelector('.mention-badge');

            if (!badge) {
                return;
            }

            if (input.value === '' || !Number.isFinite(note)) {
                badge.className =
                    'mention-badge text-xs font-semibold px-2.5 py-1 rounded-full bg-gray-100 text-gray-500';

                badge.textContent = '-';

                return;
            }

            if (note < 0 || note > maxScore) {
                badge.className =
                    'mention-badge text-xs font-semibold px-2.5 py-1 rounded-full bg-red-100 text-red-700';

                badge.textContent = 'Note invalide';

                return;
            }

            const percentage = (note / maxScore) * 100;

            let mention;
            let classes;

            if (percentage >= 80) {
                mention = 'Excellent';
                classes = 'bg-green-100 text-green-700';
            } else if (percentage >= 60) {
                mention = 'Bien';
                classes = 'bg-blue-100 text-blue-700';
            } else if (percentage >= 50) {
                mention = 'Passable';
                classes = 'bg-yellow-100 text-yellow-700';
            } else if (percentage >= 40) {
                mention = 'Insuffisant';
                classes = 'bg-orange-100 text-orange-700';
            } else {
                mention = 'Échec';
                classes = 'bg-red-100 text-red-700';
            }

            badge.className =
                `mention-badge text-xs font-semibold px-2.5 py-1 rounded-full ${classes}`;

            badge.textContent = mention;
        }


        /**
         * Met à jour le barème et les mentions.
         */
        function refreshGrades() {
            const maxScore = getMax();

            if (maxDisplay) {
                maxDisplay.textContent = maxScore;
            }

            document.querySelectorAll('.grade-input').forEach(input => {
                input.max = maxScore;
                updateMention(input);
            });
        }


        /**
         * Recharge la page lors du changement d'évaluation.
         * Le cours sélectionné est conservé.
         */
        if (assessmentSelect) {
            assessmentSelect.addEventListener('change', function () {
                const url = new URL(window.location.href);

                const offeringSelect = document.getElementById('offering-select');

                if (offeringSelect && offeringSelect.value) {
                    url.searchParams.set('offering_id', offeringSelect.value);
                }

                if (this.value) {
                    url.searchParams.set('assessment_id', this.value);
                } else {
                    url.searchParams.delete('assessment_id');
                }

                window.location.href = url.toString();
            });
        }


        /**
         * Met à jour la mention dès que la note change.
         */
        document.querySelectorAll('.grade-input').forEach(input => {
            input.addEventListener('input', function () {
                updateMention(this);
            });
        });


        /**
         * Vérifie les données avant l'enregistrement.
         */
        const gradesForm = document.getElementById('grades-form');

        if (gradesForm) {
            gradesForm.addEventListener('submit', function (event) {
                const assessmentId = assessmentInput?.value;

                if (!assessmentId) {
                    event.preventDefault();

                    alert('Choisis d’abord une évaluation.');

                    return;
                }

                const maxScore = getMax();

                const invalidInput = Array.from(
                    document.querySelectorAll('.grade-input')
                ).find(input => {
                    if (input.value.trim() === '') {
                        return false;
                    }

                    const value = Number(input.value);

                    return !Number.isFinite(value)
                        || value < 0
                        || value > maxScore;
                });

                if (invalidInput) {
                    event.preventDefault();

                    alert(
                        `Chaque note doit être comprise entre 0 et ${maxScore}.`
                    );

                    invalidInput.focus();

                    return;
                }

                const saveButton = document.getElementById('save-btn');

                if (saveButton) {
                    saveButton.disabled = true;

                    saveButton.classList.add('opacity-60', 'cursor-not-allowed');

                    const label = saveButton.querySelector('span');

                    if (label) {
                        label.textContent = 'Enregistrement...';
                    }
                }
            });
        }


        /**
         * Initialisation au chargement de la page.
         */
        refreshGrades();
    </script>

</body>
</html>
