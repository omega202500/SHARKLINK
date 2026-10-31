<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Lettre d'Admission - {{ $application->last_name }}</title>
    <style>
        @page { margin: 25mm 20mm; size: A4; }
        body { font-family: 'Times New Roman', Times, serif; font-size: 12px; line-height: 1.5; color: #000; }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #0b1f3a; padding-bottom: 15px; }
        .logo { font-size: 24px; font-weight: bold; color: #0b1f3a; margin-bottom: 5px; }
        .establishment { font-size: 14px; font-weight: bold; }
        .content { margin-top: 30px; }
        .recipient { margin-bottom: 20px; }
        .subject { font-weight: bold; text-decoration: underline; margin: 20px 0; }
        .body-text { text-align: justify; margin-bottom: 15px; }
        .signature { margin-top: 50px; text-align: right; }
        .signature-line { border-top: 1px solid #000; width: 200px; margin: 40px auto 10px auto; padding-top: 5px; }
        .footer { position: fixed; bottom: 0; left: 0; right: 0; font-size: 9px; color: #666; text-align: center; border-top: 1px solid #ddd; padding-top: 10px; }
    </style>
</head>
<body>
    <div class="header">
        <div class="logo">SHARKLINK</div>
        <div class="establishment">{{ $application->program->name ?? 'Université SHARKLINK' }}</div>
        <div style="font-size: 10px; margin-top: 5px;">Excellence académique & Innovation</div>
    </div>

    <div class="content">
        <div class="recipient">
            <strong>{{ strtoupper($application->first_name . ' ' . $application->last_name) }}</strong><br>
            {{ $application->address ?? 'Adresse non renseignée' }}<br>
            {{ $application->email }}<br>
            {{ $application->phone }}
        </div>

        <div style="text-align: right; margin-bottom: 20px;">
            Fait à Douala, le {{ $generated_at }}
        </div>

        <div class="subject">OBJET : LETTRE D'ADMISSION</div>

        <div class="body-text">
            Madame, Monsieur,
        </div>

        <div class="body-text">
            Nous avons le plaisir de vous informer que votre candidature pour l'année académique <strong>2026-2027</strong> 
            a été <strong>acceptée</strong> par notre établissement.
        </div>

        <div class="body-text">
            Vous êtes admis(e) en <strong>{{ $application->level->name ?? 'Niveau non défini' }}</strong> 
            au sein du programme <strong>{{ $application->program->name ?? 'Programme non défini' }}</strong>.
        </div>

        <div class="body-text">
            Pour finaliser votre inscription, nous vous prions de bien vouloir :
        </div>

        <div class="body-text" style="padding-left: 20px;">
            1. Confirmer votre acceptation dans un délai de 15 jours<br>
            2. Régler les frais d'inscription<br>
            3. Présenter les originaux de vos pièces justificatives<br>
            4. Participer à la journée d'accueil prévue le 15 octobre 2026
        </div>

        <div class="body-text">
            Nous vous souhaitons la bienvenue au sein de notre communauté académique et restons à votre disposition 
            pour toute information complémentaire.
        </div>

        <div class="body-text">
            Veuillez agréer, Madame, Monsieur, l'expression de nos salutations distinguées.
        </div>

        <div class="signature">
            <div style="font-weight: bold;">Le Responsable des Admissions</div>
            <div class="signature-line">
                Signature et cachet
            </div>
        </div>
    </div>

    <div class="footer">
        SHARKLINK - BP: 2701 / 24157 Douala - Tél: (+237) 33 40 64 15 - Email: admissions@sharklink.cm<br>
        République du Cameroun - Paix - Travail - Patrie
    </div>
</body>
</html>