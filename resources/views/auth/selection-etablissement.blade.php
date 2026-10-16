<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Choix de l’établissement — SHARKLINK</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Arimo:wght@400;700&family=Inter:wght@700;800&display=swap" rel="stylesheet">

    <style>
        :root {
            --bleu-nuit: #0b1f3a;
            --gris: #6b7280;
            --fond: #f4fbff;
            --bordure: #d1d5db;
            --bordure-badge: #dce8ef;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            background: var(--fond);
            font-family: 'Arimo', Arial, sans-serif;
            color: var(--bleu-nuit);
            min-height: 100vh;
        }

        /* ---------- En-tête ---------- */
        .entete {
            height: 96px;
            padding: 0 48px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .marque {
            display: flex;
            align-items: center;
            gap: 11px;
            text-decoration: none;
        }

        .marque img { width: 43px; height: 36px; display: block; }

        .marque span {
            font-family: 'Inter', Arial, sans-serif;
            font-weight: 800;
            font-size: 23px;
            color: var(--bleu-nuit);
        }

        .deconnexion {
            background: none;
            border: 0;
            cursor: pointer;
            font-family: 'Arimo', Arial, sans-serif;
            font-size: 15px;
            color: var(--gris);
        }

        .deconnexion:hover { color: var(--bleu-nuit); }

        /* ---------- Contenu ---------- */
        .choix {
            min-height: calc(100vh - 96px);
            padding: 64px 64px 160px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }

        .selection {
            width: 100%;
            max-width: 944px;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 44px;
        }

        .accueil {
            width: 100%;
            text-align: center;
            display: flex;
            flex-direction: column;
            gap: 16px;
        }

        .accueil h1 {
            font-size: 40px;
            line-height: 48px;
            font-weight: 700;
            color: var(--bleu-nuit);
        }

        .accueil p {
            font-size: 18px;
            line-height: 26px;
            color: var(--gris);
        }

        /* ---------- Cartes ---------- */
        .etablissements {
            width: 100%;
            display: flex;
            flex-wrap: wrap;
            gap: 24px;
        }

        .etablissement {
            flex: 1 0 0;
            min-width: 360px;
            height: 308px;
            padding: 28px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            background: #fff;
            border: 1px solid var(--bordure);
            border-radius: 16px;
            box-shadow: 0 8px 28px rgba(11, 31, 58, 0.04);
            text-decoration: none;
            color: inherit;
            transition: transform .15s ease, box-shadow .15s ease;
        }

        .etablissement:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 32px rgba(11, 31, 58, 0.10);
        }

        .etablissement:focus-visible {
            outline: 2px solid var(--bleu-nuit);
            outline-offset: 3px;
        }

        .etablissement:focus-within {
            outline: 2px solid var(--bleu-nuit);
            outline-offset: 3px;
        }

        .infos {
            display: flex;
            flex-direction: column;
            gap: 24px;
        }

        .infos > img { width: 40px; height: 40px; display: block; }

        .identite {
            display: flex;
            flex-direction: column;
            gap: 16px;
        }

        .identite h2 {
            min-height: 56px;
            font-family: 'Inter', Arial, sans-serif;
            font-weight: 700;
            font-size: 22px;
            line-height: 28px;
            color: var(--bleu-nuit);
        }

        .roles {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }

        .badge {
            padding: 7px 10px;
            background: var(--fond);
            border: 1px solid var(--bordure-badge);
            border-radius: 12px;
            font-size: 13px;
            line-height: 16px;
            white-space: nowrap;
            color: var(--bleu-nuit);
        }

        .acces {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0;
            border: 0;
            background: none;
            cursor: pointer;
            font-family: 'Arimo', Arial, sans-serif;
            font-weight: 700;
            font-size: 15px;
            color: var(--bleu-nuit);
        }

        .acces img { width: 22px; height: 22px; display: block; }

        /* ---------- Mobile ---------- */
        @media (max-width: 768px) {
            .entete { padding: 0 20px; }
            .choix { padding: 40px 20px 80px; justify-content: flex-start; }
            .accueil h1 { font-size: 30px; line-height: 38px; }
            .accueil p { font-size: 16px; }
            .etablissement { min-width: 100%; height: auto; min-height: 260px; gap: 32px; }
        }
    </style>
</head>
<body>

    {{-- En-tête --}}
    <header class="entete">
        <a href="{{ url('/') }}" class="marque">
            <img src="{{ asset('images/requin.svg') }}" alt="">
            <span>SHARKLINK</span>
        </a>
    </header>

    {{-- Choix de l'établissement --}}
    <main class="choix">
        <div class="selection">

            <div class="accueil">
                <h1>Bienvenue, {{ $user->first_name }}.</h1>
                <p>Dans quel établissement souhaitez-vous travailler aujourd'hui ?</p>
            </div>

            <div class="etablissements">
                @foreach ($etablissements as $etablissement)
                    <form method="POST" action="{{ route('establishment.switch') }}" class="etablissement">
                        @csrf
                        <input type="hidden" name="establishment_id" value="{{ $etablissement->id }}">

                        <div class="infos">
                            {{-- icone = nom du fichier SVG dans public/images (ex : school, graduation-cap) --}}
                            <img src="{{ asset('images/' . $etablissement->icone . '.svg') }}" alt="">

                            <div class="identite">
                                <h2>{{ $etablissement->nom }}</h2>

                                <div class="roles">
                                    @foreach ($etablissement->roles as $role)
                                        <span class="badge">
                                            {{ $role === 'Chef de département' ? '👑' : '👤' }}
                                            {{ count($etablissement->roles) === 1 ? 'Rôle : ' : '' }}{{ $role }}
                                        </span>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <button type="submit" class="acces">
                            <span>Sélectionner cet établissement</span>
                            <img src="{{ asset('images/arrow-right.svg') }}" alt="">
                        </button>
                    </form>
                @endforeach
            </div>

        </div>
    </main>

</body>
</html>