<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Mes Cours — SHARKLINK</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: { nuit: '#0b1f3a', cyan: '#22d3ee', fond: '#f4fbff', trait: '#e3edf3', gris: '#5f6f84' }
                    },
                    fontFamily: { sans: ['Inter', 'sans-serif'] }
                }
            }
        }
    </script>
</head>
<body class="bg-brand-fond font-sans text-brand-nuit min-h-screen p-6 md:p-10">
    <div class="max-w-5xl mx-auto">
        {{-- En-tête --}}
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
            <div>
                <h1 class="text-2xl font-bold">Mes Cours</h1>
                <p class="text-brand-gris text-sm mt-1">
                    {{ $establishment->name }} · {{ $user->first_name }} {{ $user->last_name }}
                </p>
            </div>
            <a href="{{ route('dashboard') }}" class="flex items-center justify-center gap-2 px-4 py-2 bg-white border border-brand-trait rounded-lg text-sm font-medium hover:bg-brand-cyan/10 hover:border-brand-cyan transition">
                <i class="fa-solid fa-arrow-left"></i> Retour au tableau de bord
            </a>
        </div>

        {{-- Liste des cours --}}
        <div class="bg-white rounded-xl border border-brand-trait shadow-sm overflow-hidden">
            @if($courses->isEmpty())
                <div class="p-10 text-center text-brand-gris">
                    <i class="fa-solid fa-book-open text-4xl mb-3 text-brand-trait"></i>
                    <p>Aucun cours ne vous est actuellement assigné dans cet établissement.</p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead class="bg-brand-fond text-brand-gris uppercase text-xs">
                            <tr>
                                <th class="px-6 py-4 font-semibold">Code</th>
                                <th class="px-6 py-4 font-semibold">Intitulé du cours</th>
                                <th class="px-6 py-4 font-semibold">Période</th>
                                <th class="px-6 py-4 font-semibold text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-brand-trait">
                            @foreach($courses as $course)
                                <tr class="hover:bg-brand-fond/50 transition">
                                    <td class="px-6 py-4 font-mono font-medium text-brand-cyan">{{ $course->course_code ?? 'N/A' }}</td>
                                    <td class="px-6 py-4 font-semibold">{{ $course->course_title }}</td>
                                    <td class="px-6 py-4 text-brand-gris">{{ $course->term }}</td>
                                    <td class="px-6 py-4 text-right">
                                        <a href="#" class="text-brand-cyan hover:underline font-medium">Gérer</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</body>
</html>