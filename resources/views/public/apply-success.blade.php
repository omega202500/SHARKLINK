<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Candidature envoyée — SHARKLINK</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = { theme: { extend: { colors: { brand: { nuit: '#0b1f3a', cyan: '#22d3ee' } }, fontFamily: { sans: ['Inter', 'sans-serif'] } } } }
    </script>
</head>
<body class="bg-[#f4fbff] font-sans min-h-screen flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-xl p-10 max-w-md w-full text-center border border-gray-100">
        <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-6">
            <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
        </div>
        <h1 class="text-2xl font-bold text-[#0b1f3a] mb-2">Candidature reçue !</h1>
        <p class="text-gray-500 text-sm mb-6">Merci {{ $application->first_name }}, votre dossier a été soumis avec succès. Notre équipe des admissions l'examinera sous 48h.</p>
        
        <div class="bg-[#f4fbff] rounded-lg p-4 mb-6 border border-gray-200">
            <p class="text-xs text-gray-500 uppercase tracking-wide font-semibold mb-1">Votre numéro de dossier</p>
            <p class="text-xl font-bold text-[#0b1f3a] tracking-wider">#{{ str_pad($application->id, 6, '0', STR_PAD_LEFT) }}</p>
        </div>

        <p class="text-xs text-gray-400 mb-6">Un email de confirmation a été envoyé à <strong>{{ $application->email }}</strong>.</p>
        
        <a href="{{ route('login') }}" class="block w-full py-3 rounded-lg bg-[#0b1f3a] text-white font-semibold hover:bg-opacity-90 transition">
            Se connecter à mon espace
        </a>
    </div>
</body>
</html>