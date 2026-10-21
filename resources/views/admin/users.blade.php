<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Administration — SHARKLINK</title>
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
<body class="bg-brand-fond font-sans text-brand-nuit min-h-screen flex items-center justify-center p-6">
    <div class="bg-white rounded-xl border border-brand-trait shadow-sm p-10 text-center max-w-md w-full">
        <div class="w-16 h-16 bg-brand-fond rounded-full flex items-center justify-center mx-auto mb-4">
            <i class="fa-solid fa-person-digging text-2xl text-brand-cyan"></i>
        </div>
        
        {{-- Change le titre ici selon la page --}}
        <h1 class="text-xl font-bold mb-2">Gestion des Utilisateurs</h1>
        
        <p class="text-brand-gris text-sm mb-6">Cette fonctionnalité d'administration est en cours de développement.</p>
        
        <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-brand-nuit text-white rounded-lg text-sm font-medium hover:bg-opacity-90 transition">
            <i class="fa-solid fa-arrow-left"></i> Retour au tableau de bord
        </a>
    </div>
</body>
</html>