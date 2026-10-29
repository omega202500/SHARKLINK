<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Délibération — SHARKLINK</title>
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
    <div class="max-w-6xl mx-auto p-6 md:p-9">
        <header class="mb-8 flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-brand-nuit">Délibération Pédagogique</h1>
                <p class="text-xs text-brand-gris mt-1">{{ $context['establishment']->name }} · {{ $context['user']->first_name }} {{ $context['user']->last_name }}</p>
            </div>
            <a href="{{ route('dashboard') }}" class="text-xs text-brand-gris hover:text-brand-nuit flex items-center gap-2 bg-white px-3 py-2 rounded-lg border border-brand-trait transition">
                <i class="fa-solid fa-arrow-left"></i> Retour
            </a>
        </header>

        @if(session('success'))
            <div class="bg-brand-success/10 text-brand-success border border-brand-success/20 rounded-lg p-4 mb-6 flex items-center gap-3">
                <i class="fa-solid fa-circle-check"></i> {{ session('success') }}
            </div>
        @endif

        <div class="bg-white rounded-xl border border-brand-trait overflow-hidden shadow-sm">
            <div class="p-6 border-b border-brand-trait">
                <h2 class="font-bold text-lg">Cours avec notes publiées</h2>
                <p class="text-xs text-brand-gris mt-1">Sélectionnez un cours pour lancer la délibération automatique</p>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="bg-brand-fond text-brand-gris text-xs uppercase">
                        <tr>
                            <th class="px-6 py-4 font-semibold">Cours</th>
                            <th class="px-6 py-4 font-semibold">Période</th>
                            <th class="px-6 py-4 font-semibold text-center">Étudiants notés</th>
                            <th class="px-6 py-4 font-semibold text-center">Date de publication</th>
                            <th class="px-6 py-4 font-semibold text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-brand-trait">
                        @forelse($offerings as $offering)
                            <tr class="hover:bg-brand-fond/50 transition">
                                <td class="px-6 py-4 font-medium">{{ $offering->title }}</td>
                                <td class="px-6 py-4 text-brand-gris">{{ $offering->term }}</td>
                                <td class="px-6 py-4 text-center">
                                    <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-brand-cyan/10 text-brand-cyan font-bold text-xs">
                                        {{ $offering->student_count ?? 0 }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center text-xs text-brand-gris">
                                    {{ $offering->published_at ? \Carbon\Carbon::parse($offering->published_at)->format('d M Y H:i') : '-' }}
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <a href="{{ route('deliberation.calculate', ['offering_id' => $offering->id]) }}" 
                                       class="inline-flex items-center gap-2 px-4 py-2 bg-brand-nuit text-white rounded-lg text-xs font-semibold hover:bg-opacity-90 transition">
                                        <i class="fa-solid fa-calculator"></i> Délibérer
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-10 text-center text-brand-gris">
                                    <i class="fa-solid fa-inbox text-3xl mb-2 opacity-50"></i>
                                    <p>Aucun cours avec des notes publiées pour le moment.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>