<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Résultats de Délibération — SHARKLINK</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: { nuit: '#0b1f3a', cyan: '#22d3ee', fond: '#f4fbff', trait: '#e3edf3', gris: '#5f6f84', success: '#16845b', danger: '#ef4444' }
                    },
                    fontFamily: { sans: ['Inter', 'sans-serif'] }
                }
            }
        }
    </script>
</head>
<body class="bg-brand-fond font-sans text-sm text-brand-nuit min-h-screen">
    <div class="max-w-7xl mx-auto p-6 md:p-9">
        <header class="mb-6 flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-brand-nuit">Résultats de Délibération</h1>
                <p class="text-xs text-brand-gris mt-1">Calcul automatique des moyennes et mentions</p>
            </div>
            <a href="{{ route('deliberation.index') }}" class="text-xs text-brand-gris hover:text-brand-nuit flex items-center gap-2 bg-white px-3 py-2 rounded-lg border border-brand-trait transition">
                <i class="fa-solid fa-arrow-left"></i> Retour
            </a>
        </header>

        {{-- Statistiques globales --}}
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
            <div class="bg-white rounded-xl border border-brand-trait p-4">
                <p class="text-xs text-brand-gris">Total Étudiants</p>
                <p class="text-2xl font-bold text-brand-nuit mt-1">{{ $stats['total_students'] }}</p>
            </div>
            <div class="bg-white rounded-xl border border-brand-trait p-4">
                <p class="text-xs text-brand-gris">Admis</p>
                <p class="text-2xl font-bold text-brand-success mt-1">{{ $stats['admitted'] }}</p>
            </div>
            <div class="bg-white rounded-xl border border-brand-trait p-4">
                <p class="text-xs text-brand-gris">Ajournés</p>
                <p class="text-2xl font-bold text-brand-danger mt-1">{{ $stats['failed'] }}</p>
            </div>
            <div class="bg-white rounded-xl border border-brand-trait p-4">
                <p class="text-xs text-brand-gris">Moyenne de classe</p>
                <p class="text-2xl font-bold text-brand-cyan mt-1">{{ number_format($stats['class_average'], 2) }}/20</p>
            </div>
        </div>

        <form method="POST" action="{{ route('deliberation.store') }}">
            @csrf
            <input type="hidden" name="offering_id" value="{{ $offeringId }}">

            <div class="bg-white rounded-xl border border-brand-trait overflow-hidden shadow-sm">
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead class="bg-brand-fond text-brand-gris text-xs uppercase">
                            <tr>
                                <th class="px-6 py-4 font-semibold">Étudiant</th>
                                <th class="px-6 py-4 font-semibold">Matricule</th>
                                <th class="px-6 py-4 font-semibold text-center">Moyenne</th>
                                <th class="px-6 py-4 font-semibold text-center">Mention</th>
                                <th class="px-6 py-4 font-semibold text-center">Décision</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-brand-trait">
                            @foreach($results as $student)
                                <tr class="hover:bg-brand-fond/50 transition">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            <div class="w-8 h-8 rounded-full bg-brand-pale text-brand-turquoise flex items-center justify-center font-bold text-xs">
                                                {{ substr($student->first_name, 0, 1) }}{{ substr($student->last_name, 0, 1) }}
                                            </div>
                                            <span class="font-medium">{{ $student->first_name }} {{ $student->last_name }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-brand-gris font-mono text-xs">{{ $student->member_number }}</td>
                                    <td class="px-6 py-4 text-center font-bold text-lg">
                                        {{ $student->average ? number_format($student->average, 2) : '-' }}/20
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <span class="px-3 py-1 rounded-full text-xs font-bold {{ $student->mention_color }}">
                                            {{ $student->mention }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <span class="px-3 py-1 rounded-full text-xs font-bold {{ $student->decision_color }}">
                                            {{ $student->decision }}
                                        </span>
                                        <input type="hidden" name="decisions[{{ $student->membership_id }}][status]" value="{{ $student->decision }}">
                                        <input type="hidden" name="decisions[{{ $student->membership_id }}][average]" value="{{ $student->average }}">
                                        <input type="hidden" name="decisions[{{ $student->membership_id }}][mention]" value="{{ $student->mention }}">
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="bg-brand-fond/50 px-6 py-4 border-t border-brand-trait flex items-center justify-between">
                    <p class="text-xs text-brand-gris">
                        <i class="fa-solid fa-circle-info mr-1"></i> Vérifiez les résultats avant d'enregistrer la délibération officielle.
                    </p>
                    <button type="submit" class="px-6 py-2.5 bg-brand-nuit text-white rounded-lg text-xs font-bold hover:bg-opacity-90 transition flex items-center gap-2">
                        <i class="fa-solid fa-check-circle"></i> Enregistrer la délibération
                    </button>
                </div>
            </div>
        </form>
    </div>
</body>
</html>