<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Relevé de Notes - {{ $student['full_name'] }}</title>
    <style>
        @page {
            margin: 15mm 12mm 15mm 12mm;
            size: A4 portrait;
        }
        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 11px;
            color: #000;
            line-height: 1.3;
        }
        .header {
            display: table;
            width: 100%;
            border-bottom: 2px solid #000;
            padding-bottom: 8px;
            margin-bottom: 15px;
        }
        .header-col {
            display: table-cell;
            vertical-align: top;
            width: 33%;
        }
        .header-col.center {
            text-align: center;
        }
        .header-col.right {
            text-align: right;
        }
        .header-title {
            font-weight: bold;
            font-size: 10px;
            text-transform: uppercase;
            margin: 2px 0;
        }
        .header-subtitle {
            font-size: 9px;
            margin: 1px 0;
        }
        .logo-container {
            text-align: center;
            padding: 5px 0;
        }
        .logo-container img {
            max-height: 70px;
            max-width: 70px;
        }
        .establishment-name {
            font-weight: bold;
            font-size: 12px;
            text-transform: uppercase;
            margin-top: 5px;
        }
        .contact-info {
            font-size: 8px;
            margin-top: 3px;
        }

        .document-title {
            text-align: center;
            font-size: 16px;
            font-weight: bold;
            text-transform: uppercase;
            margin: 15px 0 5px 0;
            letter-spacing: 1px;
        }
        .document-subtitle {
            text-align: center;
            font-size: 11px;
            font-style: italic;
            margin-bottom: 15px;
        }
        .academic-year {
            text-align: right;
            font-size: 11px;
            margin-bottom: 10px;
        }

        .student-info {
            display: table;
            width: 100%;
            margin-bottom: 15px;
            font-size: 10px;
        }
        .info-row {
            display: table-row;
        }
        .info-cell {
            display: table-cell;
            padding: 3px 0;
            vertical-align: top;
        }
        .info-cell.left { width: 50%; }
        .info-cell.right { width: 50%; }
        .info-label {
            font-weight: bold;
            display: inline-block;
            min-width: 130px;
        }
        .info-label-en {
            font-style: italic;
            color: #333;
            font-size: 9px;
        }
        .info-value {
            font-weight: bold;
            text-transform: uppercase;
        }

        table.grades-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
            font-size: 10px;
        }
        table.grades-table th {
            background-color: #f0f0f0;
            border: 1px solid #000;
            padding: 4px 3px;
            text-align: center;
            font-weight: bold;
            font-size: 9px;
        }
        table.grades-table td {
            border: 1px solid #000;
            padding: 3px;
            text-align: center;
            vertical-align: middle;
        }
        table.grades-table td.title-cell {
            text-align: left;
            font-weight: bold;
        }
        table.grades-table td.code-cell {
            text-align: center;
            font-weight: bold;
        }
        .ue-row td {
            background-color: #fafafa;
            font-weight: bold;
        }

        .synthesis-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            font-size: 10px;
        }
        .synthesis-table th,
        .synthesis-table td {
            border: 1px solid #000;
            padding: 5px;
            text-align: center;
        }
        .synthesis-table th {
            background-color: #f0f0f0;
            font-weight: bold;
            font-size: 9px;
        }
        .synthesis-table .label-cell {
            text-align: left;
            font-weight: bold;
        }

        .legend {
            font-size: 9px;
            font-style: italic;
            margin: 15px 0 20px 0;
            text-align: center;
        }

        .signatures {
            display: table;
            width: 100%;
            margin-top: 30px;
            font-size: 10px;
        }
        .signature-col {
            display: table-cell;
            width: 33%;
            text-align: center;
            vertical-align: top;
        }
        .signature-title {
            font-weight: bold;
            margin-bottom: 3px;
        }
        .signature-title-en {
            font-style: italic;
            font-size: 9px;
        }
        .signature-space {
            height: 60px;
        }
        .signature-date {
            margin-top: 5px;
            font-size: 10px;
        }

        .footer-note {
            font-size: 8px;
            font-style: italic;
            margin-top: 20px;
            text-align: center;
            border-top: 1px solid #ccc;
            padding-top: 5px;
        }

        .stamp-area {
            position: relative;
            height: 80px;
        }
    </style>
</head>
<body>

    {{-- ===== EN-TÊTE OFFICIEL ===== --}}
    <div class="header">
        <div class="header-col">
            <div class="header-title">République du Cameroun</div>
            <div class="header-subtitle">Paix – Travail – Patrie</div>
            <div class="header-subtitle" style="margin-top: 8px;">**************</div>
            <div class="header-title">Ministère de l'Enseignement Supérieur</div>
            <div class="header-subtitle">**************</div>
            <div class="header-title">{{ $establishment->name }}</div>
            <div class="header-subtitle">**************</div>
            <div class="header-title">{{ $student['faculty'] }}</div>
        </div>

        <div class="header-col center">
            <div class="logo-container">
                @if(file_exists(public_path('images/' . ($establishment->logo ?? 'shark-mark.svg'))))
                    <img src="{{ public_path('images/' . ($establishment->logo ?? 'shark-mark.svg')) }}" alt="Logo">
                @else
                    <div style="width: 70px; height: 70px; border: 2px solid #000; border-radius: 50%; margin: 0 auto; display: flex; align-items: center; justify-content: center; font-weight: bold; font-size: 10px;">
                        LOGO
                    </div>
                @endif
            </div>
        </div>

        <div class="header-col right">
            <div class="header-title">Republic of Cameroon</div>
            <div class="header-subtitle">Peace – Work – Fatherland</div>
            <div class="header-subtitle" style="margin-top: 8px;">**************</div>
            <div class="header-title">Ministry of Higher Education</div>
            <div class="header-subtitle">**************</div>
            <div class="header-title">{{ $establishment->name_en ?? $establishment->name }}</div>
            <div class="header-subtitle">**************</div>
            <div class="header-title">{{ $student['faculty'] }}</div>
        </div>
    </div>

    {{-- ===== TITRE DU DOCUMENT ===== --}}
    <div class="document-title">Relevé de Notes / Transcript</div>
    <div class="academic-year"><strong>Année Académique : {{ $academic_year }}</strong></div>

    {{-- ===== INFORMATIONS ÉTUDIANT ===== --}}
    <div class="student-info">
        <div class="info-row">
            <div class="info-cell left">
                <span class="info-label">Nom (s) & Prénom (s) :<br><span class="info-label-en">Name & Surname:</span></span>
                <span class="info-value">{{ $student['full_name'] }}</span>
            </div>
            <div class="info-cell right">
                <span class="info-label">N° Matricule :<br><span class="info-label-en">Registration N°:</span></span>
                <span class="info-value">{{ $student['matricule'] }}</span>
            </div>
        </div>
        <div class="info-row">
            <div class="info-cell left">
                <span class="info-label">Né(e) le :<br><span class="info-label-en">Born on:</span></span>
                <span class="info-value">{{ $student['birth_date'] }}</span>
                <span style="margin-left: 20px;" class="info-label">À / In: <span class="info-value">{{ $student['birth_place'] }}</span></span>
            </div>
            <div class="info-cell right">
                <span class="info-label">Sexe :<br><span class="info-label-en">Sex:</span></span>
                <span class="info-value">{{ $student['gender'] }}</span>
            </div>
        </div>
        <div class="info-row">
            <div class="info-cell left">
                <span class="info-label">Cycle :<br><span class="info-label-en">Level:</span></span>
                <span class="info-value">{{ $student['level'] }}</span>
            </div>
            <div class="info-cell right">
                <span class="info-label">Semestre :<br><span class="info-label-en">Semester:</span></span>
                <span class="info-value">{{ $semester }}</span>
            </div>
        </div>
        <div class="info-row">
            <div class="info-cell left">
                <span class="info-label">Spécialité :<br><span class="info-label-en">Speciality:</span></span>
                <span class="info-value">{{ $student['program'] }}</span>
            </div>
            <div class="info-cell right">
                <span class="info-label">Filière :<br><span class="info-label-en">Discipline:</span></span>
                <span class="info-value">{{ $student['department'] }}</span>
            </div>
        </div>
    </div>

    {{-- ===== TABLEAU DES NOTES ===== --}}
    <table class="grades-table">
        <thead>
            <tr>
                <th style="width: 10%;">Code</th>
                <th style="width: 35%;">Intitulé / Title</th>
                <th style="width: 10%;">Note/20</th>
                <th style="width: 10%;">Note/5</th>
                <th style="width: 10%;">Crédit</th>
                <th style="width: 10%;">Grade</th>
                <th style="width: 15%;">Décision</th>
            </tr>
        </thead>
        <tbody>
            @php $groupedGrades = $grades->groupBy('course_title'); @endphp
            @foreach($groupedGrades as $courseTitle => $courseGrades)
                @foreach($courseGrades as $index => $grade)
                    <tr>
                        <td class="code-cell">{{ $grade->course_code }}</td>
                        <td class="title-cell">
                            @if($index === 0)
                                <strong>{{ $courseTitle }}</strong>
                            @else
                                {{ $grade->assessment_title }}
                            @endif
                        </td>
                        <td>{{ number_format($grade->score, 2, ',', '') }}</td>
                        <td>{{ number_format($grade->score_5, 2, ',', '') }}</td>
                        <td>{{ $grade->coefficient }}</td>
                        <td><strong>{{ $grade->grade_letter }}</strong></td>
                        <td>
                            @if($grade->decision === 'ADMIS')
                                <strong>VA</strong>
                            @elseif($grade->decision === 'AJOURNÉ')
                                NV
                            @else
                                -
                            @endif
                        </td>
                    </tr>
                @endforeach
            @endforeach
        </tbody>
    </table>

    {{-- ===== SYNTHÈSE ===== --}}
    <table class="synthesis-table">
        <thead>
            <tr>
                <th>Synthèse<br>Acquis</th>
                <th>Unité d'Enseignement</th>
                <th>Élément Constitutif</th>
                <th>Crédit</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><strong>{{ $generalAverage >= 10 ? 'Acquis' : 'Non Acquis' }}</strong></td>
                <td>{{ $grades->count() }}</td>
                <td>{{ $grades->count() }}</td>
                <td><strong>{{ $totalCredits }}/{{ $totalCredits }}</strong></td>
            </tr>
            <tr>
                <td><strong>Moyenne/20</strong><br>{{ number_format($generalAverage, 2, ',', '') }}</td>
                <td><strong>Mention</strong><br>{{ $generalMention }}</td>
                <td><strong>Note/5</strong><br>{{ number_format($generalAverage_5, 3, ',', '') }}</td>
                <td><strong>Grade</strong><br>{{ $grades->first()->grade_letter ?? '-' }}</td>
            </tr>
        </tbody>
    </table>

    {{-- ===== LÉGENDE ===== --}}
    <div class="legend">
        <strong>VA:</strong> Validé/Validated; <strong>VC:</strong> Validé par compensation/Validated by compensation;
        <strong>NV:</strong> Non validé/Not validated; <strong>AVG:</strong> Average
    </div>

    {{-- ===== SIGNATURES ===== --}}
    <div class="signatures">
        <div class="signature-col">
            <div class="signature-title">Le Président du Jury</div>
            <div class="signature-title-en">The Jury's President</div>
            <div class="signature-space" style="border-bottom: 1px dotted #000; margin: 40px 20px 0 20px;"></div>
        </div>
        
        <div class="signature-col">
            <div class="signature-date">{{ $establishment->city ?? 'Douala' }}, le {{ $generated_at }}</div>
            <div class="signature-date" style="font-style: italic; margin-bottom: 10px;">{{ $establishment->city ?? 'Douala' }}, on the {{ $generated_at }}</div>
            
            {{-- Zone pour le CACHET OFFICIEL --}}
            <div style="border: 2px dashed #999; width: 90px; height: 90px; border-radius: 50%; margin: 0 auto; display: flex; align-items: center; justify-content: center; font-size: 9px; color: #999; text-align: center; padding: 5px;">
                Espace réservé<br>au Cachet Officiel
            </div>
        </div>

        <div class="signature-col">
            <div class="signature-title">Le Chef de Département</div>
            <div class="signature-title-en">The Head of Department</div>
            <div class="signature-space" style="border-bottom: 1px dotted #000; margin: 40px 20px 0 20px;"></div>
        </div>
    </div>

    {{-- ===== NOTE DE BAS DE PAGE ===== --}}
    <div class="footer-note">
        <strong>NB :</strong> Il n'est délivré qu'un seul exemplaire de ce document, le titulaire peut en obtenir autant de copies conformes que nécessaire.<br>
        <strong>NOTE:</strong> Only one copy of this document is issued; the holder may obtain as many certified copies as necessary.
    </div>

</body>
</html>