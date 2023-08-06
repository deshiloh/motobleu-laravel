<html lang="fr">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-15">
    <style>
        @page {
            margin: 0.25cm 0.25cm;
            size: 279mm 205mm;
        }

        /** Define now the real margins of every page in the PDF **/
        body {
            margin-top: 2cm;
            margin-bottom: 2cm;
            color: #293275;
            font-family: 'Roboto', sans-serif;
            padding-top: 2.1cm;
            padding-bottom: 2.5cm;
        }

        .body {
            margin-left: 2cm;
            margin-right: 2cm;
            font-size: 0.9em;
        }

        /** Define the header rules **/
        header {
            position: fixed;
            top: 0cm;
            left: 0cm;
            right: 0cm;
            height: 3.0cm;

            /** Extra personal styles **/
            background-color: #293275;
            color: white;
            text-align: center;
            /*background-color: #293275;*/
            background-repeat: no-repeat;
            background-position: 0 -30px;

        }

        header table {
            z-index: 1000;
        }

        .header-bg {
            position: absolute;
            top: -1cm;
            left: 0;
            right: 0;
            z-index: 1;
        }

        /** Define the footer rules **/
        footer {
            position: fixed;
            bottom: 0cm;
            left: 0cm;
            right: 0cm;
            height: 2.5cm;

            /** Extra personal styles **/
            background-color: #293275;
            /*background-image: url(FOOTER); */
            background-repeat: no-repeat;
            background-position: bottom center;
            color: white;
            text-align: center;
            font-size: 0.6em;
            padding-top: 40px;
        }

        table {
            width: 100%;
            margin: 0;
            padding: 0;
        }

        th {
            background-color: #293275;
        }

        .header-table {
            text-align: center;
        }

        .header-table h4 {
            margin-bottom: 0;
            margin-top: 0;
            font-size: 0.9em;
        }

        .listing {
            font-size: 0.6em;
        }

        .listing th {
            padding: 8px 0;
            color: white;
        }

        .listing tr {
            background-color: #e0e0e0;
        }

        .listing td {
            padding: 10px 0;
            border-bottom: 1px solid black;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .text-white {
            color: white;
        }

        .mt {
            margin-top: 20px;
        }

        .end td {
            padding: 0;
        }

        .end-content td {
            padding: 5px 15px;
        }

        .end-last td {
            background-color: #494949;
            color: white;
        }

        #footer {
            height: 100%;
        }

        #footer td {
            vertical-align: middle;
            text-align: center;
        }
    </style>
    <title></title>
</head>
<body>
<header>
    <table>
        <tr>
            <td>
                <img src="{{ storage_path('app/public/logo-pdf.png') }}" alt="Motobleu Paris" width="300"
                     style="padding-left: 30px; padding-top: 15px;">
            </td>
        </tr>
    </table>
</header>
<footer>
    <table id="footer">
        <tr>
            <td>
                MOTOBLEU<br>
                26 - 28 RUE MARIUS AUFAN 92300 LEVALLOIS PERRET<br>
                SIRET: 82472195500014 - TVA intracommunautaire: FR69824721955<br>
                Tél:+33647938617 - contact@motobleu-paris.com
            </td>
        </tr>
    </table>
</footer>
<table class="header-table">
    <tr>
        <td>
            @if($entreprise)
                Entreprise sélectionnée : {{ $entreprise->nom }}
            @endif
            <h4>Factures pour la période de {{ $debut->month }}/{{ $debut->year }} à {{ $fin->month }}/{{ $fin->year }}</h4>
        </td>
    </tr>
</table>
<table cellspacing="0" border="0" class="mt listing">
    <thead>
    <tr>
        <th style="padding-left: 10px; text-align: center;">Référence</th>
        <th class="text-center">Date création</th>
        <th class="text-center">Acquittée</th>
        <th class="text-center">Entreprise</th>
        <th class="text-right" style="padding-right: 15px;">Montant</th>
    </tr>
    </thead>
    <tbody>
    @php
        $fmt = new NumberFormatter('fr_FR', NumberFormatter::CURRENCY);
        $totalTTC = 0;
    @endphp
    @foreach($factures as $facture)
        @php
            $totalTTC = $totalTTC + $facture->montant_ttc;
        @endphp
        <tr>
            <td style="padding-left: 10px; text-align: center;">
                {{ $facture->reference }}
            </td>
            <td style="text-align: center">
                {{ $facture->created_at->format('d/m/Y') }}
            </td>
            <td style="text-align: center">
                @if($facture->is_acquitte)
                    Oui
                @else
                    Non
                @endif
            </td>
            <td style="text-align: center">
                {{ $facture->reservations->first()->entreprise->nom ?? "Non disponible"}}
            </td>
            <td style="text-align: right; padding-right: 17px">
                {{ $fmt->formatCurrency($facture->montant_ttc, 'EUR') }}
            </td>
        </tr>
    @endforeach
    <tr class="end">
        <td colspan="4" style="background-color: white; border-top: 10px solid #293275; border-bottom: none;"></td>
        <td colspan="1" class="end-content" style="border-top: 10px solid #293275;">
            <table>
                <tr>
                    <td style="text-align: right;">Sous total</td>
                    @php
                        $prixHT = $totalTTC / 1.1;
                    @endphp
                    <td style="text-align: right">{{ number_format($prixHT, 2, '.', ' ') }} €</td>
                </tr>
                <tr>
                    <td style="text-align: right;">TVA (10%)</td>
                    @php
                        $prixTVA =  $totalTTC - $prixHT
                    @endphp
                    <td style="text-align: right">{{ number_format($prixTVA, 2, '.', ' ') }} €</td>
                </tr>
                <tr class="end-last">
                    <td style="text-align: right;">Total à payer</td>
                    <td style="text-align: right">{{ number_format($totalTTC, 2, '.', ' ') }} €</td>
                </tr>
            </table>
        </td>
    </tr>
</table>
</body>
</html>
