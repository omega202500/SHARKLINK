{{--
    Tableau de bord — SHARKLINK (maquette Figma « Espace de travail »)

    Variables attendues (envoyées par DashboardController@index) :
      $user          : utilisateur connecté (name, optionnel : prenom, avatar_url)
      $roleLabel     : libellé du rôle affiché sous le nom (ex. « Administrateur »)
      $annee         : année académique (ex. « 2026-2027 »)
      $notifications : nombre de notifications non lues (int)
      $stats         : 4 indicateurs [label, valeur, icone, unite?, evolution?, periode]
      $frais         : pourcentages [collecte, attente, retard]
      $presences     : liste [nom, departement, heure, present(bool), photo?]
      $examens       : [moyenne (0-100), meilleur, sgpa]
      $resultats     : liste [nom, matricule, programme, examen, note, reussi(bool)]

    Images à placer dans public/images/ : voir la liste donnée avec ce fichier.
--}}
@php
    $prenom = $user->prenom ?? \Illuminate\Support\Str::before($user->name, ' ');
    $avatar = $user->avatar_url ?? asset('images/portrait-defaut.png');

    // Géométrie des anneaux (diamètre 172, épaisseur 16)
    $rayon = 78;
    $circ  = 2 * M_PI * $rayon;

    // Anneau « Collecte des frais » : 3 arcs placés bout à bout à partir de midi
    $couleursFrais = ['collecte' => '#22d3ee', 'attente' => '#f59e0b', 'retard' => '#ef4444'];
    $arcsFrais = [];
    $decalage = 0;
    foreach ($couleursFrais as $cle => $couleur) {
        $pct = (float) ($frais[$cle] ?? 0);
        $long = $pct / 100 * $circ;
        $arcsFrais[] = [
            'cle'     => $cle,
            'pct'     => $pct,
            'couleur' => $couleur,
            'trait'   => max($long - 2, 0),
            'decalage'=> $decalage,
        ];
        $decalage += $long;
    }
    $libellesFrais = ['collecte' => 'Collecté', 'attente' => 'Attente', 'retard' => 'Retard'];

    // Anneau « Aperçu examens »
    $moyenne = max(0, min(100, (float) ($examens['moyenne'] ?? 0)));
    $traitMoyenne = $moyenne / 100 * $circ;

    // Raccourcis (remplace les « # » par tes routes)
    $raccourcis = [
        ['label' => 'Ajouter Étudiant',   'icone' => 'user-plus',             'href' => '#'],
        ['label' => 'Prendre Présence',   'icone' => 'clipboard-check',       'href' => '#'],
        ['label' => 'Créer Facture',      'icone' => 'receipt',               'href' => '#'],
        ['label' => 'Télécharger Notes',  'icone' => 'download',              'href' => '#'],
        ['label' => 'Générer Rapport',    'icone' => 'chart-no-axes-combined','href' => '#'],
        ['label' => 'Calendrier',         'icone' => 'calendar-days',         'href' => '#'],
    ];
@endphp
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Tableau de bord — SHARKLINK</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Arimo:wght@400;700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --bleu-nuit: #0b1f3a;
            --gris: #758398;
            --fond: #f4fbff;
            --trait: #e7eef4;
            --trait-fort: #d1d5db;
            --turquoise: #087e96;
            --cyan: #22d3ee;
            --vert: #16845b;
            --vert-fond: #eaf8f0;
            --rouge: #ef4444;
            --rouge-fond: #fff0f0;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            background: var(--fond);
            color: var(--bleu-nuit);
            font-family: 'Inter', Arial, sans-serif;
            font-size: 12px;
            min-height: 100vh;
        }

        img { display: block; }
        a { color: inherit; text-decoration: none; }
        button { font: inherit; color: inherit; background: none; border: 0; cursor: pointer; }
        .arimo { font-family: 'Arimo', Arial, sans-serif; }

        /* ---------- En-tête ---------- */
        .entete {
            height: 72px;
            padding: 0 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            background: #fff;
            border-bottom: 1px solid var(--trait-fort);
        }

        .page-annee { display: flex; align-items: center; gap: 20px; }
        .page-annee .page { font-size: 16px; white-space: nowrap; }

        .annee {
            padding: 7px 10px;
            background: var(--fond);
            border-radius: 6px;
            font-size: 11px;
            color: var(--gris);
            white-space: nowrap;
        }

        .recherche {
            width: 355px;
            height: 40px;
            padding: 0 13px;
            display: flex;
            align-items: center;
            gap: 10px;
            background: var(--fond);
            border-radius: 8px;
        }

        .recherche img { width: 17px; height: 17px; }

        .recherche input {
            flex: 1;
            min-width: 0;
            background: none;
            border: 0;
            outline: 0;
            font: inherit;
            font-size: 12px;
            color: var(--bleu-nuit);
        }

        .recherche input::placeholder { color: var(--gris); }
        .recherche kbd { font: inherit; font-size: 11px; color: var(--gris); white-space: nowrap; }

        .compte-zone { display: flex; align-items: center; gap: 16px; }

        .notif { position: relative; width: 34px; height: 36px; display: flex; align-items: center; }
        .notif > img { width: 21px; height: 21px; }

        .notif .nombre {
            position: absolute;
            top: 0;
            left: 13px;
            width: 17px;
            height: 17px;
            border-radius: 100px;
            background: var(--cyan);
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Arimo', Arial, sans-serif;
            font-weight: 700;
            font-size: 10px;
        }

        .compte { display: flex; align-items: center; gap: 10px; }
        .compte .portrait { width: 36px; height: 36px; border-radius: 100px; object-fit: cover; }
        .compte .nom { font-weight: 600; font-size: 12px; white-space: nowrap; }
        .compte .role { margin-top: 4px; font-size: 10px; color: var(--gris); }
        .chevron { width: 14px; height: 14px; }

        /* ---------- Contenu ---------- */
        .contenu {
            padding: 24px;
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .bienvenue { display: flex; align-items: center; gap: 16px; }
        .bienvenue .intro { flex: 1; min-width: 0; display: flex; flex-direction: column; gap: 8px; }
        .bienvenue h1 { font-size: 26px; font-weight: 700; }
        .bienvenue p { font-size: 12px; line-height: 1.6; color: var(--gris); }

        .exporter {
            padding: 12px 14px;
            display: flex;
            align-items: center;
            gap: 8px;
            background: #fff;
            border: 1px solid var(--trait);
            border-radius: 8px;
            font-size: 12px;
            white-space: nowrap;
        }

        .exporter img { width: 16px; height: 16px; }

        /* Cartes */
        .carte {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(11, 31, 58, 0.03);
            padding: 24px;
            display: flex;
            flex-direction: column;
            gap: 20px;
            min-width: 0;
        }

        .carte-entete { display: flex; align-items: center; justify-content: space-between; gap: 12px; }
        .carte-entete .titre { font-size: 16px; }
        .carte-entete .contexte { margin-top: 5px; font-size: 11px; color: var(--gris); }
        .carte-entete .plus { width: 18px; height: 18px; }

        .lien-carte {
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-weight: 600;
            font-size: 11px;
            color: var(--turquoise);
        }

        .lien-carte img { width: 15px; height: 15px; }

        /* Statistiques */
        .statistiques { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 20px; }

        .indicateur { padding: 20px; gap: 13px; height: 146px; }
        .indicateur .ligne { display: flex; align-items: center; justify-content: space-between; }
        .indicateur .libelle { font-size: 12px; color: var(--gris); }
        .indicateur .ligne img { width: 18px; height: 18px; }

        .indicateur .valeur {
            font-family: 'Arimo', Arial, sans-serif;
            font-weight: 700;
            font-size: 31px;
            white-space: nowrap;
        }

        .indicateur .valeur small { font-family: 'Inter', Arial, sans-serif; font-weight: 500; font-size: 12px; }

        .evolution { display: flex; flex-wrap: wrap; align-items: center; gap: 6px; font-size: 10px; color: var(--gris); }
        .evolution .hausse { font-family: 'Arimo', Arial, sans-serif; font-weight: 700; font-size: 11px; color: var(--vert); }
        .evolution .baisse { font-family: 'Arimo', Arial, sans-serif; font-weight: 700; font-size: 11px; color: var(--rouge); }

        /* Suivi */
        .suivi { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 20px; }
        .suivi .carte { min-height: 426px; }

        .donut-zone { display: flex; justify-content: center; }
        .donut { position: relative; width: 172px; height: 172px; }
        .donut svg { width: 172px; height: 172px; display: block; }

        .donut-centre {
            position: absolute;
            inset: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }

        .donut-centre strong { font-family: 'Arimo', Arial, sans-serif; font-weight: 700; font-size: 36px; }
        .donut-centre span { font-size: 11px; color: var(--gris); }

        .tableau-statut .colonnes {
            display: flex;
            justify-content: space-between;
            padding-bottom: 7px;
            font-size: 10px;
            color: var(--gris);
            text-transform: uppercase;
        }

        .tableau-statut .statut {
            height: 29px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-top: 1px solid var(--trait);
            font-size: 11px;
        }

        .tableau-statut .legende { display: flex; align-items: center; gap: 8px; }
        .point { display: inline-block; width: 7px; height: 7px; border-radius: 100px; }
        .tableau-statut .part { font-family: 'Arimo', Arial, sans-serif; font-weight: 700; font-size: 12px; }

        /* Présence du jour */
        .presence {
            height: 53px;
            display: flex;
            align-items: center;
            gap: 9px;
            border-bottom: 1px solid var(--trait);
        }

        .presence .portrait { width: 32px; height: 32px; border-radius: 100px; object-fit: cover; flex-shrink: 0; }
        .presence .etudiant { flex: 1; min-width: 0; display: flex; flex-direction: column; gap: 4px; }
        .presence .etudiant .nom { font-size: 12px; }
        .presence .etudiant .dept { font-size: 10px; color: var(--gris); }
        .presence .heure { font-family: 'Arimo', Arial, sans-serif; font-size: 11px; color: var(--gris); }

        .badge {
            padding: 5px 9px;
            border-radius: 100px;
            font-weight: 600;
            font-size: 10px;
            white-space: nowrap;
        }

        .badge-ok { background: var(--vert-fond); color: var(--vert); }
        .badge-ko { background: var(--rouge-fond); color: var(--rouge); }

        /* Examens */
        .legende-centre { display: flex; align-items: center; justify-content: center; gap: 7px; font-size: 11px; color: var(--gris); }
        .legende-centre .point { width: 6px; height: 6px; background: var(--cyan); }

        .meilleure {
            padding: 14px;
            display: flex;
            align-items: center;
            gap: 12px;
            background: var(--fond);
            border-radius: 8px;
        }

        .meilleure .recompense {
            width: 36px;
            height: 36px;
            flex-shrink: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #e7fafd;
            border-radius: 8px;
        }

        .meilleure .recompense img { width: 19px; height: 19px; }
        .meilleure .laureat { display: flex; flex-direction: column; gap: 6px; font-size: 11px; }
        .meilleure .laureat strong { font-weight: 600; }
        .meilleure .sgpa { color: var(--turquoise); }
        .meilleure .sgpa b { font-family: 'Arimo', Arial, sans-serif; }

        /* Résultats + raccourcis */
        .bas { display: grid; grid-template-columns: minmax(0, 800fr) minmax(0, 472fr); gap: 20px; }
        .bas .carte { min-height: 388px; }

        .tableau-resultats { overflow-x: auto; }
        .tableau-resultats .rangee { min-width: 560px; }

        .rangee {
            display: grid;
            grid-template-columns: 205px 132px minmax(0, 1fr) 112px;
            gap: 12px;
            align-items: center;
            padding: 0 12px;
            font-size: 11px;
        }

        .rangee.tete {
            height: 32px;
            background: var(--fond);
            border-radius: 6px;
            font-size: 10px;
            color: var(--gris);
        }

        .rangee.donnee { height: 45px; border-bottom: 1px solid var(--trait); }
        .rangee .nom { font-size: 12px; }
        .rangee .matricule { margin-top: 4px; font-family: 'Arimo', Arial, sans-serif; font-size: 9px; color: var(--gris); }
        .rangee .note-statut { display: flex; align-items: center; gap: 8px; }
        .rangee .note { font-family: 'Arimo', Arial, sans-serif; font-weight: 700; font-size: 11px; }

        .raccourcis { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 12px; }

        .raccourci {
            height: 82px;
            padding: 10px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 9px;
            background: var(--fond);
            border-radius: 8px;
            font-size: 11px;
            text-align: center;
            transition: background .15s ease;
        }

        .raccourci:hover { background: #e7fafd; }
        .raccourci img { width: 22px; height: 22px; }

        /* Pied de page */
        .pied { display: flex; justify-content: space-between; gap: 12px; font-size: 11px; color: var(--gris); }
        .pied .sync { color: var(--vert); }

        /* ---------- Adaptation ---------- */
        @media (max-width: 1180px) {
            .statistiques { grid-template-columns: repeat(2, minmax(0, 1fr)); }
            .suivi, .bas { grid-template-columns: minmax(0, 1fr); }
            .recherche { width: 260px; }
        }

        @media (max-width: 720px) {
            .entete { padding: 0 16px; }
            .recherche, .compte .identite { display: none; }
            .page-annee { gap: 10px; }
            .contenu { padding: 16px; }
            .bienvenue { flex-direction: column; align-items: flex-start; }
            .bienvenue h1 { font-size: 22px; }
            .statistiques { grid-template-columns: minmax(0, 1fr); }
            .pied { flex-direction: column; }
        }
    </style>
</head>
<body>

    {{-- ===================== En-tête ===================== --}}
    <header class="entete">
        <div class="page-annee">
            <p class="page">{{ $page ?? 'Tableau de bord' }}</p>
            <div class="annee">Année <span class="arimo">{{ $annee }}</span></div>
        </div>

        <div class="recherche" role="search">
            <img src="{{ asset('images/search.svg') }}" alt="">
            <input type="search" placeholder="Rechercher un étudiant, un cours…" aria-label="Recherche globale">
            <kbd>⌘ K</kbd>
        </div>

        <div class="compte-zone">
            <div class="notif">
                <img src="{{ asset('images/bell.svg') }}" alt="Notifications">
                @if (($notifications ?? 0) > 0)
                    <span class="nombre">{{ $notifications }}</span>
                @endif
            </div>

            <div class="compte">
                <img class="portrait" src="{{ $avatar }}" alt="">
                <div class="identite">
                    <p class="nom">{{ $user->name }}</p>
                    <p class="role">{{ $roleLabel ?? 'Administrateur' }}</p>
                </div>
            </div>

            <img class="chevron" src="{{ asset('images/chevron-down.svg') }}" alt="">
        </div>
    </header>

    <main class="contenu">

        {{-- ===================== Bienvenue ===================== --}}
        <section class="bienvenue">
            <div class="intro">
                <h1>Votre campus, en un coup d’œil.</h1>
                <p>Bonjour {{ $prenom }}, voici l’essentiel de votre université aujourd’hui.</p>
            </div>

            {{-- TODO : remplacer « # » par la route d'export du rapport --}}
            <a href="#" class="exporter">
                <img src="{{ asset('images/download.svg') }}" alt="">
                Exporter le rapport
            </a>
        </section>

        {{-- ===================== Indicateurs clés ===================== --}}
        <section class="statistiques">
            @foreach ($stats as $s)
                <article class="carte indicateur">
                    <div class="ligne">
                        <span class="libelle">{{ $s['label'] }}</span>
                        <img src="{{ asset('images/' . $s['icone'] . '.svg') }}" alt="">
                    </div>

                    <p class="valeur">
                        {{ is_numeric($s['valeur']) ? number_format($s['valeur'], 0, ',', "\u{00A0}") : $s['valeur'] }}
                        @if (!empty($s['unite']))<small>{{ $s['unite'] }}</small>@endif
                    </p>

                    <div class="evolution">
                        @if (!empty($s['evolution']))
                            <span class="{{ str_starts_with($s['evolution'], '↓') ? 'baisse' : 'hausse' }}">{{ $s['evolution'] }}</span>
                        @endif
                        <span>{{ $s['periode'] }}</span>
                    </div>
                </article>
            @endforeach
        </section>

        {{-- ===================== Suivi de l'université ===================== --}}
        <section class="suivi">

            {{-- Collecte des frais --}}
            <article class="carte">
                <div class="carte-entete">
                    <div>
                        <p class="titre">Collecte des Frais</p>
                        <p class="contexte">Suivi des paiements · Année en cours</p>
                    </div>
                    <img class="plus" src="{{ asset('images/ellipsis.svg') }}" alt="">
                </div>

                <div class="donut-zone">
                    <div class="donut">
                        <svg viewBox="0 0 172 172" role="img" aria-label="Répartition des frais">
                            <g transform="rotate(-90 86 86)" fill="none" stroke-width="16">
                                @foreach ($arcsFrais as $arc)
                                    @if ($arc['pct'] > 0)
                                        <circle cx="86" cy="86" r="{{ $rayon }}"
                                                stroke="{{ $arc['couleur'] }}"
                                                stroke-dasharray="{{ round($arc['trait'], 2) }} {{ round($circ, 2) }}"
                                                stroke-dashoffset="{{ round(-$arc['decalage'], 2) }}"/>
                                    @endif
                                @endforeach
                            </g>
                        </svg>
                        <div class="donut-centre">
                            <strong>{{ rtrim(rtrim(number_format($frais['collecte'] ?? 0, 1, '.', ''), '0'), '.') }}%</strong>
                            <span>Collecté</span>
                        </div>
                    </div>
                </div>

                <div class="tableau-statut">
                    <div class="colonnes"><span>Statut</span><span>Part</span></div>
                    @foreach ($arcsFrais as $arc)
                        <div class="statut">
                            <span class="legende">
                                <i class="point" style="background: {{ $arc['couleur'] }}"></i>
                                {{ $libellesFrais[$arc['cle']] }}
                            </span>
                            <span class="part">{{ rtrim(rtrim(number_format($arc['pct'], 1, '.', ''), '0'), '.') }}%</span>
                        </div>
                    @endforeach
                </div>
            </article>

            {{-- Présence du jour --}}
            <article class="carte">
                <div class="carte-entete">
                    <div>
                        <p class="titre">Présence du jour</p>
                        <p class="contexte">Pointage des étudiants · Ce matin</p>
                    </div>
                    <img class="plus" src="{{ asset('images/ellipsis.svg') }}" alt="">
                </div>

                <div>
                    @foreach ($presences as $p)
                        <div class="presence">
                            <img class="portrait" src="{{ $p['photo'] ?? asset('images/portrait-defaut.png') }}" alt="">
                            <div class="etudiant">
                                <span class="nom">{{ $p['nom'] }}</span>
                                <span class="dept">{{ $p['departement'] }}</span>
                            </div>
                            <span class="heure">{{ $p['heure'] }}</span>
                            <span class="badge {{ $p['present'] ? 'badge-ok' : 'badge-ko' }}">
                                {{ $p['present'] ? 'Présent' : 'Absent' }}
                            </span>
                        </div>
                    @endforeach
                </div>

                {{-- TODO : route de la liste complète des présences --}}
                <a href="#" class="lien-carte">
                    <span>Voir toutes les présences</span>
                    <img src="{{ asset('images/arrow-right.svg') }}" alt="">
                </a>
            </article>

            {{-- Aperçu examens --}}
            <article class="carte">
                <div class="carte-entete">
                    <div>
                        <p class="titre">Aperçu Examens</p>
                        <p class="contexte">Performance académique · Semestre en cours</p>
                    </div>
                    <img class="plus" src="{{ asset('images/ellipsis.svg') }}" alt="">
                </div>

                <div class="donut-zone">
                    <div class="donut">
                        <svg viewBox="0 0 172 172" role="img" aria-label="Moyenne des examens">
                            <g transform="rotate(-90 86 86)" fill="none" stroke-width="16">
                                <circle cx="86" cy="86" r="{{ $rayon }}" stroke="#eaf1f7"/>
                                <circle cx="86" cy="86" r="{{ $rayon }}" stroke="{{ '#22d3ee' }}"
                                        stroke-dasharray="{{ round($traitMoyenne, 2) }} {{ round($circ, 2) }}"/>
                            </g>
                        </svg>
                        <div class="donut-centre">
                            <strong>{{ rtrim(rtrim(number_format($moyenne, 1, '.', ''), '0'), '.') }}%</strong>
                            <span>Moyenne</span>
                        </div>
                    </div>
                </div>

                <div class="legende-centre">
                    <i class="point"></i>
                    <span>Moyenne des résultats</span>
                </div>

                <div class="meilleure">
                    <div class="recompense"><img src="{{ asset('images/trophy.svg') }}" alt=""></div>
                    <div class="laureat">
                        <strong>Meilleur : {{ $examens['meilleur'] }}</strong>
                        <span class="sgpa">SGPA <b>{{ number_format($examens['sgpa'], 2, '.', '') }}</b></span>
                    </div>
                </div>
            </article>
        </section>

        {{-- ===================== Résultats récents + actions rapides ===================== --}}
        <section class="bas">

            <article class="carte">
                <div class="carte-entete">
                    <div>
                        <p class="titre">Résultats récents</p>
                        <p class="contexte">Les derniers examens publiés</p>
                    </div>
                    <img class="plus" src="{{ asset('images/ellipsis.svg') }}" alt="">
                </div>

                <div class="tableau-resultats">
                    <div class="rangee tete">
                        <span>Étudiant</span>
                        <span>Programme</span>
                        <span>Examen</span>
                        <span>Résultat</span>
                    </div>

                    @foreach ($resultats as $r)
                        <div class="rangee donnee">
                            <div>
                                <p class="nom">{{ $r['nom'] }}</p>
                                <p class="matricule">{{ $r['matricule'] }}</p>
                            </div>
                            <span>{{ $r['programme'] }}</span>
                            <span>{{ $r['examen'] }}</span>
                            <div class="note-statut">
                                <span class="note">{{ number_format($r['note'], 1, '.', '') }}</span>
                                <span class="badge {{ $r['reussi'] ? 'badge-ok' : 'badge-ko' }}">
                                    {{ $r['reussi'] ? 'Réussi' : 'Échec' }}
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- TODO : route de la liste complète des résultats --}}
                <a href="#" class="lien-carte">
                    <span>Voir tous les résultats</span>
                    <img src="{{ asset('images/arrow-right.svg') }}" alt="">
                </a>
            </article>

            <article class="carte">
                <div class="carte-entete">
                    <div>
                        <p class="titre">Actions Rapides</p>
                        <p class="contexte">Vos outils du quotidien, à portée de main</p>
                    </div>
                    <img class="plus" src="{{ asset('images/ellipsis.svg') }}" alt="">
                </div>

                <div class="raccourcis">
                    @foreach ($raccourcis as $action)
                        <a href="{{ $action['href'] }}" class="raccourci">
                            <img src="{{ asset('images/' . $action['icone'] . '.svg') }}" alt="">
                            <span>{{ $action['label'] }}</span>
                        </a>
                    @endforeach
                </div>
            </article>
        </section>

        {{-- ===================== Pied de page ===================== --}}
        <footer class="pied">
            <span>SHARKLINK · L’université, simplement.</span>
            <span class="sync">Toutes les données sont à jour</span>
        </footer>
    </main>

</body>
</html>